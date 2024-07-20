<?php
/**
 * Plugin Name: SEO Pinterest
 * Description: Plugin untuk mengelola redirection dari Pinterest ke website.
 * Version: 1.0
 * Author: @luffynas
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-seo-pinterest.php';
register_activation_hook( __FILE__, 'seo_pinterest_activate' );

function seo_pinterest_activate() {
    seo_pinterest_update_db_check();
}

function seo_pinterest_update_db_check() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'seo_pinterest_redirections';
    $charset_collate = $wpdb->get_charset_collate();

    $installed_ver = get_option( 'seo_pinterest_db_version' );
    $current_ver = '1.1'; // Ubah ini setiap kali skema tabel diperbarui

    if ( $installed_ver != $current_ver ) {
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            source varchar(255) NOT NULL,
            destination varchar(255) DEFAULT NULL,
            random_post BOOLEAN DEFAULT TRUE,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        update_option( 'seo_pinterest_db_version', $current_ver );
    }
}

new SEOPinterest();
