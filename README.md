# Cyberfort AI Register for WordPress

Cyberfort AI Register for WordPress is a read-only WordPress presentation layer for public AI-system data published from the central Cyberfort AI Register service.

The plugin does **not** copy the AI Register database into WordPress and does not expose internal evidence, users, FRIA records, documents or administration functions. WordPress sends authenticated server-side requests to the Cyberfort AI Register public connector API and renders the approved public projection inside the active WordPress theme.

## Status

Version `1.0.0` provides the WordPress connector foundation. It requires a compatible Cyberfort AI Register server exposing the `/api/public/v1` connector endpoints and a tenant-specific connector API key.

## Requirements

- WordPress 6.5 or later
- PHP 8.1 or later
- HTTPS
- Cyberfort AI Register connector API key
- One AI Register tenant per WordPress site

## Features

- Native WordPress settings page under **Settings → AI Register**
- Server-side API requests; the connector key is never sent to browser JavaScript
- One tenant per WordPress installation
- Connection test with tenant and API diagnostics
- Gutenberg block for a complete public register
- Gutenberg block for one AI system
- Shortcodes for the register and individual systems
- Native routes for register and system-detail pages
- Latvian and English language handling
- WPML and Polylang language detection
- Search, filters, cards/list layout and pagination settings
- ETag and `304 Not Modified` support
- 15-minute cache by default
- Last-known-good fallback for temporary upstream failures
- Theme-aware rendering with isolated plugin CSS
- Optional noindex control
- PHP 8.1–8.3 syntax validation in GitHub Actions

## Installation

1. Download `cyberfort-ai-register-1.0.0.zip` from the latest build artifact or release.
2. In WordPress, open **Plugins → Add New → Upload Plugin**.
3. Select the ZIP file and choose **Install Now**.
4. Activate **Cyberfort AI Register for WordPress**.
5. Open **Settings → AI Register**.
6. Enter the Cyberfort AI Register server address, for example:

   ```text
   https://airegister.cyberfort.lv
   ```

7. Paste the tenant-specific connector API key.
8. Choose **Test connection**.
9. Create or assign the public register page.
10. Add the AI Register block or shortcode to the selected page.

## WordPress configuration

### Connection

- **Server address:** Cyberfort AI Register base URL, without a trailing API path.
- **Connector API key:** tenant-specific read-only key issued in AI Register.
- **Environment:** production, staging or development.
- **API version:** currently `v1`.

### Presentation

The settings screen controls:

- default language;
- register base slug;
- cards or list layout;
- systems per page;
- search and filter visibility;
- public indexing;
- cache duration;
- last-known-good retention;
- optional automatic register-page creation.

## Gutenberg blocks

### AI Systems Register

Block name:

```text
cyberfort-ai-register/register
```

Displays the connected tenant's complete public register.

### AI System

Block name:

```text
cyberfort-ai-register/system
```

Displays a single system using its public reference, for example `MIC-001`.

## Shortcodes

Complete register:

```text
[cyberfort_ai_register]
```

With options:

```text
[cyberfort_ai_register language="lv" layout="cards" search="yes" filters="yes" per_page="12"]
```

Individual system:

```text
[cyberfort_ai_system reference="MIC-001" language="lv"]
```

## Native routes

Default routes:

```text
/ai-register/
/ai-register/{system-reference}/
```

Latvian route support:

```text
/mi-registrs/
/mi-registrs/{system-reference}/
```

The configured base slug may be changed in WordPress settings.

## Security model

- The API key is used only in server-side WordPress HTTP requests.
- The plugin requests only the public, tenant-scoped API projection.
- The key must have only the `public:read` connector scope.
- The plugin does not receive AI Register user credentials.
- Authentication and permission failures are not hidden by stale-cache fallback.
- Remote response size is limited.
- Output is escaped before rendering.
- Production connections require HTTPS and certificate verification.

## Expected AI Register API

The plugin expects these endpoints:

```text
GET /api/public/v1/tenant
GET /api/public/v1/systems
GET /api/public/v1/systems/{reference}
GET /api/public/v1/taxonomies
GET /api/public/v1/branding
```

Requests use:

```http
Authorization: Bearer {connector_api_key}
X-AIRegister-Plugin-Version: 1.0.0
X-AIRegister-Site-URL: https://customer.example
X-AIRegister-Language: lv
```

## Caching

The plugin uses WordPress transients for fresh responses and retains a last-known-good response for temporary upstream failures. It supports ETag revalidation and `304 Not Modified` responses.

Authentication, authorisation, key expiry and key revocation failures do not use stale public data as a silent fallback.

## Development

Run PHP syntax checks:

```bash
find . -name '*.php' -not -path './.git/*' -print0 | xargs -0 -n1 php -l
```

The repository CI validates PHP 8.1, 8.2 and 8.3.

## Building the installable ZIP

GitHub Actions runs the **Build plugin ZIP** workflow on pushes to `main`, version tags and manual dispatch. It creates:

```text
cyberfort-ai-register-1.0.0.zip
```

The ZIP contains the plugin under the required top-level directory:

```text
cyberfort-ai-register/
```

Development-only files and GitHub metadata are excluded.

## Release checklist

1. Update the version in `cyberfort-ai-register.php`.
2. Update `CFAIR_VERSION`.
3. Update `Stable tag` and changelog in `readme.txt`.
4. Run CI.
5. Run the ZIP build workflow.
6. Install the generated ZIP in a clean WordPress instance.
7. Test connection against a compatible AI Register server.
8. Test register and detail routes in Latvian and English.
9. Publish the signed release package.

## License

GPL-2.0-or-later.
