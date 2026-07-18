# Brit Travels — Day-by-Day Launch Roadmap
Fixes → Content → SEO → Optimization → SiteGround Hosting
Prepared: July 16, 2026

This assumes ~1-2 focused hours/day. Adjust the pace to your availability — the order matters more than the exact day count.

---

## Before you start: what I found that needs attention

From reviewing your actual project folder:
- `phone` in your site settings is still the placeholder `+44 0000 000000` (`database/seeders/DatabaseSeeder.php`)
- `address` is just `"United Kingdom"` — no real street address
- `public/images/` only contains `og-default.jpg` — your fleet pages currently use an illustrated graphic (`coach-illustration.blade.php`), not real photos of your actual coaches
- Fonts are Space Grotesk + Inter, self-hosted via Fontsource — fine technically, swap-able if you want a different look
- `.env` is still in local/dev mode (`APP_ENV=local`, SQLite) — expected until hosting

---

## Phase 1 — Content & Asset Fixes (Days 1–3)

**Day 1: Real business info**
- Log into `/admin` → Site Settings. Replace phone, add full street address (needed for schema + Google Business Profile matching later), confirm email addresses are ones you actually monitor.
- Double check the `booking_notification_email` — this is where booking/quote alerts land, make sure it's correct before going live.
- Write/refine your tagline and hero heading if the current wording ("Travel Together, Travel Better") doesn't match your brand voice.

**Day 2: Photos**
- Gather real photos: your actual coaches/minibuses (exterior + interior), drivers if comfortable, and a hero/banner shot for the homepage.
- Upload via Admin → Fleet for each coach size — this replaces the illustrated placeholders with real images, which matters a lot for trust and conversions (and slightly for image search SEO).
- Also replace `public/images/og-default.jpg` with a real branded image (1200×630px) — this is what shows when your links are shared on social media/WhatsApp.
- If you don't have professional photos yet, at minimum use real phone photos of your actual vehicles — real beats stock every time for a local service business.

