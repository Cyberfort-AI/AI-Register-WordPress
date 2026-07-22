<?php
namespace Cyberfort\AIRegister;

defined( 'ABSPATH' ) || exit;

class Activator {
    public static function activate(): void {
        if ( version_compare( get_bloginfo( 'version' ), '6.5', '<' ) ) {
            wp_die( esc_html__( 'Cyberfort AI Register requires WordPress 6.5 or later.', 'cyberfort-ai-register' ) );
        }
        $defaults = [
            'cfair_server_url'       => '',
            'cfair_environment'      => 'production',
            'cfair_api_version'      => 'v1',
            'cfair_language'         => 'auto',
            'cfair_cache_duration'   => 900,
            'cfair_stale_retention'  => WEEK_IN_SECONDS,
            'cfair_base_slug'        => 'ai-register',
            'cfair_register_page_id' => 0,
            'cfair_layout'           => 'cards',
            'cfair_per_page'         => 12,
            'cfair_show_search'      => 1,
            'cfair_show_filters'     => 1,
            'cfair_show_updated'     => 1,
            'cfair_show_attribution' => 1,
            'cfair_accent_color'     => '',
            'cfair_noindex'          => 0,
            'cfair_delete_data'      => 0,
        ];
        foreach ( $defaults as $key => $value ) add_option( $key, $value, '', false );
        add_option( 'cfair_api_key', '', '', false );
        add_option( 'cfair_schema_version', '2', '', false );
        ( new Routes() )->register_rules();
        flush_rewrite_rules();
        set_transient( 'cfair_activation_notice', 1, DAY_IN_SECONDS );
    }
}
