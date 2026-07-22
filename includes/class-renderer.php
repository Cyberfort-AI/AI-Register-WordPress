<?php
namespace Cyberfort\AIRegister;

defined( 'ABSPATH' ) || exit;

class Renderer {
    private Api_Client $api;
    public function __construct() { $this->api = new Api_Client(); }

    public function register( array $options = [] ): string {
        wp_enqueue_style( 'cfair-public' );
        $defaults = [
            'language' => (string) get_option( 'cfair_language', 'auto' ),
            'layout' => (string) get_option( 'cfair_layout', 'cards' ),
            'search' => (bool) get_option( 'cfair_show_search', 1 ),
            'filters' => (bool) get_option( 'cfair_show_filters', 1 ),
            'show_updated' => (bool) get_option( 'cfair_show_updated', 1 ),
            'show_attribution' => (bool) get_option( 'cfair_show_attribution', 1 ),
            'per_page' => (int) get_option( 'cfair_per_page', 12 ),
        ];
        $options = wp_parse_args( $options, $defaults );
        $options['layout'] = 'list' === $options['layout'] ? 'list' : 'cards';
        $options['per_page'] = min( 100, max( 1, absint( $options['per_page'] ) ) );
        $params = [
            'language' => $options['language'],
            'page' => max( 1, absint( $_GET['cfair_page'] ?? 1 ) ),
            'per_page' => $options['per_page'],
            'search' => sanitize_text_field( wp_unslash( $_GET['cfair_search'] ?? '' ) ),
            'risk_tier' => sanitize_text_field( wp_unslash( $_GET['cfair_risk'] ?? '' ) ),
            'status' => sanitize_text_field( wp_unslash( $_GET['cfair_status'] ?? '' ) ),
        ];
        $result = $this->api->get_systems( $params );
        $branding = $this->api->get_branding();
        $taxonomies = $this->api->get_taxonomies();
        ob_start();
        cfair_template( 'register.php', compact( 'result', 'branding', 'taxonomies', 'options', 'params' ) );
        return (string) ob_get_clean();
    }

    public function system( string $reference, string $language = 'auto' ): string {
        wp_enqueue_style( 'cfair-public' );
        if ( ! preg_match( '/^[A-Za-z0-9_-]{1,80}$/', $reference ) ) return '';
        $result = $this->api->get_system( $reference, $language );
        $branding = $this->api->get_branding();
        ob_start();
        cfair_template( 'system.php', compact( 'result', 'branding', 'reference' ) );
        return (string) ob_get_clean();
    }
}
