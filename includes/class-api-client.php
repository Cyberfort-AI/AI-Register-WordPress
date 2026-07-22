<?php
namespace Cyberfort\AIRegister;

defined( 'ABSPATH' ) || exit;

class Api_Client {
    private const MAX_RESPONSE_BYTES = 1048576;
    private Cache $cache;
    private Logger $logger;

    public function __construct() {
        $this->cache  = new Cache();
        $this->logger = new Logger();
    }

    public function get_tenant( bool $force = false ): array { return $this->request( 'tenant', [], $force ); }
    public function get_taxonomies(): array { return $this->request( 'taxonomies' ); }
    public function get_branding(): array { return $this->request( 'branding' ); }
    public function get_systems( array $params = [] ): array { return $this->request( 'systems', $params ); }
    public function get_system( string $reference, string $language = 'auto' ): array {
        return $this->request( 'systems/' . rawurlencode( $reference ), [ 'language' => $language ] );
    }
    public function test_connection(): array { return $this->get_tenant( true ); }

    private function request( string $endpoint, array $params = [], bool $force = false ): array {
        $language = ( new Language() )->resolve( (string) ( $params['language'] ?? get_option( 'cfair_language', 'auto' ) ) );
        $params['language'] = $language;
        $cache_key = $this->cache->key( str_replace( '/', '_', $endpoint ), $params, $language );
        $cached = $this->cache->get( $cache_key );
        if ( ! $force && $cached ) {
            return $cached + [ 'cache' => 'fresh' ];
        }

        $api_key = trim( (string) get_option( 'cfair_api_key' ) );
        if ( '' === $api_key ) {
            return [ 'error' => __( 'Connector API key is not configured.', 'cyberfort-ai-register' ), 'error_code' => 'missing_api_key' ];
        }

        $url = $this->url( $endpoint, $params );
        if ( is_wp_error( $url ) ) {
            return [ 'error' => $url->get_error_message(), 'error_code' => $url->get_error_code() ];
        }

        $headers = [
            'Authorization'               => 'Bearer ' . $api_key,
            'Accept'                      => 'application/json',
            'User-Agent'                  => 'Cyberfort-AI-Register-WordPress/' . CFAIR_VERSION . '; ' . home_url(),
            'X-AIRegister-Plugin-Version' => CFAIR_VERSION,
            'X-AIRegister-Site-URL'       => home_url(),
            'X-AIRegister-Language'       => $language,
        ];
        $conditional = $cached ?: $this->cache->stale( $cache_key );
        if ( ! empty( $conditional['etag'] ) ) {
            $headers['If-None-Match'] = (string) $conditional['etag'];
        }

        $start = microtime( true );
        $response = wp_remote_get( $url, [
            'timeout'     => 10,
            'redirection' => 0,
            'sslverify'   => true,
            'headers'     => $headers,
        ] );
        $latency = (int) round( ( microtime( true ) - $start ) * 1000 );

        if ( is_wp_error( $response ) ) {
            return $this->fallback( $cache_key, $response->get_error_message(), $response->get_error_code(), 0, $endpoint, $latency );
        }

        $status     = (int) wp_remote_retrieve_response_code( $response );
        $request_id = sanitize_text_field( (string) wp_remote_retrieve_header( $response, 'x-request-id' ) );

        if ( 304 === $status && $conditional ) {
            $conditional['timestamp'] = time();
            $conditional['latency_ms'] = $latency;
            $conditional['request_id'] = $request_id;
            $this->cache->put( $cache_key, $conditional );
            $this->record_success( $endpoint, 304, $latency, $request_id );
            return $conditional + [ 'cache' => 'revalidated' ];
        }

        $body = (string) wp_remote_retrieve_body( $response );
        if ( strlen( $body ) > self::MAX_RESPONSE_BYTES ) {
            return $this->fallback( $cache_key, __( 'The AI Register response was too large.', 'cyberfort-ai-register' ), 'response_too_large', $status, $endpoint, $latency, $request_id );
        }

        $decoded = json_decode( $body, true );
        if ( 200 !== $status ) {
            $remote_error = is_array( $decoded ) ? ( $decoded['error'] ?? [] ) : [];
            $code = sanitize_key( (string) ( $remote_error['code'] ?? 'remote_error' ) );
            $message = sanitize_text_field( (string) ( $remote_error['message'] ?? sprintf( __( 'AI Register returned HTTP %d.', 'cyberfort-ai-register' ), $status ) ) );
            $no_stale = in_array( $status, [ 401, 403, 404 ], true );
            return $this->fallback( $cache_key, $message, $code, $status, $endpoint, $latency, $request_id, $no_stale );
        }

        if ( ! is_array( $decoded ) ) {
            return $this->fallback( $cache_key, __( 'Malformed API response.', 'cyberfort-ai-register' ), 'malformed_response', $status, $endpoint, $latency, $request_id );
        }

        $result = [
            'data'       => $this->safe_data( $decoded ),
            'timestamp'  => time(),
            'etag'       => sanitize_text_field( (string) wp_remote_retrieve_header( $response, 'etag' ) ),
            'latency_ms' => $latency,
            'request_id' => $request_id,
        ];
        $this->cache->put( $cache_key, $result );
        delete_option( 'cfair_last_error' );
        update_option( 'cfair_last_success', gmdate( 'c' ), false );
        $this->record_success( $endpoint, 200, $latency, $request_id );
        return $result + [ 'cache' => 'miss' ];
    }

