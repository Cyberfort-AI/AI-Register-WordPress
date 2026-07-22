<?php
namespace Cyberfort\AIRegister; defined( 'ABSPATH' ) || exit;
function cfair_mask_key( string $key ): string { return $key ? substr( $key, 0, min( 12, strlen( $key ) ) ) . '…' . substr( $key, -4 ) : __( 'Not configured', 'cyberfort-ai-register' ); }
function cfair_template( string $name, array $args = [] ): void { $file = locate_template( 'cyberfort-ai-register/' . $name ); $file = $file ?: CFAIR_PATH . 'templates/' . $name; if ( is_readable( $file ) ) { extract( $args, EXTR_SKIP ); include $file; } }
