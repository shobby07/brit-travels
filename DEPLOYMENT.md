# Deploying Brit Travel to Laravel Cloud

This guide takes the site from this folder to live on **Laravel Cloud** (cloud.laravel.com).

> Previously this project targeted SiteGround — that guide is kept at
> [DEPLOYMENT-SITEGROUND.md](DEPLOYMENT-SITEGROUND.md) for reference, but Laravel Cloud
> is the current deployment target.

## Why this needed code changes, not just a dashboard setup

Laravel Cloud's filesystem is **ephemeral** — every deploy resets it, and each running
replica has its own separate copy. That breaks two things this project used to rely on:

1. **SQLite** (`database/database.sqlite`) — the file doesn't exist on a fresh
   deploy, which is exactly the `Database file ... does not exist` error you saw.
   → Fixed by attaching a real **MySQL** database (step 2 below); no code change
   needed since Laravel already reads standard `DB_*` env vars.
2. **Coach photo uploads** via `/admin` — they used to save to local disk
   (`storage/app/public`) and a `storage:link` symlink, which also don't survive a
   redeploy or a second app replica.
   → Fixed in code: uploads now go through `config('filesystems.default')` instead of
   a hardcoded `'public'` disk, and image URLs are built with `Storage::url()` instead
   of a hardcoded `asset('storage/...')` path. Point that default disk at Laravel Cloud
   **Object Storage** (step 3) and uploads persist correctly, on every replica.

Nothing about the local dev workflow changes — `sqlite` + the local `public` disk +
`storage:link` still work exactly as before on your PC.

---

## 1. Push the code

Laravel Cloud deploys straight from your Git repo (GitHub/GitLab/Bitbucket) — no manual
upload. Just make sure this branch is pushed.

## 2. Create the app and attach a MySQL database

1. [cloud.laravel.com](https://cloud.laravel.com) → **New Application** → connect this repo.
2. On the environment's **Infrastructure** canvas → **Add database** → **Laravel MySQL**
   → create a cluster (Flex size is fine to start) → create/select a database.
3. Attach it to your environment. Laravel Cloud auto-injects `DB_HOST`, `DB_USERNAME`,
   `DB_PASSWORD`, `DB_DATABASE` (and `DB_CONNECTION=mysql`) — no `.env` editing needed.

## 3. Attach Object Storage (for coach photos & fleet uploads)

1. On the same Infrastructure canvas → **Add bucket** → **Laravel Object Storage**.
2. **Disk name:** enter `s3` — this matches the disk already defined in
   `config/filesystems.php`, so no extra config is required.
3. **Visibility:** **Public** (coach photos need to be viewable by site visitors).
4. Tick **"Set as default disk"** so `FILESYSTEM_DISK` gets injected automatically.
5. Attach it to your environment.

This also auto-injects the `AWS_ACCESS_KEY_ID` / `AWS_SECRET_ACCESS_KEY` /
`AWS_DEFAULT_REGION` / `AWS_BUCKET` / `AWS_ENDPOINT` variables the `s3` disk expects.

> The `league/flysystem-aws-s3-v3` package (required for the `s3` disk driver) has
> already been added to `composer.json`/`composer.lock` — nothing to install manually.

## 4. Set the remaining environment variables

In your environment's **Settings → Environment Variables**, add:

```env
APP_NAME="Brit Travel"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://brittravel.co.uk

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_SCHEME=tls
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS="info@brittravel.co.uk"
MAIL_FROM_NAME="Brit Travel"

ADMIN_SEED_PASSWORD=set-a-strong-password-here
```

`APP_KEY` is generated automatically for you on first deploy — you don't need to set it.

Database, session, and queue all use the `database` driver, which is backed by the
MySQL database you attached in step 2 — this persists correctly across deploys and
replicas (unlike the local filesystem).

## 5. Set build and deploy commands

In **Settings → Deployments**:

**Build command:**
```bash
composer install --no-dev && npm run build && php artisan config:cache && php artisan optimize
```

**Deploy command** (runs just before the new version goes live):
```bash
php artisan migrate --force
```

Do **not** add `php artisan storage:link` — it won't persist on Cloud's ephemeral
filesystem and isn't needed once Object Storage is attached (see the note in
[Environments](https://cloud.laravel.com/docs/environments) if curious).

## 6. Deploy

Click **Deploy**. Laravel Cloud builds a Docker image, runs your build/deploy commands,
then switches traffic over with zero downtime. Watch the **Deployments** log for errors.

## 7. Seed the database (one-time, via the Commands tab)

Your environment's **Commands** tab lets you run one-off Artisan commands on the live app:

```bash
php artisan db:seed --force
```

This seeds the fleet, FAQs, and the admin user (see the password note below).

> **Admin password:** the seeder creates `admin@brittravel.co.uk` using
> `ADMIN_SEED_PASSWORD` from your environment variables (step 4). **Log in at `/admin`
> and change it immediately.**

## 8. Custom domain

**Settings → Domains** → add `brittravel.co.uk`, follow the DNS instructions shown
(Laravel Cloud verifies ownership and issues the SSL certificate automatically).

## 9. Coach photos — nothing to do

The 8 real coach photos are committed to the repo under `public/images/coaches/`
(WebP + JPEG pairs), and the seeder wires each one to its coach automatically. They
ship with every deploy, so the fleet comes up with real photos rather than the
placeholder illustration — no manual uploading required.

Replacing a photo later is still done from `/admin` → **Fleet** → edit a coach. Those
uploads go to Object Storage, and re-running the seeder will **not** overwrite them.

## 10. Go-live checklist

- [ ] Your `laravel.cloud` URL (or custom domain) loads with the padlock
- [ ] Submit a test booking → email arrives at your notification address
- [ ] `/admin` login works; **change the admin password**
- [ ] Update phone/email/address in **Admin → Site Settings**
- [ ] Fleet page shows the real coach photos (not the placeholder illustration)
- [ ] Visit `/sitemap.xml` — then submit it in [Google Search Console](https://search.google.com/search-console)
- [ ] Test the site on your phone

## Updating the site later

Laravel Cloud supports **push to deploy** — enabled by default. Just push to the branch
your environment tracks and it redeploys automatically (build + deploy commands run
again, including `migrate --force`).

Content changes (coaches, FAQs, testimonials, hero text, contact details) need **no
deployment** at all — edit them in `/admin`, they save straight to the database and
Object Storage.

## A note on coach-hire location hero images

`app/Filament/Resources/CoachHireLocations/Schemas/CoachHireLocationForm.php` still
saves its **hero image** uploads to a `public_assets` disk (the container's local
`public/` folder) rather than the new default disk. That's intentional for the
*seeded* location photos (they're committed to the git repo, so they survive every
deploy), but it means **uploading a new/replacement hero image through `/admin` after
go-live won't survive the next deploy** on Laravel Cloud. Until this field is moved
over to the same Object Storage disk as coach photos, add new location photos to
`public/images/hero/` in the repo and commit them, the same way the seeded ones work.
Happy to wire this one up the same way if you'd like — just ask.
