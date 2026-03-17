<?php
/**
 * Remove Query Strings from Static Resources
 * 
 * Removes version query strings (e.g., ?ver=5.8) from CSS and JS files
 * to improve cache performance.
 * 
 * @package WP_Performance_Booster
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Remove_Query_Strings {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'style_loader_src', array( $this, 'remove_ver_query_string' ) );
        add_filter( 'script_loader_src', array( $this, 'remove_ver_query_string' ) );
    }
    
    /**
     * Remove version query string from URL
     */
    public function remove_ver_query_string( $src ) {
        if ( strpos( $src, '?ver=' ) ) {
            $src = remove_query_arg( 'ver', $src );
        }
        return $src;
    }
}

// Initialize the class
new Remove_Query_Strings();
