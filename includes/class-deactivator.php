<?php
namespace Cyberfort\AIRegister; defined( 'ABSPATH' ) || exit;
class Deactivator { public static function deactivate(): void { wp_clear_scheduled_hook( 'cfair_refresh_cache' ); flush_rewrite_rules(); } }
