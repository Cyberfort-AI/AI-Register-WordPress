<?php
namespace Cyberfort\AIRegister; defined( 'ABSPATH' ) || exit;
/** Stores only restrained, secret-free connection diagnostics. */
class Logger { public function log( array $event ): void { unset( $event['api_key'], $event['payload'], $event['ip'] ); update_option( 'cfair_last_diagnostic', array_map( 'sanitize_text_field', $event ) + [ 'timestamp' => gmdate( 'c' ) ], false ); } }
