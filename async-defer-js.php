<?php
/**
 * Async/Defer JavaScript
 * 
 * Adds async or defer attributes to specified JavaScript files.
 * 
 * @package WP_Performance_Booster
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Async_Defer_JS {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'script_loader_tag', array( $this, 'add_async_defer_attributes' ), 10, 3 );
    }
    
    /**
     * Add async/defer attributes to script tags
     */
    public function add_async_defer_attributes( $tag, $handle, $src ) {
        // Scripts to load with async
        $async_scripts = apply_filters( 'wp_async_scripts', array(
            'jquery',
            'jquery-migrate',
            'contact-form-7',
        ));
        
        // Scripts to load with defer
        $defer_scripts = apply_filters( 'wp_defer_scripts', array(
            'wp-embed',
            'wpcf7-recaptcha',
            'google-recaptcha',
        ));
        
        // Add async attribute
        if ( in_array( $handle, $async_scripts ) ) {
            return str_replace( ' src', ' async="async" src', $tag );
        }
        
        // Add defer attribute
        if ( in_array( $handle, $defer_scripts ) ) {
            return str_replace( ' src', ' defer="defer" src', $tag );
        }
        
        return $tag;
    }
}

// Initialize the class
new Async_Defer_JS();
