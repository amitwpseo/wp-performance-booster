<?php
/**
 * Database Cleanup
 * 
 * Scheduled cleanup of post revisions, spam, transients, and expired options.
 * 
 * @package WP_Performance_Booster
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Database_Cleanup {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Schedule cleanup if not already scheduled
        add_action( 'wp', array( $this, 'schedule_cleanup' ) );
        
        // Hook the cleanup function to scheduled event
        add_action( 'wp_scheduled_database_cleanup', array( $this, 'perform_cleanup' ) );
        
        // Add admin bar menu for manual cleanup
        add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 100 );
        
        // Handle manual cleanup request
        add_action( 'admin_init', array( $this, 'handle_manual_cleanup' ) );
    }
    
    /**
     * Schedule daily cleanup
     */
    public function schedule_cleanup() {
        if ( ! wp_next_scheduled( 'wp_scheduled_database_cleanup' ) ) {
            wp_schedule_event( time(), 'daily', 'wp_scheduled_database_cleanup' );
        }
    }
    
    /**
     * Perform database cleanup
     */
    public function perform_cleanup() {
        global $wpdb;
        
        // Cleanup parameters
        $max_revisions = apply_filters( 'wp_db_max_revisions', 5 );
        $days_to_keep_trash = apply_filters( 'wp_db_days_keep_trash', 30 );
        
        // 1. Clean up old post revisions (keep only last $max_revisions per post)
        $revision_query = $wpdb->prepare(
            "DELETE a,b,c
            FROM $wpdb->posts a
            LEFT JOIN $wpdb->term_relationships b ON (a.ID = b.object_id)
            LEFT JOIN $wpdb->postmeta c ON (a.ID = c.post_id)
            WHERE a.post_type = %s
            AND a.post_modified < DATE_SUB(NOW(), INTERVAL %d DAY)
            AND (SELECT COUNT(*) FROM $wpdb->posts WHERE post_parent = a.post_parent AND post_type = 'revision' AND post_modified > a.post_modified) >= %d",
            'revision',
            $days_to_keep_trash,
            $max_revisions
        );
        $wpdb->query( $revision_query );
        
        // 2. Clean up auto-drafts
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft' AND post_modified < DATE_SUB(NOW(), INTERVAL %d DAY)",
                $days_to_keep_trash
            )
        );
        
        // 3. Clean up trashed posts
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $wpdb->posts WHERE post_status = 'trash' AND post_modified < DATE_SUB(NOW(), INTERVAL %d DAY)",
                $days_to_keep_trash
            )
        );
        
        // 4. Clean up expired transients
        $wpdb->query(
            "DELETE a, b
            FROM $wpdb->options a, $wpdb->options b
            WHERE a.option_name LIKE '\_transient\_%'
            AND a.option_name NOT LIKE '\_transient\_timeout\_%'
            AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
            AND b.option_value < UNIX_TIMESTAMP()"
        );
        
        $wpdb->query(
            "DELETE a, b
            FROM $wpdb->options a, $wpdb->options b
            WHERE a.option_name LIKE '\_site\_transient\_%'
            AND a.option_name NOT LIKE '\_site\_transient\_timeout\_%'
            AND b.option_name = CONCAT( '_site_transient_timeout_', SUBSTRING( a.option_name, 17 ) )
            AND b.option_value < UNIX_TIMESTAMP()"
        );
        
        // 5. Clean up spam comments
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $wpdb->comments WHERE comment_approved = 'spam' AND comment_date < DATE_SUB(NOW(), INTERVAL %d DAY)",
                $days_to_keep_trash
            )
        );
        
        // 6. Clean up orphaned postmeta
        $wpdb->query(
            "DELETE pm
            FROM $wpdb->postmeta pm
            LEFT JOIN $wpdb->posts wp ON wp.ID = pm.post_id
            WHERE wp.ID IS NULL"
        );
        
        // 7. Clean up orphaned termmeta
        $wpdb->query(
            "DELETE tm
            FROM $wpdb->termmeta tm
            LEFT JOIN $wpdb->terms t ON t.term_id = tm.term_id
            WHERE t.term_id IS NULL"
        );
        
        // 8. Optimize all tables
        $tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}%'" );
        foreach ( $tables as $table ) {
            $wpdb->query( "OPTIMIZE TABLE $table" );
        }
        
        // Log cleanup
        $this->log_cleanup();
    }
    
    /**
     * Log cleanup action
     */
    private function log_cleanup() {
        if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
            error_log( 'Database cleanup performed at ' . current_time( 'mysql' ) );
        }
    }
    
    /**
     * Add manual cleanup option to admin bar
     */
    public function add_admin_bar_menu( $wp_admin_bar ) {
        if ( current_user_can( 'manage_options' ) ) {
            $wp_admin_bar->add_node( array(
                'id'    => 'db-cleanup',
                'title' => 'Clean Database',
                'href'  => wp_nonce_url( admin_url( '?manual_db_cleanup=1' ), 'manual_db_cleanup' ),
                'meta'  => array( 'class' => 'db-cleanup-tool' )
            ));
        }
    }
    
    /**
     * Handle manual cleanup request
     */
    public function handle_manual_cleanup() {
        if ( isset( $_GET['manual_db_cleanup'] ) && 
             current_user_can( 'manage_options' ) && 
             wp_verify_nonce( $_GET['_wpnonce'], 'manual_db_cleanup' ) ) {
            
            $this->perform_cleanup();
            wp_redirect( add_query_arg( 'db_cleaned', '1', wp_get_referer() ) );
            exit;
        }
    }
}

// Initialize the class
new Database_Cleanup();
