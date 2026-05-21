<?php
/**
 * Admin menus and dashboards
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FCM_Admin {
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_conference_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_conference_meta_boxes' ) );
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        // Submission Meta Boxes
        add_action( 'add_meta_boxes', array( $this, 'add_submission_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_submission_meta_boxes' ) );
        
        // Analytics & Export
        add_action( 'admin_menu', array( $this, 'add_analytics_page' ) );
        add_action( 'admin_init', array( $this, 'handle_csv_export' ) );
    }

    public function add_analytics_page() {
        add_submenu_page(
            'edit.php?post_type=conference',
            __( 'Analytics & Export', 'conference-manager' ),
            __( 'Analytics & Export', 'conference-manager' ),
            'manage_options',
            'fcm-analytics',
            array( $this, 'analytics_page_html' )
        );
    }

    public function handle_csv_export() {
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'fcm-analytics' && isset( $_GET['fcm_export_csv'] ) ) {
            if ( ! current_user_can( 'manage_options' ) ) return;

            $conference_filter = isset( $_GET['filter_conference'] ) ? intval( $_GET['filter_conference'] ) : 0;
            $status_filter = isset( $_GET['filter_status'] ) ? sanitize_text_field( $_GET['filter_status'] ) : '';

            $args = array(
                'post_type' => 'conference_booking',
                'numberposts' => -1,
                'post_status' => 'publish',
            );

            $meta_query = array();
            if ( $conference_filter > 0 ) {
                $meta_query[] = array(
                    'key' => 'conference_id',
                    'value' => $conference_filter,
                );
            }
            if ( ! empty( $status_filter ) ) {
                $meta_query[] = array(
                    'key' => 'payment_status',
                    'value' => $status_filter,
                );
            }

            if ( ! empty( $meta_query ) ) {
                $args['meta_query'] = $meta_query;
            }

            $bookings = get_posts( $args );

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="bookings_export_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, array('Booking ID', 'Attendee First Name', 'Attendee Last Name', 'Email', 'Phone', 'Institution', 'Conference', 'Amount (NGN)', 'Status', 'Date'));

            foreach ( $bookings as $booking ) {
                $user = get_userdata( $booking->post_author );
                $conference_id = get_post_meta( $booking->ID, 'conference_id', true );
                $status = get_post_meta( $booking->ID, 'payment_status', true );
                $amount = get_post_meta( $booking->ID, 'amount', true );
                $phone = get_user_meta( $user->ID, 'fcm_phone', true );
                $institution = get_user_meta( $user->ID, 'fcm_institution', true );

                fputcsv($output, array(
                    $booking->ID,
                    $user->first_name,
                    $user->last_name,
                    $user->user_email,
                    $phone,
                    $institution,
                    $conference_id ? get_the_title( $conference_id ) : 'N/A',
                    $amount,
                    strtoupper($status),
                    $booking->post_date
                ));
            }

            fclose($output);
            exit;
        }
    }

    public function analytics_page_html() {
        if ( ! current_user_can( 'manage_options' ) ) return;

        $conferences = get_posts( array( 'post_type' => 'conference', 'numberposts' => -1 ) );
        
        $conference_filter = isset( $_GET['filter_conference'] ) ? intval( $_GET['filter_conference'] ) : 0;
        $status_filter = isset( $_GET['filter_status'] ) ? sanitize_text_field( $_GET['filter_status'] ) : '';
        $search_query = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

        $args = array(
            'post_type' => 'conference_booking',
            'numberposts' => -1,
            'post_status' => 'publish',
            's' => $search_query
        );

        $meta_query = array();
        if ( $conference_filter > 0 ) {
            $meta_query[] = array( 'key' => 'conference_id', 'value' => $conference_filter );
        }
        if ( ! empty( $status_filter ) ) {
            $meta_query[] = array( 'key' => 'payment_status', 'value' => $status_filter );
        }

        if ( ! empty( $meta_query ) ) {
            $args['meta_query'] = $meta_query;
        }

        $bookings = get_posts( $args );

        ?>
        <div class="wrap">
            <h1><?php _e( 'Bookings Analytics & Export', 'conference-manager' ); ?></h1>
            
            <form method="get" action="">
                <input type="hidden" name="post_type" value="conference">
                <input type="hidden" name="page" value="fcm-analytics">
                
                <div class="tablenav top">
                    <div class="alignleft actions">
                        <select name="filter_conference">
                            <option value="0">All Conferences</option>
                            <?php foreach ( $conferences as $conf ) : ?>
                                <option value="<?php echo esc_attr( $conf->ID ); ?>" <?php selected( $conference_filter, $conf->ID ); ?>><?php echo esc_html( $conf->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select name="filter_status">
                            <option value="">All Statuses</option>
                            <option value="paid" <?php selected( $status_filter, 'paid' ); ?>>Paid</option>
                            <option value="pending" <?php selected( $status_filter, 'pending' ); ?>>Pending</option>
                        </select>
                        
                        <input type="text" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="Search Bookings...">
                        
                        <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
                        
                        <a href="<?php echo esc_url( add_query_arg( 'fcm_export_csv', '1' ) ); ?>" class="button button-primary" style="margin-left: 15px;">Export to CSV</a>
                    </div>
                </div>
            </form>

            <table class="wp-list-table widefat fixed striped" style="margin-top:15px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Attendee</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Conference</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $bookings ) ) : ?>
                        <tr><td colspan="8">No bookings found matching criteria.</td></tr>
                    <?php else : ?>
                        <?php foreach ( $bookings as $booking ) : 
                            $user = get_userdata( $booking->post_author );
                            $conference_id = get_post_meta( $booking->ID, 'conference_id', true );
                            $status = get_post_meta( $booking->ID, 'payment_status', true );
                            $amount = get_post_meta( $booking->ID, 'amount', true );
                            $phone = get_user_meta( $user->ID, 'fcm_phone', true );
                        ?>
                            <tr>
                                <td>#<?php echo esc_html( $booking->ID ); ?></td>
                                <td><?php echo esc_html( $user ? $user->first_name . ' ' . $user->last_name : 'Unknown' ); ?></td>
                                <td><?php echo esc_html( $user ? $user->user_email : '' ); ?></td>
                                <td><?php echo esc_html( $phone ); ?></td>
                                <td><?php echo esc_html( $conference_id ? get_the_title( $conference_id ) : 'N/A' ); ?></td>
                                <td><?php echo esc_html( number_format( (float) $amount, 2 ) ); ?></td>
                                <td>
                                    <?php 
                                    if ( $status === 'paid' ) {
                                        echo '<span style="color:green;font-weight:bold;">PAID</span>';
                                    } else {
                                        echo '<span style="color:orange;font-weight:bold;">PENDING</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo esc_html( date( 'Y-m-d', strtotime( $booking->post_date ) ) ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=conference',
            __( 'Settings', 'conference-manager' ),
            __( 'Settings', 'conference-manager' ),
            'manage_options',
            'fcm-settings',
            array( $this, 'settings_page_html' )
        );
    }

    public function register_settings() {
        register_setting( 'fcm_settings_group', 'fcm_credo_public_key' );
        register_setting( 'fcm_settings_group', 'fcm_credo_secret_key' );
        register_setting( 'fcm_settings_group', 'fcm_credo_mode' );
    }

    public function settings_page_html() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php _e( 'Conference Manager Settings', 'conference-manager' ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'fcm_settings_group' );
                do_settings_sections( 'fcm_settings_group' );
                ?>
                <h2><?php _e( 'Credo Central Integration', 'conference-manager' ); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Credo Mode', 'conference-manager' ); ?></th>
                        <td>
                            <select name="fcm_credo_mode">
                                <option value="test" <?php selected( get_option('fcm_credo_mode'), 'test' ); ?>><?php _e( 'Test', 'conference-manager' ); ?></option>
                                <option value="live" <?php selected( get_option('fcm_credo_mode'), 'live' ); ?>><?php _e( 'Live', 'conference-manager' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Credo Public Key', 'conference-manager' ); ?></th>
                        <td><input type="text" name="fcm_credo_public_key" value="<?php echo esc_attr( get_option('fcm_credo_public_key') ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Credo Secret Key', 'conference-manager' ); ?></th>
                        <td><input type="password" name="fcm_credo_secret_key" value="<?php echo esc_attr( get_option('fcm_credo_secret_key') ); ?>" class="regular-text" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function add_conference_meta_boxes() {
        add_meta_box(
            'fcm_conference_details',
            __( 'Conference Details & Pricing', 'conference-manager' ),
            array( $this, 'render_conference_meta_box' ),
            'conference',
            'normal',
            'high'
        );
    }

    public function render_conference_meta_box( $post ) {
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'fcm_save_conference_data', 'fcm_conference_meta_box_nonce' );

        $start_date = get_post_meta( $post->ID, 'fcm_start_date', true );
        $end_date = get_post_meta( $post->ID, 'fcm_end_date', true );
        
        $physical_fee = get_post_meta( $post->ID, 'fcm_physical_fee', true );
        $virtual_fee = get_post_meta( $post->ID, 'fcm_virtual_fee', true );
        $accommodation_fee = get_post_meta( $post->ID, 'fcm_accommodation_fee', true );
        $abstract_fee = get_post_meta( $post->ID, 'fcm_abstract_fee', true );
        $paper_fee = get_post_meta( $post->ID, 'fcm_paper_fee', true );

        ?>
        <style>
            .fcm-admin-field { margin-bottom: 15px; }
            .fcm-admin-field label { display: inline-block; width: 220px; font-weight: bold; }
            .fcm-admin-field input[type="date"], .fcm-admin-field input[type="number"] { width: 200px; }
            .fcm-admin-section-title { margin-top: 20px; margin-bottom: 10px; font-size: 1.2em; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        </style>

        <div class="fcm-admin-section-title"><?php _e( 'Dates', 'conference-manager' ); ?></div>
        <div class="fcm-admin-field">
            <label for="fcm_start_date"><?php _e( 'Start Date', 'conference-manager' ); ?></label>
            <input type="date" id="fcm_start_date" name="fcm_start_date" value="<?php echo esc_attr( $start_date ); ?>" />
        </div>
        <div class="fcm-admin-field">
            <label for="fcm_end_date"><?php _e( 'End Date', 'conference-manager' ); ?></label>
            <input type="date" id="fcm_end_date" name="fcm_end_date" value="<?php echo esc_attr( $end_date ); ?>" />
        </div>

        <div class="fcm-admin-section-title"><?php _e( 'Pricing (Amount in local currency)', 'conference-manager' ); ?></div>
        <div class="fcm-admin-field">
            <label for="fcm_physical_fee"><?php _e( 'Physical Attendance Fee', 'conference-manager' ); ?></label>
            <input type="number" step="0.01" id="fcm_physical_fee" name="fcm_physical_fee" value="<?php echo esc_attr( $physical_fee ); ?>" />
        </div>
        <div class="fcm-admin-field">
            <label for="fcm_virtual_fee"><?php _e( 'Virtual Attendance Fee', 'conference-manager' ); ?></label>
            <input type="number" step="0.01" id="fcm_virtual_fee" name="fcm_virtual_fee" value="<?php echo esc_attr( $virtual_fee ); ?>" />
        </div>
        <div class="fcm-admin-field">
            <label for="fcm_accommodation_fee"><?php _e( 'Accommodation Fee', 'conference-manager' ); ?></label>
            <input type="number" step="0.01" id="fcm_accommodation_fee" name="fcm_accommodation_fee" value="<?php echo esc_attr( $accommodation_fee ); ?>" />
        </div>
        <div class="fcm-admin-field">
            <label for="fcm_abstract_fee"><?php _e( 'Abstract Submission Fee', 'conference-manager' ); ?></label>
            <input type="number" step="0.01" id="fcm_abstract_fee" name="fcm_abstract_fee" value="<?php echo esc_attr( $abstract_fee ); ?>" />
        </div>
        <div class="fcm-admin-field">
            <label for="fcm_paper_fee"><?php _e( 'Full Paper Submission Fee', 'conference-manager' ); ?></label>
            <input type="number" step="0.01" id="fcm_paper_fee" name="fcm_paper_fee" value="<?php echo esc_attr( $paper_fee ); ?>" />
        </div>
        <p class="description"><?php _e('Leave pricing fields blank or 0 if they are free or not applicable.', 'conference-manager'); ?></p>
        <?php
    }

    public function save_conference_meta_boxes( $post_id ) {
        // Check if our nonce is set.
        if ( ! isset( $_POST['fcm_conference_meta_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['fcm_conference_meta_box_nonce'], 'fcm_save_conference_data' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'conference' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        } else {
            return;
        }

        // Save Dates
        if ( isset( $_POST['fcm_start_date'] ) ) {
            update_post_meta( $post_id, 'fcm_start_date', sanitize_text_field( $_POST['fcm_start_date'] ) );
        }
        if ( isset( $_POST['fcm_end_date'] ) ) {
            update_post_meta( $post_id, 'fcm_end_date', sanitize_text_field( $_POST['fcm_end_date'] ) );
        }

        // Save Pricing
        $fees = array(
            'fcm_physical_fee',
            'fcm_virtual_fee',
            'fcm_accommodation_fee',
            'fcm_abstract_fee',
            'fcm_paper_fee'
        );

        foreach ( $fees as $fee ) {
            if ( isset( $_POST[$fee] ) ) {
                update_post_meta( $post_id, $fee, sanitize_text_field( $_POST[$fee] ) );
            }
        }
    }

    public function add_submission_meta_boxes() {
        $screens = array( 'conference_abstract', 'conference_paper' );
        foreach ( $screens as $screen ) {
            add_meta_box(
                'fcm_submission_status',
                __( 'Submission Status & File', 'conference-manager' ),
                array( $this, 'render_submission_meta_box' ),
                $screen,
                'side',
                'default'
            );
        }
    }

    public function render_submission_meta_box( $post ) {
        wp_nonce_field( 'fcm_save_submission_data', 'fcm_submission_meta_box_nonce' );

        $status = get_post_meta( $post->ID, 'review_status', true );
        if ( empty( $status ) ) $status = 'Pending Review';
        
        $file_id = get_post_meta( $post->ID, 'file_id', true );
        $conference_id = get_post_meta( $post->ID, 'conference_id', true );

        echo '<p><strong>Conference:</strong> ' . ( $conference_id ? get_the_title( $conference_id ) : 'N/A' ) . '</p>';

        if ( $file_id ) {
            $file_url = wp_get_attachment_url( $file_id );
            echo '<p><a href="' . esc_url( $file_url ) . '" target="_blank" class="button button-primary">View/Download Document</a></p>';
        } else {
            echo '<p>No document attached.</p>';
        }

        ?>
        <p>
            <label for="fcm_review_status"><strong><?php _e( 'Review Status', 'conference-manager' ); ?></strong></label>
            <br/>
            <select name="fcm_review_status" id="fcm_review_status" style="width:100%; margin-top:5px;">
                <option value="Pending Review" <?php selected( $status, 'Pending Review' ); ?>>Pending Review</option>
                <option value="Accepted" <?php selected( $status, 'Accepted' ); ?>>Accepted</option>
                <option value="Rejected" <?php selected( $status, 'Rejected' ); ?>>Rejected</option>
            </select>
        </p>
        <?php
    }

    public function save_submission_meta_boxes( $post_id ) {
        if ( ! isset( $_POST['fcm_submission_meta_box_nonce'] ) ) return;
        if ( ! wp_verify_nonce( $_POST['fcm_submission_meta_box_nonce'], 'fcm_save_submission_data' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if ( isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], array( 'conference_abstract', 'conference_paper' ) ) ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        } else {
            return;
        }

        if ( isset( $_POST['fcm_review_status'] ) ) {
            update_post_meta( $post_id, 'review_status', sanitize_text_field( $_POST['fcm_review_status'] ) );
        }
    }
}
