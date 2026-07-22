<?php
namespace Cyberfort\AIRegister; defined( 'ABSPATH' ) || exit;
class Health { public function report(): array { return [ 'plugin_version' => CFAIR_VERSION, 'wordpress_version' => get_bloginfo( 'version' ), 'php_version' => PHP_VERSION, 'server_url' => (string) get_option( 'cfair_server_url' ), 'masked_key' => cfair_mask_key( (string) get_option( 'cfair_api_key' ) ), 'last_error' => (string) get_option( 'cfair_last_error' ) ]; } }
