# David Constantin — Portfolio v2

Static portfolio for George David Constantin, junior web developer (HTML, CSS, JavaScript, WordPress, PHP).
Plain HTML/CSS/JS, no framework, no build step.

- `index.html` — home: hero, projects, skills, "what employers ask for", services, about, process, contact, CV download
- `projects/<slug>/` — 6 case studies (problem, solution, features, tech, desktop + mobile screenshots, source code)
- `demos/` — 6 live demos, each clearly labelled as a demo concept for a fictional business:
  restaurant, salon, plumber, launch (SaaS), wp-theme (static preview of the WordPress theme), booking (JS version of the PHP form)
- `wordpress-theme/crumb/` + `crumb.zip` — real minimal classic WordPress theme (style.css, functions.php, header.php, footer.php, index.php, page.php, single.php)
- `php-booking/booking.php` — PHP booking form with server-side validation, CSRF, honeypot, escaping, PRG
- `cv/` — CV PDF; `sitemap.xml`, `robots.txt`, `404.html`, `vercel.json`

Deploy on Vercel: framework "Other", no build command, output directory `.`.
Canonical/OG URLs assume https://davidgeorgeconstantin-spec.github.io — update SITE in the build script if the domain differs.
