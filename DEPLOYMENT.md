# Deploying Brit Travels to SiteGround

This guide takes the site from this folder to live on **https://brittravel.co.uk**.

## What you need

- SiteGround hosting plan (GrowBig or higher recommended — it includes SSH)
- The brittravel.co.uk domain pointed at SiteGround (already done if your current site is there)
- About 30–45 minutes

---

## 1. Prepare the build locally

On this PC, from the project folder:

```powershell
npm run build          # compiles CSS/JS into public/build
composer install --no-dev --optimize-autoloader
```

## 2. Create the database in SiteGround

1. Log in to **SiteGround → Site Tools → MySQL**.
2. Create a **database**, a **user**, and give the user **all privileges** on the database.
3. Note down: database name, username, password (host is `localhost`).

## 3. Create the email account

1. **Site Tools → Email → Accounts** → create `info@brittravel.co.uk` (or use your existing one).
2. Note the SMTP settings (**Email → Accounts → Mail Configuration**):
   - Host: `mail.brittravel.co.uk` (SiteGround shows the exact host)
   - Port: `465` (SSL)
   - Username: the full email address
   - Password: the mailbox password

## 4. Upload the project

**Option A — SSH + Git (recommended, GrowBig+):**

1. **Site Tools → Devs → SSH Keys Manager** → create a key and connect via SSH.
2. Upload the project (from this PC):
   ```powershell
   scp -r C:\Users\shoaib\brit-travels username@yourserver:~/brit-travels
   ```
   (or push to GitHub and `git clone` on the server — but you must still upload `public/build`, `vendor`, and `.env` separately if they're git-ignored, or run `composer install` on the server.)

**Option B — File Manager / FTP:**

1. Zip the entire project folder (including `vendor/` and `public/build/`, excluding `node_modules/`).
2. Upload and extract it to a folder **outside** `public_html`, e.g. `/home/username/brit-travels`.

## 5. Point the web root at Laravel's /public

Laravel must serve from its `public/` folder. On SiteGround:

1. **Site Tools → Domain → Site → Document Root** (on some plans this is under "Manage Domain").
2. Set the document root for brittravel.co.uk to:
   ```
   brit-travels/public
   ```
   If your plan doesn't allow changing document root, instead move the *contents* of `public/` into `public_html/` and edit `public_html/index.php` so the two `require` paths point at `../brit-travels/vendor/autoload.php` and `../brit-travels/bootstrap/app.php`.

## 6. Configure .env for production

Edit `.env` on the server:

```env
APP_NAME="Brit Travels"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://brittravel.co.uk

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=mail.brittravel.co.uk
MAIL_PORT=465
MAIL_SCHEME=smtps
MAIL_USERNAME=info@brittravel.co.uk
MAIL_PASSWORD=your_mailbox_password
MAIL_FROM_ADDRESS="info@brittravel.co.uk"
MAIL_FROM_NAME="Brit Travels"
```

## 7. Run the setup commands (via SSH)

```bash
cd ~/brit-travels
php artisan key:generate         # only if APP_KEY is empty
php artisan migrate --force
php artisan db:seed --force      # seeds fleet, FAQs, settings + admin user
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> **Admin password:** the seeder creates `admin@brittravel.co.uk` with the password
> from `ADMIN_SEED_PASSWORD` in `.env` (default `ChangeMe!2026`).
> **Log in at `/admin` and change it immediately**, or set `ADMIN_SEED_PASSWORD`
> in `.env` *before* seeding.

## 8. Set up the cron job (sends the emails)

Booking/quote emails are queued so the visitor never waits on SMTP. A cron job sends them:

1. **Site Tools → Devs → Cron Jobs** → add:
   ```
   php /home/username/brit-travels/artisan schedule:run
   ```
   Interval: **every minute** (`* * * * *`).

## 9. SSL + performance

1. **Site Tools → Security → SSL Manager** → install the free Let's Encrypt cert; enable **HTTPS Enforce**.
2. **Site Tools → Speed → Caching** → enable Dynamic Caching and Memcached if available.
3. In **SpeedOptimizer**, enable GZIP and browser caching. (Skip its HTML/CSS/JS "minify" options — Vite already minifies, and double-minifying can break things.)

## 10. Go-live checklist

- [ ] `https://brittravel.co.uk` loads with the padlock
- [ ] Submit a test booking → email arrives at your notification address (check spam first time)
- [ ] `/admin` login works and shows the booking; **change the admin password**
- [ ] Update phone/email/address in **Admin → Site Settings**
- [ ] Upload real coach photos in **Admin → Fleet**
- [ ] Visit `https://brittravel.co.uk/sitemap.xml` — then submit it in [Google Search Console](https://search.google.com/search-console)
- [ ] Test the site on your phone

## Updating the site later

After code changes locally:

```powershell
npm run build
```

Upload the changed files (including `public/build/`), then on the server:

```bash
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Content changes (coaches, FAQs, testimonials, hero text, contact details) need **no deployment** — edit them in `/admin`.
