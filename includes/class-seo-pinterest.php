<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class SEOPinterest {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'template_redirect', array( $this, 'handle_redirections' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_save_redirection', array( $this, 'ajax_save_redirection' ) );
        add_action( 'wp_ajax_delete_redirection', array( $this, 'ajax_delete_redirection' ) );
        add_action( 'plugins_loaded', array( $this, 'check_db_update' ) );
    }

    public function check_db_update() {
        seo_pinterest_update_db_check();
    }
    
    public function add_admin_menu() {
        add_menu_page( 'SEO Pinterest', 'SEO Pinterest', 'manage_options', 'seo-pinterest', array( $this, 'seo_pinterest_page' ) );
        add_submenu_page( 'seo-pinterest', 'Redirections', 'Redirections', 'manage_options', 'seo-pinterest-redirections', array( $this, 'redirections_page' ) );
        add_submenu_page( 'seo-pinterest', 'About Us', 'About Us', 'manage_options', 'seo-pinterest-about', array( $this, 'about_page' ) );
    }

    public function register_settings() {
        register_setting( 'seo_pinterest_options', 'seo_pinterest_redirections' );
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' );
        wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true );
        wp_enqueue_style( 'seo-pinterest-css', plugin_dir_url( __FILE__ ) . 'css/seo-pinterest.css' );
        wp_enqueue_script( 'seo-pinterest-js', plugin_dir_url( __FILE__ ) . 'js/seo-pinterest.js', array( 'jquery' ), null, true );
        wp_localize_script( 'seo-pinterest-js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
    }

    public function ajax_save_redirection() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_pinterest_redirections';
    
        if ( isset($_POST['redirection_name']) && isset($_POST['source_redirection']) && (isset($_POST['destination_redirection']) || isset($_POST['random_post'])) ) {
            $random_post = isset($_POST['random_post']) ? filter_var($_POST['random_post'], FILTER_VALIDATE_BOOLEAN) : true;
            $destination = $random_post ? null : esc_url_raw( $_POST['destination_redirection'] );
    
            $result = $wpdb->insert(
                $table_name,
                array(
                    'name' => sanitize_text_field( $_POST['redirection_name'] ),
                    'source' => esc_url_raw( $_POST['source_redirection'] ),
                    'destination' => $destination,
                    'random_post' => $random_post,
                ),
                array(
                    '%s',
                    '%s',
                    $destination === null ? 'NULL' : '%s',
                    '%d'
                )
            );
    
            if ($result !== false) {
                wp_send_json_success();
            } else {
                wp_send_json_error('Gagal menyimpan redirection.');
            }
        } else {
            wp_send_json_error('Data tidak lengkap.');
        }
    }
    
    public function ajax_delete_redirection() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_pinterest_redirections';
    
        if ( isset($_POST['id']) ) {
            $result = $wpdb->delete(
                $table_name,
                array( 'id' => intval( $_POST['id'] ) ),
                array( '%d' )
            );
    
            if ($result !== false) {
                wp_send_json_success();
            } else {
                wp_send_json_error('Gagal menghapus redirection.');
            }
        } else {
            wp_send_json_error('ID tidak ditemukan.');
        }
    }

    public function seo_pinterest_page() {
        $view_file = plugin_dir_path( dirname( __FILE__ ) ) . 'views/seo-pinterest-page.php';
        if ( file_exists( $view_file ) ) {
            include $view_file;
        } else {
            echo '<div class="notice notice-error"><p>File views/seo-pinterest-page.php tidak ditemukan.</p></div>';
        }
    }

    public function redirections_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_pinterest_redirections';

        if ( isset($_POST['save_redirection']) ) {
            $this->save_redirection();
        } elseif ( isset($_POST['delete_redirection']) ) {
            $this->delete_redirection( intval( $_POST['delete_redirection'] ) );
        }

        $redirections = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

        $view_file = plugin_dir_path( dirname( __FILE__ ) ) . 'views/redirections-page.php';
        if ( file_exists( $view_file ) ) {
            include $view_file;
        } else {
            echo '<div class="notice notice-error"><p>File views/redirections-page.php tidak ditemukan.</p></div>';
        }
    }
    
    public function delete_redirection( $id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_pinterest_redirections';
    
        $wpdb->delete(
            $table_name,
            array( 'id' => $id ),
            array( '%d' )
        );
    
        wp_redirect( admin_url( 'admin.php?page=seo-pinterest-redirections&message=redirection_deleted' ) );
        exit;
    }

    public function about_page() {
        $view_file = plugin_dir_path( dirname( __FILE__ ) ) . 'views/about-page.php';
        if ( file_exists( $view_file ) ) {
            include $view_file;
        } else {
            echo '<div class="notice notice-error"><p>File views/about-page.php tidak ditemukan.</p></div>';
        }
    }

    public function save_redirection() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_pinterest_redirections';
    
        if ( isset($_POST['redirection_name']) && isset($_POST['source_redirection']) && isset($_POST['destination_redirection']) ) {
            $wpdb->insert(
                $table_name,
                array(
                    'name' => sanitize_text_field( $_POST['redirection_name'] ),
                    'source' => esc_url_raw( $_POST['source_redirection'] ),
                    'destination' => esc_url_raw( $_POST['destination_redirection'] ),
                )
            );
    
            wp_safe_redirect( admin_url( 'admin.php?page=seo-pinterest-redirections&message=redirection_saved' ) );
            exit;
        }
    }

    public function handle_redirections() {
        if ( is_admin() || is_user_logged_in() ) return;
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'seo_pinterest_redirections';
        $redirections = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
        
        $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
        foreach ( $redirections as $redirection ) {
            if ( trailingslashit( $current_url ) == trailingslashit( $redirection['source'] ) ) {
                if ( ! $this->is_bot() ) {
                    if ( $redirection['random_post'] ) {
                        $random_post = get_posts(array(
                            'post_type' => 'post',
                            'posts_per_page' => 1,
                            'orderby' => 'rand'
                        ));
                        if ( !empty($random_post) ) {
                            wp_redirect( get_permalink( $random_post[0]->ID ), 301 );
                            exit;
                        }
                    } elseif ( !is_null( $redirection['destination'] ) ) {
                        wp_redirect( $redirection['destination'], 301 );
                        exit;
                    }
                }
            }
        }
    }

    public function is_bot() {
        $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $bots = array( 'Googlebot', 'Pinterest', 'Bingbot', 'Yahoo! Slurp', 'DuckDuckBot', 'Baiduspider', 'YandexBot', 'Sogou', 'Exabot', 'facebot', 'ia_archiver' );

        foreach ( $bots as $bot ) {
            if ( stripos( $user_agent, $bot ) !== false ) {
                return true;
            }
        }
        return false;
    }
}
