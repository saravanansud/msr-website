# Midwest Specialty Robotics — Landing Page

A single, fully self-contained landing page. Everything it needs lives in this folder, so you can drop the whole folder onto any static host.

## Contents
```
index.html            ← the page (all CSS + JS inlined)
robots.txt            ← search-crawler rules
sitemap.xml           ← XML sitemap (update <loc> if domain changes)
site.webmanifest      ← PWA / install metadata
favicon.ico, favicon-16x16.png, favicon-32x32.png
apple-touch-icon.png, icon-192.png, icon-512.png
assets/img/           ← optimized product photography + logos + social share image
```

## Deploy
Pick any one — no build step required:

- **Netlify / Vercel / Cloudflare Pages:** drag-and-drop this folder, or connect the repo. No framework, no build command.
- **GitHub Pages:** push the folder contents to the repo root (or `/docs`) and enable Pages.
- **Any web host / S3 / IIS / Apache / Nginx:** upload the folder; `index.html` is the entry point.

The site is **100% relative-path** — it works from a domain root, a subfolder, or even opened from disk.

## Before you go live — update the domain
The canonical URL, Open Graph tags, sitemap, and structured data currently point to
`https://www.midwestspecialtyrobotics.com/`. If you launch on a different domain,
find-and-replace that string in:
- `index.html` (canonical, og:url, og:image, twitter:image, JSON-LD)
- `sitemap.xml`
- `robots.txt`

## SEO built in
- Title, meta description, keywords, author, robots, theme-color, canonical
- Open Graph + Twitter Card with a custom 1200×630 share image (`assets/img/msr-og-cover.jpg`)
- JSON-LD structured data: Organization, WebSite, and FAQPage
- Descriptive, keyword-rich `alt` text and SEO-friendly image filenames
- Explicit `width`/`height` on images (no layout shift), `loading="lazy"` + `decoding="async"` below the fold
- Sitemap with image entries, robots.txt, favicons + web manifest
- Accessible: skip link, ARIA labels, keyboard-operable nav/menu, reduced-motion support

## Notes
- Fonts: **Montserrat** is loaded from Google Fonts (needs internet); body falls back to Helvetica Neue / Arial.
- The contact form is front-end only (shows a confirmation message). Wire it to a form handler
  (Formspree, Netlify Forms, or your CRM endpoint) before launch.
- Product imagery is KEENON Robotics product photography supplied via the MSR brand assets.
