<?php
/**
 * Abstract/Paper logic
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FCM_Submissions {
    public function __construct() {
        add_shortcode( 'fc_submit_abstract', array( $this, 'submit_abstract_shortcode' ) );
        add_shortcode( 'fc_submit_paper', array( $this, 'submit_paper_shortcode' ) );
        add_action( 'init', array( $this, 'handle_submission' ) );
    }

    public function submit_form_html( $type = 'abstract' ) {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'You must be logged in to submit.', 'conference-manager' ) . '</p>';
        }

        $conferences = get_posts( array(
            'post_type' => 'conference',
            'numberposts' => -1,
            'post_status' => 'publish'
        ) );

        if ( empty( $conferences ) ) {
            return '<p>' . __( 'No conferences available.', 'conference-manager' ) . '</p>';
        }

        $title_label = $type === 'abstract' ? __( 'Abstract Title', 'conference-manager' ) : __( 'Paper Title', 'conference-manager' );
        
        ob_start();
        ?>
        <form method="post" action="" enctype="multipart/form-data" class="fcm-submission-form">
            <?php wp_nonce_field( 'fcm_submit_' . $type . '_action', 'fcm_submit_nonce' ); ?>
            <input type="hidden" name="fcm_submission_type" value="<?php echo esc_attr( $type ); ?>">
            
            <div class="fcm-form-group">
                <label><?php _e( 'Select Conference', 'conference-manager' ); ?> *</label>
                <select name="fcm_conference_id" required>
                    <?php foreach ( $conferences as $conf ) : ?>
                        <option value="<?php echo esc_attr( $conf->ID ); ?>"><?php echo esc_html( $conf->post_title ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="fcm-form-group">
                <label><?php echo esc_html( $title_label ); ?> *</label>
                <input type="text" name="fcm_submission_title" required>
            </div>

            <div class="fcm-form-group">
                <label><?php _e( 'Upload Document (PDF or DOCX, max 15MB)', 'conference-manager' ); ?> *</label>
                <input type="file" name="fcm_submission_file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
            </div>

            <input type="submit" name="fcm_submit_document" value="<?php _e( 'Submit', 'conference-manager' ); ?>">
        </form>
        <?php
        return ob_get_clean();
    }

    public function submit_abstract_shortcode() {
        return $this->submit_form_html( 'abstract' );
    }

    public function submit_paper_shortcode() {
        return $this->submit_form_html( 'paper' );
    }

    public function handle_submission() {
        if ( isset( $_POST['fcm_submit_document'] ) && isset( $_POST['fcm_submit_nonce'] ) ) {
            $type = sanitize_text_field( $_POST['fcm_submission_type'] );
            if ( ! wp_verify_nonce( $_POST['fcm_submit_nonce'], 'fcm_submit_' . $type . '_action' ) ) {
                return;
            }

            $user_id = get_current_user_id();
            $conference_id = intval( $_POST['fcm_conference_id'] );
            $title = sanitize_text_field( $_POST['fcm_submission_title'] );

            // Handle file upload
            if ( empty( $_FILES['fcm_submission_file']['name'] ) ) {
                wp_die( 'Please upload a file.' );
            }

            $file = $_FILES['fcm_submission_file'];
            $max_size = 15 * 1024 * 1024; // 15MB

            if ( $file['size'] > $max_size ) {
                wp_die( 'File size exceeds 15MB limit.' );
            }

            $allowed_types = array(
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            );

            $file_info = wp_check_filetype( basename( $file['name'] ) );
            if ( ! in_array( $file_info['type'], $allowed_types ) ) {
                wp_die( 'Invalid file type. Only PDF and DOCX are allowed.' );
            }

            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );

            $attachment_id = media_handle_upload( 'fcm_submission_file', 0 );

            if ( is_wp_error( $attachment_id ) ) {
                wp_die( 'Upload error: ' . $attachment_id->get_error_message() );
            }

            $post_type = $type === 'abstract' ? 'conference_abstract' : 'conference_paper';
            $meta_fee_key = $type === 'abstract' ? 'fcm_abstract_fee' : 'fcm_paper_fee';

            $submission_id = wp_insert_post( array(
                'post_type' => $post_type,
                'post_title' => $title,
                'post_status' => 'publish',
                'post_author' => $user_id,
            ) );

            update_post_meta( $submission_id, 'conference_id', $conference_id );
            update_post_meta( $submission_id, 'file_id', $attachment_id );
            update_post_meta( $submission_id, 'review_status', 'Pending Review' );

            // Check if fee is required
            $fee = floatval( get_post_meta( $conference_id, $meta_fee_key, true ) );

            if ( $fee > 0 ) {
                // Initialize payment
                $booking_id = wp_insert_post( array(
                    'post_type' => 'conference_booking',
                    'post_title' => ucfirst($type) . ' Fee - ' . wp_get_current_user()->user_email . ' - ' . time(),
                    'post_status' => 'publish',
                    'post_author' => $user_id,
                ) );

                update_post_meta( $booking_id, 'conference_id', $conference_id );
                update_post_meta( $booking_id, 'booking_type', $type . '_submission' );
                update_post_meta( $booking_id, 'submission_id', $submission_id );
                update_post_meta( $booking_id, 'amount', $fee );
                update_post_meta( $booking_id, 'payment_status', 'pending' );

                // Init Credo
                $public_key = get_option( 'fcm_credo_public_key' );
                $secret_key = get_option( 'fcm_credo_secret_key' );
                $mode = get_option( 'fcm_credo_mode', 'test' );
                $base_url = $mode === 'live' ? 'https://api.credocentral.com/v1/' : 'https://api.credocentral.com/v1/';

                if ( empty( $secret_key ) ) {
                    wp_die( 'Payment gateway not configured.' );
                }

                $reference = 'FCM_' . $booking_id . '_' . time();
                $callback_url = add_query_arg( array( 'fcm_credo_verify' => $booking_id, 'ref' => $reference ), site_url() );

                $payload = array(
                    'amount' => $fee * 100,
                    'currency' => 'NGN',
                    'reference' => $reference,
                    'callbackUrl' => $callback_url,
                    'email' => wp_get_current_user()->user_email,
                );

                $response = wp_remote_post( $base_url . 'transactions/initialize', array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $secret_key,
                        'Content-Type' => 'application/json',
                    ),
                    'body' => wp_json_encode( $payload ),
                    'timeout' => 45,
                ) );

                if ( is_wp_error( $response ) ) {
                    wp_die( 'Payment initialization failed.' );
                }

                $body = wp_remote_retrieve_body( $response );
                $data = json_decode( $body, true );

                if ( isset( $data['data']['authorizationUrl'] ) ) {
                    update_post_meta( $booking_id, 'credo_reference', $reference );
                    wp_redirect( $data['data']['authorizationUrl'] );
                    exit;
                } elseif ( isset( $data['authorization_url'] ) ) {
                     update_post_meta( $booking_id, 'credo_reference', $reference );
                     wp_redirect( $data['authorization_url'] );
                     exit;
                } else {
                    wp_die( 'Payment gateway error: ' . print_r( $data, true ) );
                }
            } else {
                update_post_meta( $submission_id, 'payment_status', 'paid' );
                wp_redirect( add_query_arg( array( 'tab' => 'submissions', 'submitted' => '1' ), site_url( '/dashboard' ) ) );
                exit;
            }
        }
    }
}
