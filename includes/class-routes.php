<?php
namespace Cyberfort\AIRegister;

defined( 'ABSPATH' ) || exit;

class Routes {
    public function init(): void {
        add_action( 'init', [ $this, 'register_rules' ] );
        add_filter( 'query_vars', [ $this, 'vars' ] );
        add_action( 'template_redirect', [ $this, 'render' ] );
        add_action( 'wp_head', [ $this, 'robots' ], 1 );
    }

    public function register_rules(): void {
        $slugs = array_filter( array_unique( [ sanitize_title( (string) get_option( 'cfair_base_slug', 'ai-register' ) ), 'mi-registrs' ] ) );
        foreach ( $slugs as $slug ) {
            add_rewrite_rule( '^' . preg_quote( $slug, '/' ) . '/?$', 'index.php?cfair_view=register', 'top' );
            add_rewrite_rule( '^' . preg_quote( $slug, '/' ) . '/([A-Za-z0-9_-]+)/?$', 'index.php?cfair_view=system&cfair_system_ref=$matches[1]', 'top' );
        }
    }

    public function vars( array $vars ): array { return array_merge( $vars, [ 'cfair_view', 'cfair_system_ref', 'cfair_language' ] ); }

    public function render(): void {
        $view = (string) get_query_var( 'cfair_view' );
        if ( ! in_array( $view, [ 'register', 'system' ], true ) ) return;
        $content = 'register' === $view
            ? ( new Renderer() )->register()
            : ( new Renderer() )->system( (string) get_query_var( 'cfair_system_ref' ), (string) get_query_var( 'cfair_language', 'auto' ) );
        if ( 'system' === $view && '' === $content ) {
            global $wp_query;
            $wp_query->set_404();
            status_header( 404 );
            nocache_headers();
            include get_404_template();
            exit;
        }
        status_header( 200 );
        get_header();
        echo '<main id="primary" class="site-main cfair-route">' . $content . '</main>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Renderer templates escape fields.
        get_footer();
        exit;
    }

    public function robots(): void {
        if ( get_query_var( 'cfair_view' ) && get_option( 'cfair_noindex' ) ) echo "<meta name=\"robots\" content=\"noindex,nofollow\">\n";
    }
}
