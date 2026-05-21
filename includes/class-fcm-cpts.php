<?php
/**
 * Registers Custom Post Types and Taxonomies
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FCM_CPTs {
    public function __construct() {
        add_action( 'init', array( $this, 'register_cpts' ) );
    }

    public function register_cpts() {
        // Conference CPT
        $conference_labels = array(
            'name'                  => _x( 'Conferences', 'Post Type General Name', 'conference-manager' ),
            'singular_name'         => _x( 'Conference', 'Post Type Singular Name', 'conference-manager' ),
            'menu_name'             => __( 'Conferences', 'conference-manager' ),
            'all_items'             => __( 'All Conferences', 'conference-manager' ),
            'add_new_item'          => __( 'Add New Conference', 'conference-manager' ),
            'add_new'               => __( 'Add New', 'conference-manager' ),
            'edit_item'             => __( 'Edit Conference', 'conference-manager' ),
            'update_item'           => __( 'Update Conference', 'conference-manager' ),
            'view_item'             => __( 'View Conference', 'conference-manager' ),
            'search_items'          => __( 'Search Conference', 'conference-manager' ),
        );
        $conference_args = array(
            'label'                 => __( 'Conference', 'conference-manager' ),
            'labels'                => $conference_labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-megaphone',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        );
        register_post_type( 'conference', $conference_args );

        // Conference Abstract CPT
        $abstract_labels = array(
            'name'                  => _x( 'Abstracts', 'Post Type General Name', 'conference-manager' ),
            'singular_name'         => _x( 'Abstract', 'Post Type Singular Name', 'conference-manager' ),
            'menu_name'             => __( 'Abstracts', 'conference-manager' ),
            'all_items'             => __( 'All Abstracts', 'conference-manager' ),
        );
        $abstract_args = array(
            'label'                 => __( 'Abstract', 'conference-manager' ),
            'labels'                => $abstract_labels,
            'supports'              => array( 'title', 'editor', 'author', 'custom-fields' ),
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'edit.php?post_type=conference',
            'capability_type'       => 'post',
        );
        register_post_type( 'conference_abstract', $abstract_args );

        // Conference Paper CPT
        $paper_labels = array(
            'name'                  => _x( 'Full Papers', 'Post Type General Name', 'conference-manager' ),
            'singular_name'         => _x( 'Full Paper', 'Post Type Singular Name', 'conference-manager' ),
            'menu_name'             => __( 'Full Papers', 'conference-manager' ),
            'all_items'             => __( 'All Papers', 'conference-manager' ),
        );
        $paper_args = array(
            'label'                 => __( 'Full Paper', 'conference-manager' ),
            'labels'                => $paper_labels,
            'supports'              => array( 'title', 'editor', 'author', 'custom-fields' ),
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'edit.php?post_type=conference',
            'capability_type'       => 'post',
        );
        register_post_type( 'conference_paper', $paper_args );

        // Conference Booking CPT
        $booking_labels = array(
            'name'                  => _x( 'Bookings', 'Post Type General Name', 'conference-manager' ),
            'singular_name'         => _x( 'Booking', 'Post Type Singular Name', 'conference-manager' ),
            'menu_name'             => __( 'Bookings', 'conference-manager' ),
            'all_items'             => __( 'All Bookings', 'conference-manager' ),
        );
        $booking_args = array(
            'label'                 => __( 'Booking', 'conference-manager' ),
            'labels'                => $booking_labels,
            'supports'              => array( 'title', 'author', 'custom-fields' ),
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'edit.php?post_type=conference',
            'capability_type'       => 'post',
        );
        register_post_type( 'conference_booking', $booking_args );
    }
}
