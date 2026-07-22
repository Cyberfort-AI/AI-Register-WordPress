<?php
namespace Cyberfort\AIRegister; defined( 'ABSPATH' ) || exit;
class Language { public function resolve( string $requested = 'auto' ): string { if ( in_array( $requested, [ 'lv', 'en' ], true ) ) return $requested; if ( has_filter( 'wpml_current_language' ) ) $locale = apply_filters( 'wpml_current_language', null ); elseif ( function_exists( 'pll_current_language' ) ) $locale = pll_current_language( 'slug' ); else $locale = determine_locale(); return str_starts_with( (string) $locale, 'lv' ) ? 'lv' : 'en'; } }
