# Deploying Brit Travel to Laravel Cloud

This site runs **without a database**. Everything it needs ships with the code, so a
deploy is just: push, build, done — no database to create, attach, or pay for.

> The earlier SiteGround guide is kept at [DEPLOYMENT-SITEGROUND.md](DEPLOYMENT-SITEGROUND.md).

## How the site works now

| Thing | Where it lives |
|---|---|
| Fleet (8 coaches) | `config/fleet.php` |
| Location pages (9 cities) | `config/locations.php` |
| FAQs | `config/faqs.php` |
| Testimonials | `config/testimonials.php` |
| Phone, email, address, hero text, social links | `config/site.php` |
| Coach photos | `public/images/coaches/` |
| Location hero photos | `public/images/hero/` |

Booking, quote, and contact submissions are **emailed straight to you** and are not
stored anywhere. There is no `/admin` panel.

⚠️ **Because nothing is stored, the notification email is the only record of a
booking.** If it fails to send, the visitor is shown an error asking them to phone
instead, rather than a thank-you page for a request you never received. Make sure your
SMTP settings (below) are correct and that the emails aren't landing in spam.

## 1. Push the code

Laravel Cloud deploys from your Git repo. Just make sure `main` is pushed.

## 2. Set environment variables

Settings → Environment Variables. **Delete any `DB_*` variables** — they do nothing now.

```env
APP_NAME="Brit Travel"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://brittravel.co.uk

SESSION_DRIVER=cookie
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Where booking, quote and contact submissions are sent — this is the inbox you check.
BOOKING_NOTIFICATION_EMAIL=you@yourdomain.com

# Shown on the site (footer, contact page, structured data).
SITE_EMAIL=info@brittravel.co.uk
SITE_PHONE="+44 7000 000000"

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_SCHEME=tls
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS="info@brittravel.co.uk"
MAIL_FROM_NAME="Brit Travel"
```

`APP_KEY` is generated for you on first deploy.

`SESSION_DRIVER=cookie` matters: Laravel Cloud's filesystem is wiped on every deploy and
each replica has its own, so file-based sessions would be unreliable. Cookie sessions
live in the visitor's browser and need no storage at all.

## 3. Set build & deploy commands

Settings → Deployments.

**Build command:**
```bash
composer install --no-dev && npm run build && php artisan optimize
```

**Deploy command:** leave it **empty**. There are no migrations to run.

Do not add `php artisan storage:link` — nothing is uploaded at runtime.

## 4. Deploy

Click Deploy and watch the log. That's it — the site comes up complete, with the fleet,
locations, FAQs, testimonials, and all photos already in place.

## 5. Send yourself a test booking

The single most important check, because email is the whole booking system:

1. Go to `/book` and submit a real booking.
2. Confirm the notification arrives at `BOOKING_NOTIFICATION_EMAIL` — **check spam** the
   first time, and mark it "not spam" so future ones land in the inbox.
3. Confirm the customer copy arrives at the address you entered.

## Changing content later

All content is now in code, so changes are: edit the file, commit, push. Laravel Cloud
redeploys automatically.

| To change… | Edit… |
|---|---|
| Phone / email / address / hero text | `config/site.php` |
| A coach's name, seats, description, amenities | `config/fleet.php` |
| A city page's copy or FAQs | `config/locations.php` |
| Site FAQs | `config/faqs.php` |
| Testimonials | `config/testimonials.php` |

To swap a coach photo, drop a new WebP **and** JPEG pair into `public/images/coaches/`
using the same filenames, or point that coach's `image` value at your new filename.

Your phone and email can also be changed without a code change — they read from the
`SITE_PHONE` and `SITE_EMAIL` environment variables.

## Troubleshooting

### Bookings aren't arriving

Check, in order:

1. `BOOKING_NOTIFICATION_EMAIL` is set (Settings → Environment Variables) and you
   redeployed after setting it — config is cached at build time.
2. Your spam folder.
3. Your `MAIL_*` settings. If SMTP is rejecting mail, visitors see an error on the form
   telling them to call, and the failure is written to your logs.

### A content change isn't showing

You need to redeploy — content is baked into the config cache at build time.

### "Database file … does not exist"

This shouldn't be possible any more; nothing touches a database. If you ever see it, a
`DB_*` environment variable or leftover code is at fault — send me the error.
