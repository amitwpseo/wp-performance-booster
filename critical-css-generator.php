<?php
/**
 * Critical CSS Generator
 * 
 * Generates and inlines critical above-the-fold CSS for faster initial rendering.
 * Customize the CSS selectors based on your theme.
 * 
 * @package WP_Performance_Booster
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Critical_CSS_Generator {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_head', array( $this, 'inline_critical_css' ), 1 );
    }
    
    /**
     * Inline critical CSS based on current page type
     */
    public function inline_critical_css() {
        $critical_css = '';
        
        if ( is_front_page() || is_home() ) {
            $critical_css = $this->get_homepage_critical_css();
        } elseif ( is_single() ) {
            $critical_css = $this->get_single_post_critical_css();
        } elseif ( is_page() ) {
            $critical_css = $this->get_page_critical_css();
        } elseif ( is_archive() ) {
            $critical_css = $this->get_archive_critical_css();
        }
        
        if ( ! empty( $critical_css ) ) {
            echo '<style id="critical-css">' . $critical_css . '</style>';
        }
    }
    
    /**
     * Homepage critical CSS
     */
    private function get_homepage_critical_css() {
        return "
            /* Critical homepage CSS - Customize these selectors for your theme */
            header, .site-header, .header { display: block; width: 100%; }
            .hero, .hero-section, .slider { min-height: 500px; }
            .logo, .site-logo { max-width: 200px; }
            .menu, .navigation, .nav { display: flex; }
            .btn, .button, .cta-button { 
                background: #007cba; 
                color: #fff; 
                padding: 10px 20px; 
                border-radius: 4px; 
                text-decoration: none; 
                display: inline-block; 
            }
            h1, .page-title { font-size: 2.5em; margin-bottom: 20px; }
            p { margin-bottom: 15px; line-height: 1.6; }
        ";
    }
    
    /**
     * Single post critical CSS
     */
    private function get_single_post_critical_css() {
        return "
            /* Critical single post CSS */
            header, .site-header, .header { display: block; }
            .post-title, .entry-title { font-size: 2em; margin: 20px 0; }
            .post-meta, .entry-meta { font-size: 0.9em; color: #666; margin-bottom: 20px; }
            .post-content, .entry-content { font-size: 1.1em; line-height: 1.7; }
            .featured-image, .post-thumbnail { max-width: 100%; height: auto; margin-bottom: 20px; }
        ";
    }
    
    /**
     * Regular page critical CSS
     */
    private function get_page_critical_css() {
        return "
            /* Critical page CSS */
            header, .site-header, .header { display: block; }
            .page-title, .entry-title { font-size: 2.2em; margin: 20px 0; }
            .page-content, .entry-content { font-size: 1.1em; line-height: 1.7; }
        ";
    }
    
    /**
     * Archive page critical CSS
     */
    private function get_archive_critical_css() {
        return "
            /* Critical archive CSS */
            header, .site-header, .header { display: block; }
            .archive-title, .page-title { font-size: 2em; margin: 20px 0; }
            .post-grid, .posts-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
            .post-item { border: 1px solid #eee; padding: 15px; }
            .post-title { font-size: 1.3em; margin: 0 0 10px; }
        ";
    }
}

// Initialize the class
new Critical_CSS_Generator();
