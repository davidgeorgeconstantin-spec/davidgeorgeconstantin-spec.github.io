# Pedal & Co. booking form (PHP demo)

Demo concept for a fictional bike-repair shop. One PHP file, no framework.

Run locally:

    php -S localhost:8000
    # open http://localhost:8000/booking.php

Features: server-side validation, CSRF token, honeypot, escaped output (XSS),
Post/Redirect/Get, email header-injection protection.

The portfolio is hosted as a static site, so the live demo at /demos/booking/
re-implements the same validation rules in JavaScript. This file is the real
server-side version.
