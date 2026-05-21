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
}
