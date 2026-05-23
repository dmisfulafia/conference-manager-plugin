<?php
/**
 * Shortcodes and frontend hooks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FCM_Public {
    protected $templates;

    public function __construct() {
        $this->templates = array(
            'template-fcm-register.php'  => 'Conference Registration',
            'template-fcm-login.php'     => 'Conference Login',
            'template-fcm-dashboard.php' => 'Conference Dashboard',
        );

        add_filter( 'theme_page_templates', array( $this, 'register_page_templates' ) );
        add_filter( 'template_include', array( $this, 'load_page_templates' ) );
    }

    public function register_page_templates( $post_templates ) {
        $post_templates = array_merge( $post_templates, $this->templates );
        return $post_templates;
    }

    public function load_page_templates( $template ) {
        global $post;

        if ( ! $post ) {
            return $template;
        }

        $template_name = get_post_meta( $post->ID, '_wp_page_template', true );

        if ( isset( $this->templates[ $template_name ] ) ) {
            $file = FCM_PLUGIN_DIR . 'templates/' . $template_name;
            if ( file_exists( $file ) ) {
                return $file;
            }
        }

        return $template;
    }
}
