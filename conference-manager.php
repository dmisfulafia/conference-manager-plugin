<?php
/**
 * Plugin Name: Conference Manager
 * Description: A custom, modular WordPress plugin designed to manage university conferences, abstract/paper submissions, attendee registration, and Credo Central-powered payments.
 * Version: 1.0.0
 * Author: Custom Developer
 * Text Domain: conference-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'FCM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FCM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include core classes
require_once FCM_PLUGIN_DIR . 'includes/class-fcm-cpts.php';
require_once FCM_PLUGIN_DIR . 'includes/class-fcm-users.php';
require_once FCM_PLUGIN_DIR . 'includes/class-fcm-payments.php';
require_once FCM_PLUGIN_DIR . 'includes/class-fcm-submissions.php';
require_once FCM_PLUGIN_DIR . 'includes/class-fcm-qr-generator.php';

// Include admin and public classes
if ( is_admin() ) {
    require_once FCM_PLUGIN_DIR . 'admin/class-fcm-admin.php';
}
require_once FCM_PLUGIN_DIR . 'public/class-fcm-public.php';

// Initialize the plugin
function fcm_init() {
    new FCM_CPTs();
    new FCM_Users();
    new FCM_Payments();
    new FCM_Submissions();
    new FCM_QR_Generator();
    
    if ( is_admin() ) {
        new FCM_Admin();
    }
    
    new FCM_Public();
}
add_action( 'plugins_loaded', 'fcm_init' );

// Activation Hook
register_activation_hook( __FILE__, 'fcm_plugin_activation' );
function fcm_plugin_activation() {
    require_once FCM_PLUGIN_DIR . 'includes/class-fcm-users.php';
    FCM_Users::create_roles();
    
    require_once FCM_PLUGIN_DIR . 'includes/class-fcm-cpts.php';
    $cpts = new FCM_CPTs();
    $cpts->register_cpts();
    
    flush_rewrite_rules();
}
