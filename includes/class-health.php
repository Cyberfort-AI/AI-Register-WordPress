<?php
namespace Cyberfort\AIRegister;

defined( 'ABSPATH' ) || exit;

class Health {
    public function report(): array {
        $last_error = get_option( 'cfair_last_error', '' );
        if ( is_array( $last_error ) ) {
            $last_error = trim( (string) ( $last_error['code'] ?? '' ) . ': ' . (string) ( $last_error['message'] ?? '' ) );
        }
        $theme = wp_get_theme();
        return [
            'plugin_version'    => CFAIR_VERSION,
            'wordpress_version' => get_bloginfo( 'version' ),
            'php_version'       => PHP_VERSION,
            'theme'             => $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ),
            'multilingual'      => defined( 'ICL_SITEPRESS_VERSION' ) ? 'WPML' : ( function_exists( 'pll_current_language' ) ? 'Polylang' : 'WordPress locale' ),
            'server_url'        => (string) get_option( 'cfair_server_url' ),
            'environment'       => (string) get_option( 'cfair_environment', 'production' ),
            'api_version'       => (string) get_option( 'cfair_api_version', 'v1' ),
            'masked_key'        => cfair_mask_key( (string) get_option( 'cfair_api_key' ) ),
            'last_success'      => (string) get_option( 'cfair_last_success', __( 'Never', 'cyberfort-ai-register' ) ),
            'last_error'        => $last_error ?: __( 'None', 'cyberfort-ai-register' ),
            'permalink_mode'    => get_option( 'permalink_structure' ) ? 'pretty' : 'plain',
            'tls_required'      => 'production' === get_option( 'cfair_environment', 'production' ) ? 'yes' : 'development override possible',
        ];
    }
}
