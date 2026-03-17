<?php
/**
 * Lazy Load Optimization
 * 
 * Adds lazy loading to images, iframes, and videos with JavaScript fallback.
 * 
 * @package WP_Performance_Booster
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Lazy_Load_Optimizer {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'wp_content_img_tag', array( $this, 'add_lazy_loading_to_images' ), 10, 3 );
        add_filter( 'the_content', array( $this, 'add_lazy_loading_to_iframes' ) );
        add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_lazy_attributes_to_featured_images' ) );
        add_action( 'wp_footer', array( $this, 'add_lazy_load_script' ) );
    }
    
    /**
     * Add lazy loading to images in content
     */
    public function add_lazy_loading_to_images( $filtered_image, $context, $attachment_id ) {
        // Skip if image already has loading attribute
        if ( strpos( $filtered_image, 'loading=' ) !== false ) {
            return $filtered_image;
        }
        
        // Add loading="lazy" attribute
        $filtered_image = str_replace( '<img ', '<img loading="lazy" ', $filtered_image );
        
        return $filtered_image;
    }
    
    /**
     * Add lazy loading to iframes in content
     */
    public function add_lazy_loading_to_iframes( $content ) {
        if ( is_admin() ) {
            return $content;
        }
        
        // Skip if no iframes
        if ( strpos( $content, '<iframe' ) === false ) {
            return $content;
        }
        
        // Add loading="lazy" to all iframes without loading attribute
        $content = preg_replace( 
            '/<iframe(?!.*loading=)/i', 
            '<iframe loading="lazy"', 
            $content 
        );
        
        return $content;
    }
    
    /**
     * Add lazy attributes to featured images
     */
    public function add_lazy_attributes_to_featured_images( $attr ) {
        if ( isset( $attr['src'] ) && ! isset( $attr['loading'] ) ) {
            $attr['loading'] = 'lazy';
        }
        return $attr;
    }
    
    /**
     * Add JavaScript fallback for older browsers
     */
    public function add_lazy_load_script() {
        ?>
        <script>
        (function() {
            // Check if browser supports native lazy loading
            if ('loading' in HTMLImageElement.prototype) {
                // Browser supports native lazy loading - do nothing
                return;
            }
            
            // Fallback for browsers that don't support native lazy loading
            var images = document.querySelectorAll('img[loading="lazy"], iframe[loading="lazy"]');
            
            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            var lazyElement = entry.target;
                            
                            // If it's an image with data-src, swap it
                            if (lazyElement.tagName === 'IMG' && lazyElement.dataset.src) {
                                lazyElement.src = lazyElement.dataset.src;
                            }
                            
                            lazyElement.removeAttribute('loading');
                            observer.unobserve(lazyElement);
                        }
                    });
                });
                
                images.forEach(function(image) {
                    // Store original src in data-src
                    if (image.tagName === 'IMG' && image.src) {
                        image.dataset.src = image.src;
                        image.src = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
                    }
                    
                    observer.observe(image);
                });
            }
        })();
        </script>
        <?php
    }
}

// Initialize the class
new Lazy_Load_Optimizer();
