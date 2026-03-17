<?php
/**
 * Preload Critical Assets
 * 
 * Preloads fonts, key APIs, and above-the-fold images for faster rendering.
 * 
 * @package WP_Performance_Booster
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Preload_Critical_Assets {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_head', array( $this, 'add_preload_links' ), 2 );
        add_filter( 'wp_resource_hints', array( $this, 'add_preconnect_hints' ), 10, 2 );
    }
    
    /**
     * Add preload links to head
     */
    public function add_preload_links() {
        // Preload fonts
        $this->preload_fonts();
        
        // Preload critical images
        $this->preload_critical_images();
        
        // Preload CSS (if you have critical CSS file)
        $this->preload_critical_css();
    }
    
    /**
     * Preload fonts
     */
    private function preload_fonts() {
        // Get customizer or theme fonts
        $fonts = apply_filters( 'wp_preload_fonts', array(
            array(
                'url' => get_template_directory_uri() . '/fonts/main.woff2',
                'type' => 'font/woff2',
                'as' => 'font',
                'crossorigin' => true
            ),
            // Add more fonts as needed
        ));
        
        foreach ( $fonts as $font ) {
            $this->render_preload_link( $font );
        }
    }
    
    /**
     * Preload critical above-the-fold images
     */
    private function preload_critical_images() {
        // Get hero image if on front page
        if ( is_front_page() ) {
            $hero_image = $this->get_hero_image();
            if ( $hero_image ) {
                $this->render_preload_link( array(
                    'url' => $hero_image,
                    'as' => 'image',
                    'type' => 'image/webp'
                ));
            }
        }
        
        // Preload logo
        $logo = $this->get_logo_url();
        if ( $logo ) {
            $this->render_preload_link( array(
                'url' => $logo,
                'as' => 'image',
                'type' => 'image/webp'
            ));
        }
    }
    
    /**
     * Preload critical CSS file
     */
    private function preload_critical_css() {
        $critical_css_url = apply_filters( 'wp_critical_css_url', '' );
        if ( $critical_css_url ) {
            $this->render_preload_link( array(
                'url' => $critical_css_url,
                'as' => 'style',
                'type' => 'text/css'
            ));
        }
    }
    
    /**
     * Render preload link tag
     */
    private function render_preload_link( $asset ) {
        $output = '<link rel="preload" href="' . esc_url( $asset['url'] ) . '" as="' . esc_attr( $asset['as'] ) . '"';
        
        if ( isset( $asset['type'] ) ) {
            $output .= ' type="' . esc_attr( $asset['type'] ) . '"';
        }
        
        if ( isset( $asset['crossorigin'] ) && $asset['crossorigin'] ) {
            $output .= ' crossorigin';
        }
        
        $output .= '>' . "\n";
        
        echo $output;
    }
    
    /**
     * Get hero image URL
     */
    private function get_hero_image() {
        // Try to get featured image of homepage
        $homepage_id = get_option( 'page_on_front' );
        if ( $homepage_id && has_post_thumbnail( $homepage_id ) ) {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $homepage_id ), 'full' );
            if ( $image ) {
                return $image[0];
            }
        }
        
        // Return default hero if no featured image
        return apply_filters( 'wp_default_hero_image', '' );
    }
    
    /**
     * Get logo URL
     */
    private function get_logo_url() {
        if ( has_custom_logo() ) {
            $logo_id = get_theme_mod( 'custom_logo' );
            $logo = wp_get_attachment_image_src( $logo_id, 'full' );
            if ( $logo ) {
                return $logo[0];
            }
        }
        return '';
    }
    
    /**
     * Add preconnect hints for third-party domains
     */
    public function add_preconnect_hints( $urls, $relation_type ) {
        if ( 'preconnect' !== $relation_type ) {
            return $urls;
        }
        
        // Add preconnect for common third-party services
        $preconnects = apply_filters( 'wp_preconnect_domains', array(
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
            'https://www.google-analytics.com',
            'https://connect.facebook.net'
        ));
        
        foreach ( $preconnects as $domain ) {
            $urls[] = array(
                'href' => $domain,
                'crossorigin' => true,
            );
        }
        
        return $urls;
    }
}

// Initialize the class
new Preload_Critical_Assets();
