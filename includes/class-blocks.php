<?php
namespace Cyberfort\AIRegister;

defined( 'ABSPATH' ) || exit;

class Blocks {
    public function init(): void {
        add_action( 'init', [ $this, 'register' ] );
        add_action( 'enqueue_block_editor_assets', [ $this, 'editor_assets' ] );
    }

    public function register(): void {
        if ( ! function_exists( 'register_block_type' ) ) return;
        register_block_type( CFAIR_PATH . 'blocks/register', [
            'render_callback' => static fn( array $attributes ): string => ( new Renderer() )->register( $attributes ),
        ] );
        register_block_type( CFAIR_PATH . 'blocks/system', [
            'render_callback' => static fn( array $attributes ): string => ( new Renderer() )->system(
                sanitize_text_field( (string) ( $attributes['reference'] ?? '' ) ),
                in_array( $attributes['language'] ?? 'auto', [ 'auto', 'lv', 'en' ], true ) ? $attributes['language'] : 'auto'
            ),
        ] );
    }

    public function editor_assets(): void {
        wp_enqueue_script(
            'cfair-blocks',
            CFAIR_URL . 'assets/js/blocks.js',
            [ 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n' ],
            CFAIR_VERSION,
            true
        );
    }
}
