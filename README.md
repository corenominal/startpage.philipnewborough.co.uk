# startpage.philipnewborough.co.uk

A self-hosted personal start page built with [CodeIgniter 4](https://codeigniter.com/). It acts as a smart query router — entered text is matched against redirects, custom search engine prefixes, and URL patterns before falling back to a default search, making it a fast and flexible browser homepage.

## Features

- **Smart query routing** — matches input against phrase redirects, search engine prefixes, and raw URLs in priority order
- **Custom search engines** — define prefix-based shortcuts (e.g. `g foo` → Google search for "foo")
- **Redirects** — map phrases to URLs for instant navigation
- **Shortcuts** — organise bookmarks into reorderable categories with custom icons
- **Search history** — records queries with frequency counts; viewable and deletable via UI
- **System commands** — run server-side tools via the start page (e.g. `/ping`, `/whois`, `/dig`, `/headers`)
- **OpenSearch integration** — use the start page as a search engine from your browser address bar
- **Import / Export** — back up and restore redirects, search engines, and history as JSON
- **Admin interface** — manage all data via a web UI with server-side DataTables
- **External authentication** — session validation delegated to a remote auth server
- **REST API** — minimal JSON API with key-based auth for programmatic access
- **Metrics endpoint** — receives and records analytics data

## Tech Stack

- **PHP / CodeIgniter 4** — application framework (PSR-12 code style)
- **Bootstrap 5** — UI framework (BEM CSS naming convention)
- **Bootstrap Icons** — icon library
- **Hermawan/DataTables** — server-side DataTables integration
- **JavaScript** — Airbnb style guide

## Requirements

- PHP 8.1+
- Composer
- A web server (Apache or Nginx)
- MySQL / MariaDB

## Setup

1. Clone the repository and install dependencies:

   ```bash
   composer install
   npm install
   ```

2. Copy the environment file and configure it:

   ```bash
   cp env .env
   ```

3. Update `.env` with your database credentials, base URL, and any external service URLs defined in `app/Config/Urls.php`.

4. Run database migrations:

   ```bash
   php spark migrate
   ```

5. Point your web server document root at the `public/` directory.

## Configuration

Key configuration files under `app/Config/`:

| File | Purpose |
|------|---------|
| `User.php` | Application username and home directory |
| `Urls.php` | URLs for external services (auth server, assets CDN, metrics, etc.) |
| `ApiKeys.php` | API key definitions |
| `Routes.php` | Route definitions |

## Routes Overview

| Path | Description |
|------|-------------|
| `/` | Main start page |
| `/start/search` | Search engine management |
| `/start/redirects` | Redirect management |
| `/start/history` | Query history |
| `/admin` | Admin dashboard |
| `/admin/shortcuts` | Shortcut and category management |
| `/admin/import-export` | Import / export data |
| `/command` | JSON command endpoint |
| `/opensearch.xml` | OpenSearch descriptor |
| `/api/test/ping` | API health check |
| `/logout` | End session |

## License

See [LICENSE](LICENSE).

