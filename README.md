# Barnomala Client

Barnomala Client is a Laravel 12 + Vue 3 web application for managing and publishing a school/institute website. It includes a public-facing site, an authenticated admin panel, content management for notices/news/speeches, configurable homepage sections, and SSO-based admin login integration.

## Features

- Public website pages (home, about, history, achievements, academic, results, teachers, contact, apply)
- Notice and news listing/details pages
- Admin dashboard protected by authentication and email verification
- Admin CRUD for notices, news, and speeches
- Homepage/institute configuration management (branding, sliders, settings, stats, layout)
- SSO login endpoint for platform-driven admin sign-in
- Seeded app options for quick bootstrap

## Tech Stack

- Backend: Laravel 12, PHP 8.2+
- Auth: Laravel Fortify + Jetstream
- Frontend: Vue 3, Vite 7, Tailwind CSS 4
- Database: MySQL (default config)
- Queue/Cache/Session: Database drivers (default in environment template)

## Requirements

- PHP 8.2 or higher
- Composer 2+
- Node.js 20+ and npm
- MySQL 8+ (or compatible)

## Local Development Setup

### 1) Install dependencies and initialize app

You can run the project bootstrap script:

```bash
composer run setup
```

Or run setup manually:

```bash
composer install
copy .env.example .env
php artisan key:generate
npm install
```

### 2) Configure environment

Edit `.env` and set at least the following values:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

WP_DB_HOST=127.0.0.1
WP_DB_PORT=3306
WP_DB_DATABASE=old_wordpress
WP_DB_USERNAME=root
WP_DB_PASSWORD=

CLIENT_API_KEY=your_sso_shared_secret
CLIENT_ADMIN_EMAIL=admin@barnomala.com
```

Notes:

- `CLIENT_API_KEY` is required for validating SSO signature.
- `CLIENT_ADMIN_EMAIL` is used by SSO and seeder logic. If not set, default is `admin@barnomala.com`.

### 3) Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed
```

This creates a system admin user using `CLIENT_ADMIN_EMAIL`.

### 4) Start development servers

Run all app processes together (web server, queue listener, logs, Vite):

```bash
composer run dev
```

Or run manually in separate terminals:

```bash
php artisan serve
php artisan queue:listen --tries=1 --timeout=0
php artisan pail --timeout=0
npm run dev
```

## Build for Production

```bash
npm run build
```

After build, a ZIP archive is created at `public/build.zip` (via `postbuild` script).

## Testing

```bash
composer run test
```

You can also run:

```bash
php artisan test
```

## Important Routes

- `/` - Home page
- `/notices` and `/notices/{notice}` - Public notices
- `/news` and `/news/{news}` - Public news
- `/contact-us` - Contact page and form submission
- `/sso/login` - SSO entry endpoint
- `/admin` - Admin dashboard (auth + verified)
- `/admin/transfer` - Data transfer manager (legacy DB preview + WordPress API preview + teacher import)

## SSO Login Flow

The SSO endpoint expects query params:

- `payload` (base64-encoded JSON containing `expires_at`)
- `signature` (HMAC SHA-256 of payload, signed with `CLIENT_API_KEY`)

On success, the app signs in the admin user identified by `CLIENT_ADMIN_EMAIL` and redirects to `/admin`.

## Project Structure (High Level)

- `app/Http/Controllers` - Public/admin/SSO controllers
- `app/Models` - Core entities (`Notice`, `News`, `Speech`, `Option`, artifacts, `User`)
- `database/migrations` - Schema definitions
- `database/seeders` - Initial options and admin user seeding
- `resources/views` - Blade templates (public and admin)
- `routes/web.php` - Main web routes

## Operational Notes

- Ensure queue worker is running in environments where queued jobs are used.
- If file uploads are enabled in your deployment, run:

```bash
php artisan storage:link
```

- Route `/up` currently triggers migration and cache clear. Restrict or remove this route in production for security.

## License

This project is distributed under the MIT License (Laravel base project license), unless your organization applies a different internal license policy.
