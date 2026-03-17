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
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojis_tinymce' ) );
        add_filter( 'wp_resource_hints', array( $this, 'disable_emojis_remove_dns_prefetch' ), 10, 2 );
    }
    
    /**
     * Disable emojis in TinyMCE
     */
    public function disable_emojis_tinymce( $plugins ) {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, array( 'wpemoji' ) );
        }
        return array();
    }
    
    /**
     * Remove emoji CDN DNS prefetch
     */
    public function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
        if ( 'dns-prefetch' === $relation_type ) {
            $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/' );
            $urls = array_diff( $urls, array( $emoji_svg_url ) );
        }
        return $urls;
    }
    
    /**
     * Disable WordPress embeds
     */
    public function disable_embeds() {
        // Remove the REST API endpoint
        remove_action( 'rest_api_init', 'wp_oembed_register_route' );
        
        // Turn off oEmbed auto discovery
        add_filter( 'embed_oembed_discover', '__return_false' );
        
        // Remove oEmbed discovery links
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        
        // Remove oEmbed-specific JavaScript from the front-end
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        
        // Remove all embed-related filters
        remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
        
        // Remove the oEmbed rewrite rules
        add_filter( 'rewrite_rules_array', array( $this, 'disable_embeds_rewrites' ) );
        
        // Remove filter of the oEmbed result before any HTTP requests are made
        remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
        
        // Remove the oEmbed cache cleaning
        remove_action( 'save_post', 'wp_oembed_clear_cache_for_post' );
        
        // Remove the oEmbed AJAX endpoint
        remove_action( 'wp_ajax_oembed_cache', 'wp_oembed_cache' );
        remove_action( 'wp_ajax_nopriv_oembed_cache', 'wp_oembed_cache' );
        
        // Disable the oEmbed API from the REST API endpoints
        add_filter( 'rest_endpoints', array( $this, 'disable_embeds_rest_endpoints' ) );
    }
    
    /**
     * Remove embed rewrite rules
     */
    public function disable_embeds_rewrites( $rules ) {
        foreach ( $rules as $rule => $rewrite ) {
            if ( strpos( $rule, 'embed=true' ) !== false ) {
                unset( $rules[ $rule ] );
            }
        }
        return $rules;
    }
    
    /**
     * Remove oEmbed REST endpoints
     */
    public function disable_embeds_rest_endpoints( $endpoints ) {
        if ( isset( $endpoints['/oembed/1.0/embed'] ) ) {
            unset( $endpoints['/oembed/1.0/embed'] );
        }
        if ( isset( $endpoints['/oembed/1.0/proxy'] ) ) {
            unset( $endpoints['/oembed/1.0/proxy'] );
        }
        return $endpoints;
    }
}

// Initialize the class
new Disable_Emojis_Embeds();
