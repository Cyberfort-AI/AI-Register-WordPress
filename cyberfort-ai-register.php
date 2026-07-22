<?php
/**
 * Plugin Name: Cyberfort AI Register for WordPress
 * Description: Read-only public AI-system register integration for Cyberfort AI Register.
 * Version: 1.0.0
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Text Domain: cyberfort-ai-register
 */
defined( 'ABSPATH' ) || exit;
if ( version_compare( PHP_VERSION, '8.1', '<' ) ) { add_action( 'admin_notices', static function () { echo '<div class="notice notice-error"><p>' . esc_html__( 'Cyberfort AI Register requires PHP 8.1 or later.', 'cyberfort-ai-register' ) . '</p></div>'; } ); return; }
define( 'CFAIR_VERSION', '1.0.0' ); define( 'CFAIR_FILE', __FILE__ ); define( 'CFAIR_PATH', plugin_dir_path( __FILE__ ) ); define( 'CFAIR_URL', plugin_dir_url( __FILE__ ) ); define( 'CFAIR_BASENAME', plugin_basename( __FILE__ ) );
spl_autoload_register( static function ( $class ) { $prefix = 'Cyberfort\\AIRegister\\'; if ( str_starts_with( $class, $prefix ) ) { $file = CFAIR_PATH . 'includes/class-' . strtolower( str_replace( '_', '-', substr( $class, strlen( $prefix ) ) ) ) . '.php'; if ( is_readable( $file ) ) require $file; } } );
register_activation_hook( __FILE__, [ 'Cyberfort\\AIRegister\\Activator', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'Cyberfort\\AIRegister\\Deactivator', 'deactivate' ] );
add_action( 'plugins_loaded', static function () { load_plugin_textdomain( 'cyberfort-ai-register', false, dirname( CFAIR_BASENAME ) . '/languages' ); ( new Cyberfort\AIRegister\Plugin() )->init(); } );
