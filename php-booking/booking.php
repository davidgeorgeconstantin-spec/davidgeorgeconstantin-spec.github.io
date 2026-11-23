<?php
/**
 * Pedal & Co. (fictional bike-repair shop) — booking form with server-side validation.
 * Demo concept by David Constantin. Plain PHP 7.4+, no framework, single file.
 *
 * What it shows:
 *  - Server-side validation (never trust the browser)
 *  - CSRF token + honeypot field against spam bots
 *  - Escaping all output with htmlspecialchars() (XSS protection)
 *  - Post/Redirect/Get so a refresh does not resend the form
 *  - Email header-injection protection
 */

declare(strict_types=1);
session_start();

const SERVICES   = ['tune-up' => 'Basic tune-up', 'brakes' => 'Brake service', 'wheel' => 'Wheel truing', 'full' => 'Full service'];
const OPEN_HOURS = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
const SHOP_EMAIL = 'bookings@example.com'; // fictional address

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** Character count that works with or without the mbstring extension. */
function text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : (int) preg_match_all('/./us', $value);
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

/**
 * Validates the submitted data. Returns [cleanData, errors].
 */
function validate_booking(array $in): array
{
    $data = [
        'name'    => trim((string) ($in['name'] ?? '')),
        'email'   => trim((string) ($in['email'] ?? '')),
        'phone'   => preg_replace('/[\s().-]/', '', (string) ($in['phone'] ?? '')),
        'service' => (string) ($in['service'] ?? ''),
        'date'    => (string) ($in['date'] ?? ''),
        'time'    => (string) ($in['time'] ?? ''),
        'notes'   => trim((string) ($in['notes'] ?? '')),
    ];
    $errors = [];

    $nameLength = text_length($data['name']);
    if ($nameLength < 2 || $nameLength > 80) {
        $errors['name'] = 'Please enter your name (2–80 characters).';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (!preg_match('/^\+?[0-9]{9,15}$/', $data['phone'])) {
        $errors['phone'] = 'Please enter a valid phone number (9–15 digits).';
    }
    if (!array_key_exists($data['service'], SERVICES)) {
        $errors['service'] = 'Please choose a service from the list.';
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $data['date']);
    $today = new DateTimeImmutable('today');
    if (!$date || $date->format('Y-m-d') !== $data['date']) {
        $errors['date'] = 'Please choose a valid date.';
    } elseif ($date < $today) {
        $errors['date'] = 'The date cannot be in the past.';
    } elseif ($date > $today->modify('+60 days')) {
        $errors['date'] = 'You can book up to 60 days ahead.';
    } elseif ((int) $date->format('N') === 7) {
        $errors['date'] = 'We are closed on Sundays.';
    }

    if (!in_array($data['time'], OPEN_HOURS, true)) {
        $errors['time'] = 'Please choose one of the available time slots.';
    }
    if (text_length($data['notes']) > 500) {
        $errors['notes'] = 'Notes can be at most 500 characters.';
    }

    return [$data, $errors];
}

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. CSRF check
    if (!hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Invalid form token. Please reload the page and try again.');
    }
    // 2. Honeypot: real people never fill the hidden "website" field
    if (!empty($_POST['website'])) {
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?sent=1', true, 303);
        exit;
    }

    [$old, $errors] = validate_booking($_POST);

    if (!$errors) {
        // 3. Remove line breaks so nobody can inject extra mail headers
        $safeEmail = str_replace(["\r", "\n"], '', $old['email']);
        $body = sprintf(
            "New booking\n\nName: %s\nEmail: %s\nPhone: %s\nService: %s\nDate: %s at %s\nNotes: %s\n",
            $old['name'], $safeEmail, $old['phone'], SERVICES[$old['service']], $old['date'], $old['time'], $old['notes']
        );
        $headers = 'From: website@example.com' . "\r\n" . 'Reply-To: ' . $safeEmail;
        @mail(SHOP_EMAIL, 'New booking: ' . SERVICES[$old['service']], $body, $headers);

        $_SESSION['csrf'] = bin2hex(random_bytes(32)); // one-time token
        // 4. Post/Redirect/Get
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?sent=1', true, 303);
        exit;
    }
}

$sent = isset($_GET['sent']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book a service — Pedal &amp; Co. (demo)</title>
</head>
<body>
<main>
    <h1>Book a bike service</h1>
    <p><em>Demo concept for a fictional business.</em></p>

    <?php if ($sent) : ?>
        <p role="status">Thank you! Your booking request was received. We will confirm by email.</p>
    <?php endif; ?>

    <?php if ($errors) : ?>
        <div role="alert">
            <p>Please fix <?= count($errors) ?> field(s):</p>
            <ul>
                <?php foreach ($errors as $field => $message) : ?>
                    <li><a href="#<?= e($field) ?>"><?= e($message) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="" novalidate>
        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
        <div hidden><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>

        <label for="name">Full name</label>
        <input id="name" name="name" autocomplete="name" required value="<?= e($old['name'] ?? '') ?>"
               <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="name-err"' : '' ?>>
        <?php if (isset($errors['name'])) : ?><p id="name-err"><?= e($errors['name']) ?></p><?php endif; ?>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="email" required value="<?= e($old['email'] ?? '') ?>"
               <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-err"' : '' ?>>
        <?php if (isset($errors['email'])) : ?><p id="email-err"><?= e($errors['email']) ?></p><?php endif; ?>

        <label for="phone">Phone</label>
        <input id="phone" name="phone" type="tel" autocomplete="tel" required value="<?= e($old['phone'] ?? '') ?>"
               <?= isset($errors['phone']) ? 'aria-invalid="true" aria-describedby="phone-err"' : '' ?>>
        <?php if (isset($errors['phone'])) : ?><p id="phone-err"><?= e($errors['phone']) ?></p><?php endif; ?>

        <label for="service">Service</label>
        <select id="service" name="service" required>
            <option value="">Choose…</option>
            <?php foreach (SERVICES as $key => $label) : ?>
                <option value="<?= e($key) ?>" <?= ($old['service'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['service'])) : ?><p><?= e($errors['service']) ?></p><?php endif; ?>

        <label for="date">Date</label>
        <input id="date" name="date" type="date" required min="<?= e(date('Y-m-d')) ?>" value="<?= e($old['date'] ?? '') ?>">
        <?php if (isset($errors['date'])) : ?><p><?= e($errors['date']) ?></p><?php endif; ?>

        <label for="time">Time</label>
        <select id="time" name="time" required>
            <option value="">Choose…</option>
            <?php foreach (OPEN_HOURS as $slot) : ?>
                <option <?= ($old['time'] ?? '') === $slot ? 'selected' : '' ?>><?= e($slot) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['time'])) : ?><p><?= e($errors['time']) ?></p><?php endif; ?>

        <label for="notes">Notes (optional)</label>
        <textarea id="notes" name="notes" maxlength="500"><?= e($old['notes'] ?? '') ?></textarea>
        <?php if (isset($errors['notes'])) : ?><p><?= e($errors['notes']) ?></p><?php endif; ?>

        <button type="submit">Request booking</button>
    </form>
</main>
</body>
</html>