**Day 3: Fonts & visual polish**
- If you want different fonts than Space Grotesk/Inter: browse [fontsource.org](https://fontsource.org) for a self-hosted alternative (keeps performance — no external Google Fonts request), swap the `@import` lines in `resources/css/app.css` and the `--font-sans`/`--font-display` variables.
- Review colors/spacing/copy across each page (`resources/views/pages/*`, `home.blade.php`, `fleet/*`) for anything that still reads as placeholder/lorem-ipsum text.
- Proofread the FAQ, testimonials, and about page content — these were likely seeded with example content.

---

## Phase 2 — Finish the SEO Work (Days 4–5)

Most of the technical SEO is already built (sitemap, robots.txt, canonical tags, Open Graph, LocalBusiness schema). What's left:

**Day 4: Schema & metadata completion**
- In `resources/views/components/layout.blade.php`, the `LocalBusiness` JSON-LD address only has `addressCountry: GB`. Add `streetAddress`, `addressLocality`, and `postalCode` once you have your real address from Day 1 — this helps Google match your site to your Google Business Profile.
- Review each page's title/description (passed into `<x-layout title="..." description="...">`) — make sure they're unique, specific, and under ~60/160 characters, not just the site-wide default.
- Confirm fleet pages have unique descriptions per coach size (not a templated repeat).

**Day 5: Analytics & verification (do this right before or right after hosting)**
- Create a Google Search Console property for `brittravel.co.uk` (use the domain-level verification via DNS TXT record — SiteGround lets you add this in Site Tools → Domain → DNS Zone Editor).
- Add Google Analytics 4 — create a property at [analytics.google.com](https://analytics.google.com), add the tracking snippet to `layout.blade.php`'s `<head>`.
- Once live, submit `https://brittravel.co.uk/sitemap.xml` in Search Console.

---

## Phase 3 — Code Cleanup & Optimization (Day 6)

**Your question: do you need to keep everything in the project folder?** No — here's what actually needs to travel to the live server versus what's safe to leave out (your `.gitignore` already reflects most of this correctly):

| Keep / upload | Leave out / regenerate on server |
|---|---|
| `app/`, `routes/`, `resources/`, `database/`, `config/`, `public/` (minus `build/`), `composer.json`, `composer.lock`, `package.json`, `artisan` | `vendor/` — regenerate via `composer install --no-dev --optimize-autoloader` on the server (or upload it if SSH access is limited) |
| `.env` — created fresh on the server with production values, never copied from local as-is | `node_modules/` — never needed on the server; only `npm run build`'s output (`public/build/`) is |
| `public/build/` — the compiled CSS/JS from `npm run build` | `.git/`, `.claude/`, `tests/`, `.phpunit.result.cache` — dev-only, safe to exclude from production |
| `storage/` folder structure (needs to exist and be writable) | `storage/logs/*.log` — don't need old local logs on the server |

**Optimization tasks for Day 6:**
- Run `npm run build` locally and confirm no console errors.
- Run `composer install --no-dev --optimize-autoloader` in a copy of the project to verify it installs cleanly without dev dependencies.
- Skim for obvious N+1 query risks — e.g. confirm `Coach::active()->get()` calls aren't triggering per-item queries in a loop in the Blade views (check `fleet/index.blade.php` for any relationship access inside `@foreach`).
- Note for later: once live, you'll run `php artisan config:cache`, `route:cache`, `view:cache` — these are your biggest free performance wins and take 10 seconds.

---

## Phase 4 — Domain & SiteGround Hosting (Days 7–8)

Your own `DEPLOYMENT.md` (already in your project) is a good, specific step-by-step for this exact project — use it as the primary reference. Summary + video support below.

**Day 7: Domain + database + email**
1. In SiteGround Site Tools, add `brittravel.co.uk` as your site if not already, and point your domain's nameservers (at your registrar) to SiteGround, or add an A record — SiteGround shows you the exact values to use.
2. Site Tools → MySQL → create your production database, user, and password.
3. Site Tools → Email → Accounts → create/confirm `info@brittravel.co.uk` and note the SMTP settings for your `.env`.

Video walkthroughs for this part:
- [How To Point Your Domain To Siteground Hosting (2026 Guide)](https://www.youtube.com/watch?v=YE7YBuJWVyg)
- [How to Point a Domain to SiteGround Servers?](https://www.youtube.com/watch?v=DYLuTq5qyuc)
- Official written guide: [SiteGround — Point a Domain to SiteGround Servers](https://world.siteground.com/tutorials/getting-started/point-domain-siteground-servers/)

*(I found these via search but haven't watched them personally — skim the first couple of minutes to confirm it matches your current SiteGround dashboard before following along, since hosting panel UIs change.)*

**Day 8: Upload, configure, go live**
1. Connect via SSH (Site Tools → Devs → SSH Keys Manager), or use File Manager if you're not comfortable with SSH yet.
2. Upload the project per the "Keep / leave out" table above, following your `DEPLOYMENT.md` Option A (SSH) or Option B (File Manager).
3. Set the document root to point at your project's `public/` folder (Site Tools → Domain → Document Root).
4. Configure `.env` on the server with production DB, mail, and `APP_URL` values; set `APP_ENV=production`, `APP_DEBUG=false`.
5. Run: `php artisan key:generate` (if needed) → `migrate --force` → `db:seed --force` → `storage:link` → `config:cache` → `route:cache` → `view:cache`.
6. Install the free SSL certificate (Site Tools → Security → SSL Manager) and enable HTTPS Enforce.
7. Set up the cron job for queued emails: `php artisan schedule:run` every minute (Site Tools → Devs → Cron Jobs).

Video walkthroughs for this part:
- [Upload Laravel project in SiteGround cPanel](https://www.youtube.com/watch?v=NIwj54CpcNc)
- [How to Connect to Website Through SSH in SiteGround (2026)](https://www.youtube.com/watch?v=6KAFUaI3U88)
- [How To Host A Website On SiteGround (2026)](https://www.youtube.com/watch?v=TdR7hIXCh6c) — general hosting walkthrough, useful if you're new to SiteGround's dashboard overall
- Official: [SiteGround KB — How to install Laravel on my SiteGround account?](https://www.siteground.com/kb/install-laravel/)

---

## Phase 5 — Testing (Day 9)

- Load `https://brittravel.co.uk` — confirm the padlock/HTTPS works.
- Submit a real test booking and a real test quote — confirm emails arrive (check spam folder the first time).
- Log into `/admin` on the live site, confirm the test booking/quote shows up, then **change the seeded admin password immediately**.
- Check `https://brittravel.co.uk/sitemap.xml` loads correctly.
- Test on an actual phone, not just browser dev tools — check tap targets, form usability, and load speed on mobile data if possible.
- Run [PageSpeed Insights](https://pagespeed.web.dev) against the live URL and note any red flags.

---

## Phase 6 — Post-Launch SEO Ramp-Up (Days 10–12+)

- **Day 10**: Set up and verify your Google Business Profile (business name, category, service area, real address, phone, hours, a handful of real photos).
- **Day 11**: Submit sitemap in Search Console (if not done in Phase 2), request indexing for your homepage and top fleet pages.
- **Day 12 onward**: Start collecting real customer reviews (Google + on-site testimonials), and begin planning your first 3–4 blog topics based on real customer questions — this is the point where blogging starts to earn its keep, as covered earlier.

---

## Quick answer recap: code optimization

You do **not** need to keep `vendor/`, `node_modules/`, `.git/`, or old log files in what you deploy — they're either regenerated on the server (`composer install`, `npm run build`) or simply not needed there. Your `.gitignore` already excludes the right things for version control; just make sure your upload method (SSH/zip) follows the same rule so you're not manually dragging 100MB+ of `node_modules` onto a shared host for no reason.
