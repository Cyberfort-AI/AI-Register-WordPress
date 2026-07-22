<?php
namespace Cyberfort\AIRegister;

defined( 'ABSPATH' ) || exit;

class Settings {
    public function init(): void {
        add_action( 'admin_menu', [ $this, 'menu' ] );
        add_action( 'admin_init', [ $this, 'settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
        add_action( 'wp_ajax_cfair_test_connection', [ $this, 'test' ] );
        add_action( 'admin_post_cfair_clear_cache', [ $this, 'clear' ] );
        add_action( 'admin_post_cfair_create_page', [ $this, 'create_page' ] );
    }

    public function menu(): void {
        add_options_page( __( 'AI Register', 'cyberfort-ai-register' ), __( 'AI Register', 'cyberfort-ai-register' ), 'manage_options', 'cyberfort-ai-register', [ $this, 'page' ] );
    }

    public function settings(): void {
        $options = [
            'cfair_server_url'        => [ $this, 'url' ],
            'cfair_api_key'           => [ $this, 'key' ],
            'cfair_environment'       => [ $this, 'environment' ],
            'cfair_language'          => [ $this, 'language' ],
            'cfair_cache_duration'    => [ $this, 'cache_duration' ],
            'cfair_stale_retention'   => 'absint',
            'cfair_base_slug'         => 'sanitize_title',
            'cfair_register_page_id'  => 'absint',
            'cfair_layout'            => [ $this, 'layout' ],
            'cfair_per_page'          => [ $this, 'per_page' ],
            'cfair_show_search'       => 'absint',
            'cfair_show_filters'      => 'absint',
            'cfair_show_updated'      => 'absint',
            'cfair_show_attribution'  => 'absint',
            'cfair_accent_color'      => 'sanitize_hex_color',
            'cfair_noindex'           => 'absint',
            'cfair_delete_data'       => 'absint',
        ];
        foreach ( $options as $option => $callback ) {
            register_setting( 'cfair_settings', $option, [ 'sanitize_callback' => $callback ] );
        }
    }

    public function assets( string $hook ): void {
        if ( 'settings_page_cyberfort-ai-register' !== $hook ) return;
        wp_enqueue_style( 'cfair-admin', CFAIR_URL . 'assets/css/admin.css', [], CFAIR_VERSION );
        wp_enqueue_script( 'cfair-admin', CFAIR_URL . 'assets/js/admin.js', [], CFAIR_VERSION, true );
        wp_localize_script( 'cfair-admin', 'cfairAdmin', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'cfair_test_connection' ),
            'testing' => __( 'Testing connection…', 'cyberfort-ai-register' ),
            'success' => __( 'Connection successful.', 'cyberfort-ai-register' ),
            'failed' => __( 'Connection failed.', 'cyberfort-ai-register' ),
        ] );
    }

    public function url( $url ): string {
        $url = untrailingslashit( esc_url_raw( trim( (string) $url ) ) );
        $parts = wp_parse_url( $url );
        $development = 'development' === get_option( 'cfair_environment' );
        $local = in_array( strtolower( (string) ( $parts['host'] ?? '' ) ), [ 'localhost', '127.0.0.1', '::1' ], true );
        if ( ! $parts || empty( $parts['host'] ) || ! empty( $parts['query'] ) || ! empty( $parts['fragment'] ) || ( 'https' !== ( $parts['scheme'] ?? '' ) && ! ( $development && $local ) ) ) {
            add_settings_error( 'cfair_settings', 'cfair_url', __( 'Enter a valid HTTPS server URL without a query or fragment.', 'cyberfort-ai-register' ) );
            return (string) get_option( 'cfair_server_url' );
        }
        return $url;
    }

    public function key( $key ): string {
        $key = trim( (string) $key );
        if ( '' === $key ) return (string) get_option( 'cfair_api_key' );
        if ( strlen( $key ) < 24 || strlen( $key ) > 512 || ! preg_match( '/^[A-Za-z0-9_\-.~]+$/', $key ) ) {
            add_settings_error( 'cfair_settings', 'cfair_key', __( 'The connector API key format is invalid.', 'cyberfort-ai-register' ) );
            return (string) get_option( 'cfair_api_key' );
        }
        ( new Cache() )->clear();
        return $key;
    }

    public function environment( $value ): string { return in_array( $value, [ 'production', 'staging', 'development' ], true ) ? $value : 'production'; }
    public function language( $value ): string { return in_array( $value, [ 'auto', 'lv', 'en' ], true ) ? $value : 'auto'; }
    public function layout( $value ): string { return 'list' === $value ? 'list' : 'cards'; }
    public function per_page( $value ): int { return min( 100, max( 1, absint( $value ) ) ); }
    public function cache_duration( $value ): int { $allowed = [ 300, 900, 1800, 3600, 21600 ]; return in_array( absint( $value ), $allowed, true ) ? absint( $value ) : 900; }

    public function page(): void {
        if ( ! current_user_can( 'manage_options' ) ) return;
        require CFAIR_PATH . 'templates/admin/settings-page.php';
    }

    public function test(): void {
        check_ajax_referer( 'cfair_test_connection' );
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( [ 'message' => __( 'Unauthorized.', 'cyberfort-ai-register' ) ], 403 );
        $result = ( new Api_Client() )->test_connection();
        if ( isset( $result['error'] ) ) wp_send_json_error( [ 'message' => $result['error'], 'code' => $result['error_code'] ?? 'error' ], $result['status'] ?: 400 );
        wp_send_json_success( $result );
    }

    public function clear(): void {
        check_admin_referer( 'cfair_clear_cache' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'Unauthorized.', 'cyberfort-ai-register' ) );
        ( new Cache() )->clear();
        wp_safe_redirect( admin_url( 'options-general.php?page=cyberfort-ai-register&cleared=1' ) );
        exit;
    }

    public function create_page(): void {
        check_admin_referer( 'cfair_create_page' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'Unauthorized.', 'cyberfort-ai-register' ) );
        $existing = absint( get_option( 'cfair_register_page_id' ) );
        if ( $existing && get_post( $existing ) ) {
            wp_safe_redirect( get_edit_post_link( $existing, 'url' ) );
            exit;
        }
        $language = ( new Language() )->resolve( (string) get_option( 'cfair_language', 'auto' ) );
        $page_id = wp_insert_post( [
            'post_title' => 'lv' === $language ? 'MI sistēmu reģistrs' : 'AI Systems Register',
            'post_name' => sanitize_title( (string) get_option( 'cfair_base_slug', 'ai-register' ) ),
            'post_content' => '<!-- wp:cyberfort-ai-register/register /-->',
            'post_status' => 'publish',
            'post_type' => 'page',
        ], true );
        if ( ! is_wp_error( $page_id ) ) update_option( 'cfair_register_page_id', $page_id, false );
        flush_rewrite_rules();
        wp_safe_redirect( is_wp_error( $page_id ) ? admin_url( 'options-general.php?page=cyberfort-ai-register&page_error=1' ) : get_edit_post_link( $page_id, 'url' ) );
        exit;
    }
}