    private function url( string $endpoint, array $params ): string|\WP_Error {
        $server = untrailingslashit( (string) get_option( 'cfair_server_url' ) );
        $parts  = wp_parse_url( $server );
        $dev    = 'development' === get_option( 'cfair_environment' ) && defined( 'CFAIR_ALLOW_INSECURE_LOCALHOST' ) && CFAIR_ALLOW_INSECURE_LOCALHOST;
        $local  = in_array( strtolower( (string) ( $parts['host'] ?? '' ) ), [ 'localhost', '127.0.0.1', '::1' ], true );
        if ( ! $parts || ! empty( $parts['query'] ) || ! empty( $parts['fragment'] ) || empty( $parts['host'] ) || ( 'https' !== ( $parts['scheme'] ?? '' ) && ! ( $dev && $local ) ) ) {
            return new \WP_Error( 'invalid_server_url', __( 'A valid HTTPS AI Register server URL is required.', 'cyberfort-ai-register' ) );
        }
        $allowed = array_flip( [ 'language', 'page', 'per_page', 'search', 'risk_tier', 'status', 'updated_after' ] );
        $params = array_intersect_key( $params, $allowed );
        $params['page'] = isset( $params['page'] ) ? max( 1, absint( $params['page'] ) ) : null;
        $params['per_page'] = isset( $params['per_page'] ) ? min( 100, max( 1, absint( $params['per_page'] ) ) ) : null;
        $params = array_filter( $params, static fn( $value ) => null !== $value && '' !== $value );
        return add_query_arg( $params, $server . '/api/public/' . sanitize_key( (string) get_option( 'cfair_api_version', 'v1' ) ) . '/' . ltrim( $endpoint, '/' ) );
    }

    private function safe_data( array $data ): array {
        return json_decode( wp_json_encode( $data ), true ) ?: [];
    }

    private function fallback( string $key, string $error, string $code, int $status, string $endpoint, int $latency, string $request_id = '', bool $no_stale = false ): array {
        update_option( 'cfair_last_error', [ 'message' => sanitize_text_field( $error ), 'code' => sanitize_key( $code ), 'status' => $status, 'at' => gmdate( 'c' ) ], false );
        $this->logger->log( [ 'endpoint' => $endpoint, 'status' => $status, 'duration_ms' => $latency, 'error_code' => $code, 'request_id' => $request_id ] );
        if ( ! $no_stale && ( $stale = $this->cache->stale( $key ) ) ) {
            return $stale + [ 'cache' => 'stale', 'warning' => sanitize_text_field( $error ) ];
        }
        return [ 'error' => sanitize_text_field( $error ), 'error_code' => sanitize_key( $code ), 'status' => $status, 'request_id' => $request_id ];
    }

    private function record_success( string $endpoint, int $status, int $latency, string $request_id ): void {
        $this->logger->log( [ 'endpoint' => $endpoint, 'status' => $status, 'duration_ms' => $latency, 'cache' => 304 === $status ? 'revalidated' : 'remote', 'request_id' => $request_id ] );
    }
}
