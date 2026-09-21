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
SITE_EMAIL=enquiries@brittravel.co.uk
SITE_PHONE="01206 591149"
# Drives the floating WhatsApp button — full international form.
SITE_WHATSAPP_NUMBER="+44 7348 656810"

# Hostinger mailbox — see the notes below before changing any of these.
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_SCHEME=smtps
MAIL_USERNAME=enquiries@brittravel.co.uk
MAIL_PASSWORD=your-mailbox-password
MAIL_FROM_ADDRESS="enquiries@brittravel.co.uk"
MAIL_FROM_NAME="Brit Travel"
```

`APP_KEY` is generated for you on first deploy.

### About the mail settings

**`MAIL_SCHEME` only accepts `smtp` or `smtps`.** They are the only two schemes
Symfony's mailer registers, so `tls` or `ssl` throw `UnsupportedSchemeException`
and *every* send fails. Use `smtps` with port 465, or `smtp` with port 587 —
STARTTLS is negotiated automatically on 587, so the connection is encrypted
either way.

**`MAIL_PASSWORD` is the mailbox password**, the one you set when creating
enquiries@brittravel.co.uk in hPanel — not your Hostinger account password.

**`MAIL_USERNAME` and `MAIL_FROM_ADDRESS` should be the same address.** Sending
as an address the authenticated mailbox doesn't own fails SPF and lands the mail
in spam, or gets it rejected outright.

**`BOOKING_NOTIFICATION_EMAIL` can be left unset.** It falls back to
`SITE_EMAIL`, so enquiries go to enquiries@brittravel.co.uk by default. Set it
only if you want them delivered somewhere other than the address shown on the
site.

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

The single most important check, because email is the whole booking system.

Open a console on your Laravel Cloud app and run:

```bash
php artisan mail:test
```

It prints the mail settings **as the running app sees them** and sends a fully
populated sample booking notification. Two things make that worth more than
guessing at the form:

- The printed values come from the cached config, so if you changed a variable
  without redeploying you'll see the stale one and know why it's still failing.
- It's a real `BookingReceivedMail`, so a pass proves the credentials, the
  template and the subject line all work — not just that a socket opened.

Pass an address to send it elsewhere: `php artisan mail:test you@gmail.com`.

Then confirm end to end:

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

Run `php artisan mail:test` first — it reports the exact SMTP error instead of
leaving you to guess. Then check, in order:

1. You **redeployed** after changing any environment variable. Config is cached at
   build time, so a change alone does nothing. `mail:test` prints the values actually
   in use, which is the quickest way to spot a stale cache.
2. Your spam folder — and mark the first one "not spam".
3. The `MAIL_*` settings, against the notes in step 2 above. `MAIL_SCHEME=tls` is the
   classic mistake and fails every send.

If SMTP is rejecting mail, visitors see an error on the form telling them to call,
and the failure is written to your logs — so nothing is lost silently.

### "Failed to authenticate on SMTP server"

The mailbox password is wrong, or `MAIL_USERNAME` isn't the full address. Use the
complete `enquiries@brittravel.co.uk`, not `enquiries`, and the password you set for
that mailbox in hPanel rather than your Hostinger account login. If you're sure both
are right, reset the mailbox password in hPanel and update the variable.

### "Connection could not be established" / timeouts

Port 465 is blocked or the host is unreachable. Switch to the other pair —
`MAIL_PORT=587` with `MAIL_SCHEME=smtp` — and redeploy.

### Emails land in spam

Check that brittravel.co.uk's SPF and DKIM records exist in your DNS. Hostinger adds
them automatically when the domain's nameservers point at Hostinger; if DNS is hosted
elsewhere, copy the records from hPanel → Emails → DNS settings. `MAIL_FROM_ADDRESS`
must also match `MAIL_USERNAME`, or the mail is unauthenticated no matter what the
DNS says.

### Bookings stop arriving during a busy spell

Hostinger mailboxes have an hourly sending limit, and each submission sends two
emails (office copy plus customer acknowledgement). If you ever hit it, move to a
dedicated sending service such as Postmark, Resend or SES — only the `MAIL_*`
variables change, no code.

### A content change isn't showing

You need to redeploy — content is baked into the config cache at build time.

### "Database file … does not exist"

This shouldn't be possible any more; nothing touches a database. If you ever see it, a
`DB_*` environment variable or leftover code is at fault — send me the error.
