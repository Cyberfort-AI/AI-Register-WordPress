=== Cyberfort AI Register for WordPress ===
Contributors: cyberfort
Tags: ai register, ai act, transparency, public register, governance
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display a tenant's approved public AI-system register from Cyberfort AI Register as native WordPress content.

== Description ==

Cyberfort AI Register for WordPress is a read-only connector and presentation plugin.

It retrieves a tenant-scoped public data projection from Cyberfort AI Register through server-side HTTPS requests and renders it inside the active WordPress theme. The connector API key is never sent to public browser JavaScript.

The plugin does not copy the AI Register database into WordPress and does not expose internal evidence, users, FRIA records, documents or administrative functions.

Features include:

* Native WordPress configuration under Settings → AI Register.
* Tenant-specific connector API key.
* Test connection and masked diagnostics.
* Gutenberg block for a complete AI Systems Register.
* Gutenberg block for one AI system.
* Shortcodes for register and individual-system views.
* Native WordPress routes.
* Latvian and English language handling.
* WPML and Polylang language detection.
* Search, filtering, list/card layouts and pagination settings.
* ETag and 304 response support.
* Last-known-good fallback for temporary upstream failures.
* Theme-aware rendering with isolated styles.

A compatible Cyberfort AI Register server exposing the `/api/public/v1` connector endpoints is required.

== Installation ==

1. Upload `cyberfort-ai-register-1.0.0.zip` through Plugins → Add New → Upload Plugin.
2. Activate the plugin.
3. Open Settings → AI Register.
4. Enter the Cyberfort AI Register server address.
5. Enter the tenant-specific connector API key.
6. Select Test connection.
7. Create or assign the public register page.
8. Add the AI Systems Register Gutenberg block or `[cyberfort_ai_register]` shortcode.

== Frequently Asked Questions ==

= Does this plugin store the AI Register database in WordPress? =

No. Cyberfort AI Register remains the authoritative system. The plugin caches only the approved public API projection.

= Is the connector API key visible to website visitors? =

No. API requests are made from the WordPress server. The key is not sent to public JavaScript.

= Can visitors edit AI systems through WordPress? =

No. Version 1 is strictly read-only.

= Can one WordPress site connect to several tenants? =

No. Version 1 connects one WordPress site to one AI Register tenant.

= Which shortcodes are available? =

Complete register:

`[cyberfort_ai_register]`

Individual system:

`[cyberfort_ai_system reference="MIC-001"]`

= Which API endpoints are required? =

* `GET /api/public/v1/tenant`
* `GET /api/public/v1/systems`
* `GET /api/public/v1/systems/{reference}`
* `GET /api/public/v1/taxonomies`
* `GET /api/public/v1/branding`

== Changelog ==

= 1.0.0 =

* Initial WordPress connector foundation.
* Added native configuration and connection diagnostics.
* Added server-side authenticated API client.
* Added ETag and 304 handling.
* Added last-known-good cache fallback for temporary failures.
* Added Gutenberg register and system blocks.
* Added register and individual-system shortcodes.
* Added native public routes.
* Added Latvian and English language handling.
* Added WPML and Polylang detection.
* Added display, indexing and cache settings.
* Added PHP 8.1–8.3 CI validation.

== Upgrade Notice ==

= 1.0.0 =

Initial release. Requires a compatible Cyberfort AI Register connector API and tenant key.
