<?php
/**
 * GZIP Compression
 * 
 * Enables GZIP compression via PHP as fallback for .htaccess.
 * 
 * @package WP_Performance_Booster
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class GZIP_Compression {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Check if compression is already enabled
        if ( $this->is_compression_enabled() ) {
            return;
        }
        
        // Enable output buffering with compression
        add_action( 'init', array( $this, 'start_output_buffer' ), 1 );
    }
    
    /**
     * Check if compression is already enabled
     */
    private function is_compression_enabled() {
        // Check if zlib compression is active
        if ( ini_get( 'zlib.output_compression' ) ) {
            return true;
        }
        
        // Check if Apache has mod_deflate
        if ( function_exists( 'apache_get_modules' ) ) {
            if ( in_array( 'mod_deflate', apache_get_modules() ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Start output buffer with compression
     */
    public function start_output_buffer() {
        // Check if compression is supported by browser
        if ( $this->client_supports_compression() ) {
            ob_start( 'ob_gzhandler' );
        }
    }
    
    /**
     * Check if client supports compression
     */
    private function client_supports_compression() {
        if ( isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) ) {
            if ( strpos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) !== false ) {
                return true;
            }
            if ( strpos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate' ) !== false ) {
                return true;
            }
        }
        return false;
    }
}

// Initialize the class
new GZIP_Compression();
