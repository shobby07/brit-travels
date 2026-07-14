# Brit Travels — Coach Hire Website

Modern, SEO-optimized coach hire website with online booking, quotation requests, and a full admin dashboard. Built for **https://brittravel.co.uk**, hosted on SiteGround.

## Stack

- **Laravel 13** (PHP 8.3) + **MySQL** (SQLite in local dev)
- **Filament 5** admin panel at `/admin`
- **Tailwind CSS 4** + **Alpine.js** frontend, **GSAP + Lenis** animations/smooth scroll
- Vite builds, self-hosted fonts (Space Grotesk + Inter)

## Features

- **Booking** (`/book`): 2-step form — one-way/round-trip, pickup & drop-off, date/time, passengers, optional coach — emails the owner + customer, stored with reference (e.g. `BT-2026-XXXXX`)
- **Quotations** (`/quote`): free quote request form, same email flow (`QT-…` references)
- **Fleet**: 8 coach sizes (8–70 seats), each with its own SEO landing page (`/fleet/49-seater-coach`)
- **Admin** (`/admin`): bookings (confirm / cancel / complete with one click + optional customer email, filters, CSV export), quotes, fleet CRUD with image upload, testimonials, FAQs, site settings, dashboard stats & chart
- **SEO**: server-rendered HTML, unique meta per page, JSON-LD (LocalBusiness, Service, FAQPage, Breadcrumbs), `sitemap.xml`, `robots.txt`, Open Graph, canonical URLs
- Spam protection: honeypot fields + rate limiting on all public forms

## Local development

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build            # or: npm run dev (watch mode)
php artisan serve
```

- Site: http://127.0.0.1:8000
- Admin: http://127.0.0.1:8000/admin — `admin@brittravel.co.uk` / `ChangeMe!2026` (from `ADMIN_SEED_PASSWORD`)
- Local emails are written to `storage/logs/laravel.log` (`MAIL_MAILER=log`); run `php artisan queue:work --stop-when-empty` to process them

## Tests

```powershell
php artisan test
```

Covers the booking/quote/contact flows, validation rules, email dispatch, public pages, SEO tags, and admin panel access.

## Deployment

See **[DEPLOYMENT.md](DEPLOYMENT.md)** for the step-by-step SiteGround guide (database, SMTP, document root, cron for queued emails, SSL, go-live checklist).
