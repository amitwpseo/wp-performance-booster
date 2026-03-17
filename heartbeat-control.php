<?php
/**
 * Heartbeat Control
 * 
 * Controls WordPress Heartbeat API to reduce server load.
 * 
 * @package WP_Performance_Booster
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Heartbeat_Control {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'init', array( $this, 'control_heartbeat' ), 1 );
        add_action( 'admin_enqueue_scripts', array( $this, 'maybe_disable_heartbeat' ), 1 );
    }
    
    /**
     * Control Heartbeat settings
     */
    public function control_heartbeat() {
        global $pagenow;
        
        // Settings
        $heartbeat_frequency = apply_filters( 'wp_heartbeat_frequency', 60 ); // seconds
        $disable_heartbeat = apply_filters( 'wp_disable_heartbeat', false );
        
        // Disable Heartbeat completely
        if ( $disable_heartbeat ) {
            wp_deregister_script( 'heartbeat' );
            return;
        }
        
        // Change Heartbeat frequency
        if ( $heartbeat_frequency !== 15 ) { // default is 15 seconds
            add_filter( 'heartbeat_settings', function( $settings ) use ( $heartbeat_frequency ) {
                $settings['interval'] = $heartbeat_frequency;
                return $settings;
            });
        }
        
        // Disable on specific pages
        if ( $this->should_disable_heartbeat( $pagenow ) ) {
            wp_deregister_script( 'heartbeat' );
        }
    }
    
    /**
     * Check if Heartbeat should be disabled on current page
     */
    private function should_disable_heartbeat( $pagenow ) {
        $disable_pages = apply_filters( 'wp_heartbeat_disable_pages', array(
            'edit.php',
            'post.php',
            'post-new.php',
            'admin-ajax.php',
        ));
        
        return in_array( $pagenow, $disable_pages );
    }
    
    /**
     * Maybe disable Heartbeat entirely in admin
     */
    public function maybe_disable_heartbeat() {
        $disable_in_admin = apply_filters( 'wp_disable_heartbeat_admin', true );
        
        if ( $disable_in_admin ) {
            wp_deregister_script( 'heartbeat' );
        }
    }
}

// Initialize the class
new Heartbeat_Control();
