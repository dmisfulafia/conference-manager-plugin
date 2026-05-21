<?php
/**
 * QR Code logic
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FCM_QR_Generator {
    public function __construct() {
        // Add a meta box to show QR code in admin booking view
        add_action( 'add_meta_boxes', array( $this, 'add_booking_qr_meta_box' ) );
    }

    public static function get_qr_payload( $booking_id ) {
        $booking = get_post( $booking_id );
        if ( ! $booking ) return '';
        
        $user_id = $booking->post_author;
        
        $payload = array(
            'booking_id' => $booking_id,
            'user_id' => $user_id,
            'status' => get_post_meta( $booking_id, 'payment_status', true )
        );

        // Base64 encode the payload to ensure it's safely passed in the URL
        return base64_encode( wp_json_encode( $payload ) );
    }

    public static function get_qr_code_url( $booking_id, $size = '150x150' ) {
        $payload = self::get_qr_payload( $booking_id );
        if ( empty( $payload ) ) return '';

        // Using a reliable free QR code generator API to avoid bloating the plugin with a large library
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . '&data=' . urlencode( $payload );
    }

    public function add_booking_qr_meta_box() {
        add_meta_box(
            'fcm_booking_qr',
            __( 'Check-in QR Code', 'conference-manager' ),
            array( $this, 'render_booking_qr_meta_box' ),
            'conference_booking',
            'side',
            'default'
        );
    }

    public function render_booking_qr_meta_box( $post ) {
        $status = get_post_meta( $post->ID, 'payment_status', true );
        
        if ( $status === 'paid' ) {
            $qr_url = self::get_qr_code_url( $post->ID );
            echo '<div style="text-align:center;">';
            echo '<img src="' . esc_url( $qr_url ) . '" alt="QR Code" />';
            echo '<p><strong>Scan this QR code during check-in.</strong></p>';
            echo '</div>';
        } else {
            echo '<p>QR Code is only generated for paid/completed bookings.</p>';
        }
    }
}
