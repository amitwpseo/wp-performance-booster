<?php
/**
 * Disable Emojis and Embeds
 * 
 * Removes unnecessary WordPress core features that add extra HTTP requests
 * and bloat to the <head> section.
 * 
 * @package WP_Performance_Booster
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Disable_Emojis_Embeds {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Disable emojis
        add_action( 'init', array( $this, 'disable_emojis' ) );
        
        // Disable embeds
        add_action( 'init', array( $this, 'disable_embeds' ), 9999 );
    }
    
    /**
     * Disable WordPress emojis
     */
    public function disable_emojis() {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji'
