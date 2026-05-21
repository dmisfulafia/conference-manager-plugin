<?php
/**
 * Auth, Registration, Profile logic
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FCM_Users {
    public function __construct() {
        // Handle form submissions
        add_action( 'init', array( $this, 'handle_registration' ) );
        add_action( 'init', array( $this, 'handle_login' ) );
        add_action( 'init', array( $this, 'handle_email_verification' ) );
        add_action( 'init', array( $this, 'handle_profile_update' ) );
        
        // Register Shortcodes
        add_shortcode( 'fc_register', array( $this, 'registration_form_shortcode' ) );
        add_shortcode( 'fc_login', array( $this, 'login_form_shortcode' ) );
        add_shortcode( 'fc_dashboard', array( $this, 'dashboard_shortcode' ) );
    }

    public static function create_roles() {
        add_role(
            'conference_attendee',
            __( 'Conference Attendee', 'conference-manager' ),
            array(
                'read' => true,
            )
        );
    }

    public function registration_form_shortcode() {
        if ( is_user_logged_in() ) {
            return '<p>' . __( 'You are already registered and logged in.', 'conference-manager' ) . '</p>';
        }

        ob_start();
        ?>
        <form method="post" action="" class="fcm-register-form">
            <?php wp_nonce_field( 'fcm_register_action', 'fcm_register_nonce' ); ?>
            
            <div class="fcm-form-group">
                <label for="fcm_title"><?php _e( 'Title', 'conference-manager' ); ?> *</label>
                <select name="fcm_title" id="fcm_title" required>
                    <option value="Mr.">Mr.</option>
                    <option value="Ms.">Ms.</option>
                    <option value="Mrs.">Mrs.</option>
                    <option value="Dr.">Dr.</option>
                    <option value="Prof.">Prof.</option>
                </select>
            </div>

            <div class="fcm-form-group">
                <label for="fcm_first_name"><?php _e( 'First Name', 'conference-manager' ); ?> *</label>
                <input type="text" name="fcm_first_name" id="fcm_first_name" required>
            </div>

            <div class="fcm-form-group">
                <label for="fcm_last_name"><?php _e( 'Last Name', 'conference-manager' ); ?> *</label>
                <input type="text" name="fcm_last_name" id="fcm_last_name" required>
            </div>

            <div class="fcm-form-group">
                <label for="fcm_email"><?php _e( 'Email', 'conference-manager' ); ?> *</label>
                <input type="email" name="fcm_email" id="fcm_email" required>
            </div>

            <div class="fcm-form-group">
                <label for="fcm_password"><?php _e( 'Password', 'conference-manager' ); ?> *</label>
                <input type="password" name="fcm_password" id="fcm_password" required>
            </div>
            
            <div class="fcm-form-group">
                <label for="fcm_phone"><?php _e( 'Phone', 'conference-manager' ); ?> *</label>
                <input type="text" name="fcm_phone" id="fcm_phone" required>
            </div>

            <div class="fcm-form-group">
                <label for="fcm_gender"><?php _e( 'Gender', 'conference-manager' ); ?></label>
                <select name="fcm_gender" id="fcm_gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div class="fcm-form-group">
                <label for="fcm_occupation"><?php _e( 'Occupation', 'conference-manager' ); ?></label>
                <input type="text" name="fcm_occupation" id="fcm_occupation">
            </div>

            <div class="fcm-form-group">
                <label for="fcm_institution"><?php _e( 'Institution/Organization', 'conference-manager' ); ?></label>
                <input type="text" name="fcm_institution" id="fcm_institution">
            </div>

            <div class="fcm-form-group">
                <label for="fcm_country"><?php _e( 'Country', 'conference-manager' ); ?></label>
                <input type="text" name="fcm_country" id="fcm_country">
            </div>

            <input type="submit" name="fcm_submit_registration" value="<?php _e( 'Register', 'conference-manager' ); ?>">
        </form>
        <?php
        return ob_get_clean();
    }

    public function login_form_shortcode() {
        if ( is_user_logged_in() ) {
            return '<p>' . __( 'You are already logged in.', 'conference-manager' ) . '</p>';
        }

        ob_start();
        ?>
        <form method="post" action="" class="fcm-login-form">
            <?php wp_nonce_field( 'fcm_login_action', 'fcm_login_nonce' ); ?>
            
            <div class="fcm-form-group">
                <label for="fcm_login_email"><?php _e( 'Email', 'conference-manager' ); ?> *</label>
                <input type="email" name="fcm_login_email" id="fcm_login_email" required>
            </div>

            <div class="fcm-form-group">
                <label for="fcm_login_password"><?php _e( 'Password', 'conference-manager' ); ?> *</label>
                <input type="password" name="fcm_login_password" id="fcm_login_password" required>
            </div>

            <input type="submit" name="fcm_submit_login" value="<?php _e( 'Login', 'conference-manager' ); ?>">
        </form>
        <?php
        return ob_get_clean();
    }

    public function handle_registration() {
        if ( isset( $_POST['fcm_submit_registration'] ) && wp_verify_nonce( $_POST['fcm_register_nonce'], 'fcm_register_action' ) ) {
            $email = sanitize_email( $_POST['fcm_email'] );
            $password = $_POST['fcm_password'];
            $first_name = sanitize_text_field( $_POST['fcm_first_name'] );
            $last_name = sanitize_text_field( $_POST['fcm_last_name'] );
            $title = sanitize_text_field( $_POST['fcm_title'] );
            $phone = sanitize_text_field( $_POST['fcm_phone'] );
            $gender = sanitize_text_field( $_POST['fcm_gender'] );
            $occupation = sanitize_text_field( $_POST['fcm_occupation'] );
            $institution = sanitize_text_field( $_POST['fcm_institution'] );
            $country = sanitize_text_field( $_POST['fcm_country'] );

            if ( email_exists( $email ) ) {
                // Handle error
                return;
            }

            $user_id = wp_create_user( $email, $password, $email );

            if ( ! is_wp_error( $user_id ) ) {
                $user = new WP_User( $user_id );
                $user->set_role( 'conference_attendee' );

                update_user_meta( $user_id, 'first_name', $first_name );
                update_user_meta( $user_id, 'last_name', $last_name );
                update_user_meta( $user_id, 'fcm_title', $title );
                update_user_meta( $user_id, 'fcm_phone', $phone );
                update_user_meta( $user_id, 'fcm_gender', $gender );
                update_user_meta( $user_id, 'fcm_occupation', $occupation );
                update_user_meta( $user_id, 'fcm_institution', $institution );
                update_user_meta( $user_id, 'fcm_country', $country );

                // Generate Verification Hash
                $hash = wp_generate_password( 20, false );
                update_user_meta( $user_id, 'fcm_verification_hash', $hash );
                update_user_meta( $user_id, 'fcm_is_verified', 0 );

                // Send Email
                $verify_url = add_query_arg( array( 'fcm_verify' => $hash, 'uid' => $user_id ), site_url() );
                $subject = __( 'Verify Your Account - Conference Manager', 'conference-manager' );
                $message = sprintf( __( 'Please click the following link to verify your account: %s', 'conference-manager' ), $verify_url );
                wp_mail( $email, $subject, $message );

                // Redirect or show success message
                wp_redirect( add_query_arg( 'registered', '1', wp_get_referer() ) );
                exit;
            }
        }
    }

    public function handle_email_verification() {
        if ( isset( $_GET['fcm_verify'] ) && isset( $_GET['uid'] ) ) {
            $user_id = intval( $_GET['uid'] );
            $hash = sanitize_text_field( $_GET['fcm_verify'] );
            $saved_hash = get_user_meta( $user_id, 'fcm_verification_hash', true );

            if ( $hash === $saved_hash ) {
                update_user_meta( $user_id, 'fcm_is_verified', 1 );
                delete_user_meta( $user_id, 'fcm_verification_hash' );
                // Redirect to login page with success message
                wp_redirect( add_query_arg( 'verified', '1', site_url( '/login' ) ) );
                exit;
            }
        }
    }

    public function handle_login() {
        if ( isset( $_POST['fcm_submit_login'] ) && wp_verify_nonce( $_POST['fcm_login_nonce'], 'fcm_login_action' ) ) {
            $email = sanitize_email( $_POST['fcm_login_email'] );
            $password = $_POST['fcm_login_password'];

            $user = get_user_by( 'email', $email );

            if ( $user ) {
                $is_verified = get_user_meta( $user->ID, 'fcm_is_verified', true );
                if ( ! $is_verified ) {
                    // Handle not verified error
                    return;
                }

                $creds = array(
                    'user_login'    => $email,
                    'user_password' => $password,
                    'remember'      => true
                );

                $user_signon = wp_signon( $creds, false );

                if ( ! is_wp_error( $user_signon ) ) {
                    wp_redirect( site_url( '/dashboard' ) ); // Redirect to dashboard
                    exit;
                }
            }
        }
    }

    public function dashboard_shortcode() {
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'You must be logged in to view the dashboard.', 'conference-manager' ) . ' <a href="' . site_url('/login') . '">Login here</a></p>';
        }

        $user_id = get_current_user_id();
        $user = get_userdata( $user_id );
        
        // Handle active tab
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'profile';

        ob_start();
        ?>
        <div class="fcm-dashboard">
            <div class="fcm-dashboard-nav">
                <a href="?tab=profile" class="<?php echo $active_tab === 'profile' ? 'active' : ''; ?>">Profile</a>
                <a href="?tab=bookings" class="<?php echo $active_tab === 'bookings' ? 'active' : ''; ?>">Bookings</a>
                <a href="?tab=submissions" class="<?php echo $active_tab === 'submissions' ? 'active' : ''; ?>">Submissions</a>
                <a href="<?php echo wp_logout_url( site_url() ); ?>">Logout</a>
            </div>

            <div class="fcm-dashboard-content">
                <?php if ( $active_tab === 'profile' ) : ?>
                    
                    <?php if ( isset( $_GET['updated'] ) ) : ?>
                        <div class="fcm-notice fcm-success"><p>Profile updated successfully.</p></div>
                    <?php endif; ?>
                    
                    <?php if ( isset( $_GET['photo_updated'] ) ) : ?>
                        <div class="fcm-notice fcm-success"><p>Passport photo uploaded successfully.</p></div>
                    <?php endif; ?>

                    <?php if ( isset( $_GET['fcm_error'] ) ) : ?>
                        <div class="fcm-notice fcm-error"><p><?php echo esc_html( urldecode( $_GET['fcm_error'] ) ); ?></p></div>
                    <?php endif; ?>

                    <!-- Passport Photo Form -->
                    <div class="fcm-profile-section">
                        <h3>Passport Photo</h3>
                        <?php 
                        $photo_id = get_user_meta( $user_id, 'passport_photo_id', true ); 
                        if ( $photo_id ) {
                            echo wp_get_attachment_image( $photo_id, 'thumbnail' );
                        }
                        ?>
                        <form method="post" action="" enctype="multipart/form-data">
                            <?php wp_nonce_field( 'fcm_photo_action', 'fcm_photo_nonce' ); ?>
                            <input type="file" name="fcm_passport_photo" accept="image/*" required>
                            <input type="submit" name="fcm_submit_passport_photo" value="Upload Photo">
                        </form>
                    </div>

                    <!-- Profile Update Form -->
                    <div class="fcm-profile-section">
                        <h3>Update Profile</h3>
                        <form method="post" action="">
                            <?php wp_nonce_field( 'fcm_profile_action', 'fcm_profile_nonce' ); ?>
                            <div class="fcm-form-group">
                                <label>First Name</label>
                                <input type="text" name="fcm_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" required>
                            </div>
                            <div class="fcm-form-group">
                                <label>Last Name</label>
                                <input type="text" name="fcm_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" required>
                            </div>
                            <div class="fcm-form-group">
                                <label>Phone</label>
                                <input type="text" name="fcm_phone" value="<?php echo esc_attr( get_user_meta( $user_id, 'fcm_phone', true ) ); ?>" required>
                            </div>
                            <div class="fcm-form-group">
                                <label>Institution</label>
                                <input type="text" name="fcm_institution" value="<?php echo esc_attr( get_user_meta( $user_id, 'fcm_institution', true ) ); ?>">
                            </div>
                            <div class="fcm-form-group">
                                <label>Occupation</label>
                                <input type="text" name="fcm_occupation" value="<?php echo esc_attr( get_user_meta( $user_id, 'fcm_occupation', true ) ); ?>">
                            </div>
                            <input type="submit" name="fcm_submit_profile_update" value="Save Changes">
                        </form>
                    </div>

                    <!-- Password Change Form -->
                    <div class="fcm-profile-section">
                        <h3>Change Password</h3>
                        <form method="post" action="">
                            <?php wp_nonce_field( 'fcm_password_action', 'fcm_password_nonce' ); ?>
                            <div class="fcm-form-group">
                                <label>New Password</label>
                                <input type="password" name="fcm_new_password" required>
                            </div>
                            <input type="submit" name="fcm_submit_password_change" value="Change Password">
                        </form>
                    </div>

                <?php elseif ( $active_tab === 'bookings' ) : ?>
                    <h3>Your Bookings & Receipts</h3>
                    <?php
                    $bookings = get_posts( array(
                        'post_type' => 'conference_booking',
                        'author' => $user_id,
                        'numberposts' => -1,
                        'post_status' => 'publish'
                    ) );

                    if ( ! empty( $bookings ) ) {
                        echo '<div class="fcm-bookings-list">';
                        foreach ( $bookings as $booking ) {
                            $conference_id = get_post_meta( $booking->ID, 'conference_id', true );
                            $status = get_post_meta( $booking->ID, 'payment_status', true );
                            $amount = get_post_meta( $booking->ID, 'amount', true );
                            $type = get_post_meta( $booking->ID, 'booking_type', true );
                            
                            echo '<div class="fcm-booking-card" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; display:flex; justify-content:space-between; align-items:center;">';
                            echo '<div>';
                            echo '<h4>' . ( $conference_id ? get_the_title( $conference_id ) : 'Submission Fee' ) . '</h4>';
                            echo '<p><strong>Booking ID:</strong> #' . $booking->ID . '</p>';
                            echo '<p><strong>Type:</strong> ' . ( $type ? ucwords(str_replace('_', ' ', $type)) : 'Attendance' ) . '</p>';
                            echo '<p><strong>Amount:</strong> ' . ( $amount > 0 ? 'NGN ' . number_format($amount, 2) : 'Free' ) . '</p>';
                            echo '<p><strong>Status:</strong> <span style="color:' . ($status === 'paid' ? 'green' : 'orange') . ';">' . strtoupper($status) . '</span></p>';
                            echo '</div>';
                            
                            if ( $status === 'paid' ) {
                                $qr_url = FCM_QR_Generator::get_qr_code_url( $booking->ID, '100x100' );
                                echo '<div>';
                                echo '<img src="' . esc_url( $qr_url ) . '" alt="QR Code" style="border:1px solid #eee; padding:5px; border-radius:5px;" />';
                                echo '<div style="text-align:center; font-size:0.8em; margin-top:5px;">Check-in QR</div>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo '<p>You have no bookings yet.</p>';
                    }
                    ?>
                <?php elseif ( $active_tab === 'submissions' ) : ?>
                    <h3>Your Submissions</h3>
                    <p>Submissions will appear here (Phase 5).</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_profile_update() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        $user_id = get_current_user_id();

        // Handle Profile Update
        if ( isset( $_POST['fcm_submit_profile_update'] ) && wp_verify_nonce( $_POST['fcm_profile_nonce'], 'fcm_profile_action' ) ) {
            update_user_meta( $user_id, 'first_name', sanitize_text_field( $_POST['fcm_first_name'] ) );
            update_user_meta( $user_id, 'last_name', sanitize_text_field( $_POST['fcm_last_name'] ) );
            update_user_meta( $user_id, 'fcm_phone', sanitize_text_field( $_POST['fcm_phone'] ) );
            update_user_meta( $user_id, 'fcm_institution', sanitize_text_field( $_POST['fcm_institution'] ) );
            update_user_meta( $user_id, 'fcm_occupation', sanitize_text_field( $_POST['fcm_occupation'] ) );
            wp_redirect( add_query_arg( array( 'tab' => 'profile', 'updated' => '1' ), wp_get_referer() ) );
            exit;
        }

        // Handle Password Change
        if ( isset( $_POST['fcm_submit_password_change'] ) && wp_verify_nonce( $_POST['fcm_password_nonce'], 'fcm_password_action' ) ) {
            wp_set_password( $_POST['fcm_new_password'], $user_id );
            // Setting password logs the user out, redirect to login page.
            wp_redirect( site_url( '/login?pwd_changed=1' ) );
            exit;
        }

        // Handle Passport Photo Upload
        if ( isset( $_POST['fcm_submit_passport_photo'] ) && wp_verify_nonce( $_POST['fcm_photo_nonce'], 'fcm_photo_action' ) ) {
            if ( ! empty( $_FILES['fcm_passport_photo']['name'] ) ) {
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                require_once( ABSPATH . 'wp-admin/includes/media.php' );

                $attachment_id = media_handle_upload( 'fcm_passport_photo', 0 );

                if ( is_wp_error( $attachment_id ) ) {
                    wp_redirect( add_query_arg( array( 'tab' => 'profile', 'fcm_error' => urlencode( $attachment_id->get_error_message() ) ), wp_get_referer() ) );
                    exit;
                } else {
                    update_user_meta( $user_id, 'passport_photo_id', $attachment_id );
                    wp_redirect( add_query_arg( array( 'tab' => 'profile', 'photo_updated' => '1' ), wp_get_referer() ) );
                    exit;
                }
            }
        }
    }
}
