<?php
/**
 * Credo Central integration & Webhooks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FCM_Payments {
    public function __construct() {
        add_shortcode( 'fc_checkout', array( $this, 'checkout_shortcode' ) );
        add_action( 'init', array( $this, 'handle_checkout_submission' ) );
        add_action( 'init', array( $this, 'verify_credo_payment' ) );
    }

    public function get_api_base_url() {
        $mode = get_option( 'fcm_credo_mode', 'test' );
        // Based on typical Credo Central API docs
        return $mode === 'live' ? 'https://api.credocentral.com/v1/' : 'https://api.credocentral.com/v1/'; 
    }

    public function checkout_shortcode() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'You must be logged in to access checkout.', 'conference-manager' ) . '</p>';
        }

        // Get conferences
        $conferences = get_posts( array(
            'post_type' => 'conference',
            'numberposts' => -1,
            'post_status' => 'publish'
        ) );

        if ( empty( $conferences ) ) {
            return '<p>' . __( 'No conferences available for booking.', 'conference-manager' ) . '</p>';
        }

        ob_start();
        ?>
        <form method="post" action="" class="fcm-checkout-form">
            <?php wp_nonce_field( 'fcm_checkout_action', 'fcm_checkout_nonce' ); ?>
            
            <div class="fcm-form-group">
                <label><?php _e( 'Select Conference', 'conference-manager' ); ?> *</label>
                <select name="fcm_conference_id" required>
                    <?php foreach ( $conferences as $conf ) : ?>
                        <option value="<?php echo esc_attr( $conf->ID ); ?>"><?php echo esc_html( $conf->post_title ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="fcm-form-group">
                <label><?php _e( 'Attendance Type', 'conference-manager' ); ?> *</label>
                <select name="fcm_attendance_type" required>
                    <option value="physical"><?php _e( 'Physical', 'conference-manager' ); ?></option>
                    <option value="virtual"><?php _e( 'Virtual', 'conference-manager' ); ?></option>
                </select>
            </div>

            <div class="fcm-form-group">
                <label>
                    <input type="checkbox" name="fcm_needs_accommodation" value="1">
                    <?php _e( 'I need accommodation', 'conference-manager' ); ?>
                </label>
            </div>

            <input type="submit" name="fcm_submit_checkout" value="<?php _e( 'Proceed to Payment', 'conference-manager' ); ?>">
        </form>
        <?php
        return ob_get_clean();
    }

    public function handle_checkout_submission() {
        if ( isset( $_POST['fcm_submit_checkout'] ) && wp_verify_nonce( $_POST['fcm_checkout_nonce'], 'fcm_checkout_action' ) ) {
            
            $user_id = get_current_user_id();
            $user = get_userdata( $user_id );
            
            $conference_id = intval( $_POST['fcm_conference_id'] );
            $attendance_type = sanitize_text_field( $_POST['fcm_attendance_type'] );
            $needs_accommodation = isset( $_POST['fcm_needs_accommodation'] ) ? 1 : 0;

            // Calculate total amount
            $amount = 0;
            if ( $attendance_type === 'physical' ) {
                $amount += floatval( get_post_meta( $conference_id, 'fcm_physical_fee', true ) );
            } else {
                $amount += floatval( get_post_meta( $conference_id, 'fcm_virtual_fee', true ) );
            }

            if ( $needs_accommodation ) {
                $amount += floatval( get_post_meta( $conference_id, 'fcm_accommodation_fee', true ) );
            }

            // Create Booking Post (Status: Pending)
            $booking_id = wp_insert_post( array(
                'post_type' => 'conference_booking',
                'post_title' => 'Booking - ' . $user->user_email . ' - ' . time(),
                'post_status' => 'publish',
                'post_author' => $user_id,
            ) );

            update_post_meta( $booking_id, 'conference_id', $conference_id );
            update_post_meta( $booking_id, 'attendance_type', $attendance_type );
            update_post_meta( $booking_id, 'needs_accommodation', $needs_accommodation );
            update_post_meta( $booking_id, 'amount', $amount );
            update_post_meta( $booking_id, 'payment_status', 'pending' );

            // If amount is 0, just mark as paid
            if ( $amount <= 0 ) {
                update_post_meta( $booking_id, 'payment_status', 'paid' );
                wp_redirect( add_query_arg( array( 'tab' => 'bookings', 'booking_success' => '1' ), site_url( '/dashboard' ) ) );
                exit;
            }

            // Call Credo Central API to initialize transaction
            $public_key = get_option( 'fcm_credo_public_key' );
            $secret_key = get_option( 'fcm_credo_secret_key' );
            
            if ( empty( $secret_key ) || empty( $public_key ) ) {
                wp_die( 'Payment gateway not configured properly in the admin settings.' );
            }

            $reference = 'FCM_' . $booking_id . '_' . time();
            $callback_url = add_query_arg( array( 'fcm_credo_verify' => $booking_id, 'ref' => $reference ), site_url() );

            $payload = array(
                'amount' => $amount * 100, // Amount in kobo
                'currency' => 'NGN',
                'reference' => $reference,
                'callbackUrl' => $callback_url,
                'email' => $user->user_email,
            );

            // Initialize Transaction
            $response = wp_remote_post( $this->get_api_base_url() . 'transactions/initialize', array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $secret_key,
                    'Content-Type' => 'application/json',
                ),
                'body' => wp_json_encode( $payload ),
                'timeout' => 45,
            ) );

            if ( is_wp_error( $response ) ) {
                wp_die( 'Payment initialization failed. Please try again. Error: ' . $response->get_error_message() );
            }

            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );

            // Based on generic Nigerian PG structure (like Paystack/Credo)
            if ( isset( $data['data']['authorizationUrl'] ) ) {
                update_post_meta( $booking_id, 'credo_reference', $reference );
                wp_redirect( $data['data']['authorizationUrl'] );
                exit;
            } elseif ( isset( $data['authorization_url'] ) ) { // Alternative structure
                 update_post_meta( $booking_id, 'credo_reference', $reference );
                 wp_redirect( $data['authorization_url'] );
                 exit;
            } else {
                wp_die( 'Payment gateway error: ' . print_r( $data, true ) );
            }
        }
    }

    public function verify_credo_payment() {
        // Fallback for query param name 'reference' or 'ref' or 'trxref'
        $reference = isset( $_GET['reference'] ) ? $_GET['reference'] : ( isset( $_GET['ref'] ) ? $_GET['ref'] : ( isset( $_GET['trxref'] ) ? $_GET['trxref'] : '' ) );

        if ( isset( $_GET['fcm_credo_verify'] ) && ! empty( $reference ) ) {
            $booking_id = intval( $_GET['fcm_credo_verify'] );
            $reference = sanitize_text_field( $reference );
            
            $secret_key = get_option( 'fcm_credo_secret_key' );

            $response = wp_remote_get( $this->get_api_base_url() . 'transactions/verify/' . $reference, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $secret_key,
                ),
                'timeout' => 45,
            ) );

            if ( ! is_wp_error( $response ) ) {
                $body = wp_remote_retrieve_body( $response );
                $data = json_decode( $body, true );

                if ( ( isset( $data['data']['status'] ) && $data['data']['status'] === 'success' ) || ( isset( $data['status'] ) && $data['status'] === true ) ) {
                    update_post_meta( $booking_id, 'payment_status', 'paid' );
                    // Redirect to dashboard with success
                    wp_redirect( add_query_arg( array( 'tab' => 'bookings', 'payment_success' => '1' ), site_url( '/dashboard' ) ) );
                    exit;
                }
            }

            wp_die( 'Payment verification failed or was cancelled.' );
        }
    }
}
