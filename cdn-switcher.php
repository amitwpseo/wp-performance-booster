<?php
/**
 * CDN Switcher
 * 
 * Rewrites media URLs to use a CDN for faster global delivery.
 * 
 * @package WP_Performance_Booster
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CDN_Switcher {
    
    /**
     * CDN URL
     */
    private $cdn_url;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Get CDN URL from settings or define constant
        if ( defined( 'WP_CDN_URL' ) ) {
            $this->cdn_url = WP_CDN_URL;
        } else {
            $this->cdn_url = get_option( 'cdn_url', '' );
        }
        
        // Add admin menu
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        
        // Hook into content filters
        add_filter( 'wp_get_attachment_url', array( $this, 'rewrite_cdn_url' ) );
        add_filter( 'wp_get_attachment_image_src', array( $this, 'rewrite_image_src' ) );
        add_filter( 'the_content', array( $this, 'rewrite_content_urls' ) );
        add_filter( 'wp_calculate_image_srcset', array( $this, 'rewrite_srcset_urls' ) );
        
        // Admin scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'CDN Settings',
            'CDN Switcher',
            'manage_options',
            'cdn-switcher',
            array( $this, 'settings_page' )
        );
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        // Save settings
        if ( isset( $_POST['submit'] ) && check_admin_referer( 'cdn_settings' ) ) {
            update_option( 'cdn_url', esc_url_raw( $_POST['cdn_url'] ) );
            echo '<div class="notice notice-success"><p>CDN URL saved!</p></div>';
        }
        
        $current_cdn = get_option( 'cdn_url', '' );
        ?>
        <div class="wrap">
            <h1>CDN Switcher Settings</h1>
            <form method="post">
                <?php wp_nonce_field( 'cdn_settings' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">CDN URL</th>
                        <td>
                            <input type="url" name="cdn_url" value="<?php echo esc_url( $current_cdn ); ?>" class="regular-text" placeholder="https://cdn.example.com">
                            <p class="description">Enter your CDN URL without trailing slash (e.g., https://cdn.yourdomain.com)</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" class="button-primary" value="Save Changes">
                </p>
            </form>
            
            <?php if ( $current_cdn ) : ?>
            <div class="card" style="max-width: 100%; padding: 20px; margin-top: 20px;">
                <h2>Status: Active ✓</h2>
                <p>All media URLs are being rewritten to: <code><?php echo esc_url( $current_cdn ); ?></code></p>
                
                <h3>Test Your CDN</h3>
                <p>Sample image URL (original vs CDN):</p>
                <?php
                $sample_image = wp_get_attachment_url( get_option( 'site_icon' ) );
                if ( $sample_image ) {
                    $cdn_image = $this->rewrite_url( $sample_image );
                    echo "<p><strong>Original:</strong> " . esc_url( $sample_image ) . "</p>";
                    echo "<p><strong>CDN:</strong> " . esc_url( $cdn_image ) . "</p>";
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Admin scripts
     */
    public function admin_scripts( $hook ) {
        if ( $hook !== 'settings_page_cdn-switcher' ) {
            return;
        }
        ?>
        <style>
        .card { background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; }
        </style>
        <?php
    }
    
    /**
     * Rewrite a single URL
     */
    private function rewrite_url( $url ) {
        if ( empty( $this->cdn_url ) ) {
            return $url;
        }
        
        $site_url = get_site_url();
        $rewritten_url = str_replace( $site_url, $this->cdn_url, $url );
        
        return $rewritten_url;
    }
    
    /**
     * Rewrite attachment URL
     */
    public function rewrite_cdn_url( $url ) {
        return $this->rewrite_url( $url );
    }
    
    /**
     * Rewrite image src array
     */
    public function rewrite_image_src( $image ) {
        if ( is_array( $image ) && isset( $image[0] ) ) {
            $image[0] = $this->rewrite_url( $image[0] );
        }
        return $image;
    }
    
    /**
     * Rewrite URLs in content
     */
    public function rewrite_content_urls( $content ) {
        if ( empty( $this->cdn_url ) ) {
            return $content;
        }
        
        $site_url = get_site_url();
        $content = str_replace( $site_url, $this->cdn_url, $content );
        
        return $content;
    }
    
    /**
     * Rewrite srcset URLs
     */
    public function rewrite_srcset_urls( $sources ) {
        if ( empty( $this->cdn_url ) ) {
            return $sources;
        }
        
        foreach ( $sources as &$source ) {
            if ( isset( $source['url'] ) ) {
                $source['url'] = $this->rewrite_url( $source['url'] );
            }
        }
        
        return $sources;
    }
}

// Initialize the class
new CDN_Switcher();
