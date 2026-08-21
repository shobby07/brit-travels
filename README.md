# Brit Travel — Coach Hire Website

Modern, SEO-optimized coach hire website with online booking and quotation forms. Built
for **https://brittravel.co.uk**, hosted on Laravel Cloud.

## Stack

- **Laravel 13** (PHP 8.3) — **no database**, all content is static config
- **Tailwind CSS 4** + **Alpine.js** frontend, **GSAP + Lenis** animations/smooth scroll
- Vite builds, self-hosted fonts (Space Grotesk + Inter)

## How content works

There is no database and no admin panel. Everything the site needs ships with the code:

| Content | File |
|---|---|
| Fleet (8 coaches) | `config/fleet.php` |
| Location landing pages (9 cities) | `config/locations.php` |
| FAQs | `config/faqs.php` |
| Testimonials | `config/testimonials.php` |
| Phone, email, address, hero text, socials | `config/site.php` |
| Coach photos | `public/images/coaches/` |
| Location hero photos | `public/images/hero/` |

To change content: edit the file, commit, push. See [DEPLOYMENT.md](DEPLOYMENT.md).

## Features

- **Booking** (`/book`): 2-step form — one-way/round-trip, pickup & drop-off, via stops,
  date/time, passengers, luggage, optional coach. Emails you and the customer, each with
  a shared reference (e.g. `BT-2026-XXXXX`)
- **Quotations** (`/quote`): free quote request form, same email flow (`QT-…` references)
- **Fleet**: 8 coach sizes (8–70 seats), each with its own SEO landing page
  (`/fleet/49-seater-coach`)
- **Locations**: 9 city landing pages (`/coach-hire/london`) with unique copy and FAQs
- **SEO**: server-rendered HTML, unique meta per page, JSON-LD (LocalBusiness, Service,
  FAQPage, Vehicle, Breadcrumbs), `sitemap.xml`, `robots.txt`, Open Graph, canonical URLs
- Spam protection: honeypot fields + rate limiting on all public forms

> **Submissions are emailed, not stored.** The notification email is the only record of a
> booking. If it can't be sent, the visitor is shown an error asking them to call rather
> than a thank-you page. Keep an eye on deliverability.

## Local development

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
npm run build            # or: npm run dev (watch mode)
php artisan serve
```

- Site: http://127.0.0.1:8000
- Local emails are written to `storage/logs/laravel.log` (`MAIL_MAILER=log`)

No migrations, no seeding, no database setup.

## Tests

```powershell
php artisan test
```

Covers the booking/quote/contact flows, validation rules, email dispatch, every fleet and
location page, SEO tags, and structured data.

## Deployment

See **[DEPLOYMENT.md](DEPLOYMENT.md)**. The previous SiteGround guide is kept at
[DEPLOYMENT-SITEGROUND.md](DEPLOYMENT-SITEGROUND.md) for reference — note it predates the
database removal.
