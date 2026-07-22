<?php defined( 'ABSPATH' ) || exit; $health = ( new Cyberfort\AIRegister\Health() )->report(); ?>
<div class="wrap cfair-admin">
<h1><?php esc_html_e( 'Cyberfort AI Register', 'cyberfort-ai-register' ); ?></h1>
<p><?php esc_html_e( 'Connect this WordPress site to one AI Register tenant and publish its approved public AI systems.', 'cyberfort-ai-register' ); ?></p>
<?php settings_errors( 'cfair_settings' ); ?>
<?php if ( isset( $_GET['cleared'] ) ) : ?><div class="notice notice-success inline"><p><?php esc_html_e( 'Cache cleared.', 'cyberfort-ai-register' ); ?></p></div><?php endif; ?>
<div class="cfair-admin__grid">
<div class="cfair-admin__main">
<form method="post" action="options.php">
<?php settings_fields( 'cfair_settings' ); ?>
<h2><?php esc_html_e( 'Connection', 'cyberfort-ai-register' ); ?></h2>
<table class="form-table" role="presentation">
<tr><th><label for="cfair_server_url"><?php esc_html_e( 'AI Register server address', 'cyberfort-ai-register' ); ?></label></th><td><input class="regular-text" type="url" id="cfair_server_url" name="cfair_server_url" value="<?php echo esc_attr( get_option( 'cfair_server_url' ) ); ?>" placeholder="https://airegister.cyberfort.lv"></td></tr>
<tr><th><label for="cfair_api_key"><?php esc_html_e( 'Connector API key', 'cyberfort-ai-register' ); ?></label></th><td><input class="regular-text" type="password" id="cfair_api_key" name="cfair_api_key" value="" autocomplete="new-password" placeholder="<?php echo esc_attr( cfair_mask_key( (string) get_option( 'cfair_api_key' ) ) ); ?>"><p class="description"><?php esc_html_e( 'Leave blank to keep the current key. The key grants read-only access to the tenant’s approved public projection.', 'cyberfort-ai-register' ); ?></p></td></tr>
<tr><th><label for="cfair_environment"><?php esc_html_e( 'Environment', 'cyberfort-ai-register' ); ?></label></th><td><select id="cfair_environment" name="cfair_environment"><?php foreach ( [ 'production', 'staging', 'development' ] as $value ) : ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( get_option( 'cfair_environment' ), $value ); ?>><?php echo esc_html( ucfirst( $value ) ); ?></option><?php endforeach; ?></select></td></tr>
<tr><th><label for="cfair_language"><?php esc_html_e( 'Default language', 'cyberfort-ai-register' ); ?></label></th><td><select id="cfair_language" name="cfair_language"><?php foreach ( [ 'auto', 'lv', 'en' ] as $value ) : ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( get_option( 'cfair_language' ), $value ); ?>><?php echo esc_html( strtoupper( $value ) ); ?></option><?php endforeach; ?></select></td></tr>
</table>
<h2><?php esc_html_e( 'Display', 'cyberfort-ai-register' ); ?></h2>
<table class="form-table" role="presentation">
<tr><th><label for="cfair_base_slug"><?php esc_html_e( 'Register URL slug', 'cyberfort-ai-register' ); ?></label></th><td><input type="text" id="cfair_base_slug" name="cfair_base_slug" value="<?php echo esc_attr( get_option( 'cfair_base_slug', 'ai-register' ) ); ?>"></td></tr>
<tr><th><label for="cfair_register_page_id"><?php esc_html_e( 'Register page', 'cyberfort-ai-register' ); ?></label></th><td><?php wp_dropdown_pages( [ 'name' => 'cfair_register_page_id', 'id' => 'cfair_register_page_id', 'selected' => absint( get_option( 'cfair_register_page_id' ) ), 'show_option_none' => __( 'Use native route only', 'cyberfort-ai-register' ) ] ); ?></td></tr>
<tr><th><label for="cfair_layout"><?php esc_html_e( 'Layout', 'cyberfort-ai-register' ); ?></label></th><td><select id="cfair_layout" name="cfair_layout"><option value="cards" <?php selected( get_option( 'cfair_layout' ), 'cards' ); ?>><?php esc_html_e( 'Cards', 'cyberfort-ai-register' ); ?></option><option value="list" <?php selected( get_option( 'cfair_layout' ), 'list' ); ?>><?php esc_html_e( 'List', 'cyberfort-ai-register' ); ?></option></select></td></tr>
<tr><th><label for="cfair_per_page"><?php esc_html_e( 'Systems per page', 'cyberfort-ai-register' ); ?></label></th><td><input type="number" min="1" max="100" id="cfair_per_page" name="cfair_per_page" value="<?php echo esc_attr( get_option( 'cfair_per_page', 12 ) ); ?>"></td></tr>
<tr><th><?php esc_html_e( 'Visible controls', 'cyberfort-ai-register' ); ?></th><td><?php foreach ( [ 'cfair_show_search' => __( 'Search', 'cyberfort-ai-register' ), 'cfair_show_filters' => __( 'Filters', 'cyberfort-ai-register' ), 'cfair_show_updated' => __( 'Last updated', 'cyberfort-ai-register' ), 'cfair_show_attribution' => __( 'Cyberfort attribution', 'cyberfort-ai-register' ) ] as $name => $label ) : ?><label class="cfair-check"><input type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( get_option( $name, 1 ) ); ?>> <?php echo esc_html( $label ); ?></label><?php endforeach; ?></td></tr>
<tr><th><label for="cfair_accent_color"><?php esc_html_e( 'Accent colour', 'cyberfort-ai-register' ); ?></label></th><td><input type="color" id="cfair_accent_color" name="cfair_accent_color" value="<?php echo esc_attr( get_option( 'cfair_accent_color' ) ?: '#155eef' ); ?>"></td></tr>
</table>
<h2><?php esc_html_e( 'Cache and indexing', 'cyberfort-ai-register' ); ?></h2>
<table class="form-table" role="presentation">
<tr><th><label for="cfair_cache_duration"><?php esc_html_e( 'Cache duration', 'cyberfort-ai-register' ); ?></label></th><td><select id="cfair_cache_duration" name="cfair_cache_duration"><?php foreach ( [ 300 => '5 minutes', 900 => '15 minutes', 1800 => '30 minutes', 3600 => '1 hour', 21600 => '6 hours' ] as $value => $label ) : ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( (int) get_option( 'cfair_cache_duration' ), $value ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></td></tr>
<tr><th><?php esc_html_e( 'Search engines', 'cyberfort-ai-register' ); ?></th><td><label><input type="checkbox" name="cfair_noindex" value="1" <?php checked( get_option( 'cfair_noindex' ) ); ?>> <?php esc_html_e( 'Do not index register routes', 'cyberfort-ai-register' ); ?></label></td></tr>
<tr><th><?php esc_html_e( 'Uninstall', 'cyberfort-ai-register' ); ?></th><td><label><input type="checkbox" name="cfair_delete_data" value="1" <?php checked( get_option( 'cfair_delete_data' ) ); ?>> <?php esc_html_e( 'Delete plugin settings and cache on uninstall', 'cyberfort-ai-register' ); ?></label></td></tr>
</table>
<?php submit_button(); ?>
</form>
<div class="cfair-actions">
<button class="button button-secondary" id="cfair-test" type="button"><?php esc_html_e( 'Test connection', 'cyberfort-ai-register' ); ?></button><div id="cfair-test-result" role="status" aria-live="polite"></div>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="cfair_clear_cache"><?php wp_nonce_field( 'cfair_clear_cache' ); ?><button class="button"><?php esc_html_e( 'Clear cache', 'cyberfort-ai-register' ); ?></button></form>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="cfair_create_page"><?php wp_nonce_field( 'cfair_create_page' ); ?><button class="button"><?php esc_html_e( 'Create register page', 'cyberfort-ai-register' ); ?></button></form>
</div>
</div>
<aside class="cfair-admin__aside"><h2><?php esc_html_e( 'Diagnostics', 'cyberfort-ai-register' ); ?></h2><dl><?php foreach ( $health as $label => $value ) : ?><dt><?php echo esc_html( ucwords( str_replace( '_', ' ', $label ) ) ); ?></dt><dd><?php echo esc_html( is_scalar( $value ) ? (string) $value : wp_json_encode( $value ) ); ?></dd><?php endforeach; ?></dl></aside>
</div></div>
