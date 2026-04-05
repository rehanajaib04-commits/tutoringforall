<!DOCTYPE html>
<html>
<head>
    <title>Weekly Lesson Slot</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: #f5f5f5; color: #333; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; border-bottom: 2px solid #333; background: #fff; }
        .navbar .logo { font-size: 24px; text-decoration: none; color: #333; }
        .nav-links a { text-decoration: none; color: #333; margin-left: 20px; font-size: 15px; padding: 7px 14px; border: 1px solid #ccc; border-radius: 4px; }
        .nav-links a:hover { background: #f0f0f0; }
        .nav-links span { margin-left: 20px; font-size: 14px; color: #555; }
        .container { width: 80%; max-width: 640px; margin: 40px auto; }
        .back-link { display: inline-block; text-decoration: none; color: #666; font-size: 14px; margin-bottom: 20px; }
        .back-link:hover { color: #333; text-decoration: underline; }
        .card { background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 32px; }
        .card h1 { font-size: 26px; font-weight: normal; margin: 0 0 6px; }
        .card .subtitle { color: #666; font-size: 14px; margin: 0 0 24px; }
        .badge-weekly { display: inline-block; font-size: 12px; padding: 3px 10px; border-radius: 20px; background: #e8f4e8; color: #2a7a2a; border: 1px solid #b8ddb8; margin-bottom: 20px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 28px; }
        .detail-item { background: #fafafa; border: 1px solid #eee; border-radius: 8px; padding: 14px; }
        .detail-item span { display: block; font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .detail-item strong { font-size: 16px; font-weight: 600; }
        .recurs-note { background: #e8f4e8; border: 1px solid #b8ddb8; border-radius: 8px; padding: 14px 16px; font-size: 14px; color: #2a7a2a; margin-bottom: 24px; }
        .btn-book { display: inline-block; background: #333; color: #fff; padding: 12px 28px; border-radius: 6px; text-decoration: none; font-size: 16px; border: none; cursor: pointer; }
        .btn-book:hover { background: #111; }
        .login-note { font-size: 14px; color: #888; }
        .login-note a { color: #333; font-weight: bold; }
    </style>
</head>
<body>
<nav class="navbar">
    <a href="teacherlist.php" class="logo">TutorFind</a>
    <div class="nav-links">
        <?php if (isset($_SESSION['email_address'])): ?>
            <span>Logged in as: <?= htmlspecialchars($_SESSION['email_address']) ?></span>
            <a href="bookingspage.php">My Bookings</a>
            <a href="logout.php">Sign Out</a>
        <?php else: ?>
            <a href="sign_in.php">Sign In</a>
        <?php endif; ?>
    </div>
</nav>
<div class="container">
    <a href="availability.php?teacher_email=<?= urlencode($teacher->email_address) ?>" class="back-link">&larr; Back to availability</a>
    <div class="card">
        <span class="badge-weekly">🔁 Weekly Recurring Lesson</span>
        <h1><?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?></h1>
        <p class="subtitle"><?= htmlspecialchars($teacher->teacher_type ?? 'General Lessons') ?></p>

        <div class="detail-grid">
            <div class="detail-item">
                <span>Day</span>
                <strong>Every <?= htmlspecialchars($display_day) ?></strong>
            </div>
            <div class="detail-item">
                <span>Time</span>
                <strong><?= htmlspecialchars($display_start) ?> – <?= htmlspecialchars($display_end) ?></strong>
            </div>
            <div class="detail-item">
                <span>First Lesson</span>
                <strong><?= htmlspecialchars($display_date) ?></strong>
            </div>
            <div class="detail-item">
                <span>Teacher</span>
                <strong><?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?></strong>
            </div>
        </div>

        <div class="recurs-note">
            🔁 This is a <strong>weekly recurring</strong> booking. The lesson will repeat every <?= htmlspecialchars($display_day) ?> at <?= htmlspecialchars($display_start) ?>.
        </div>

        <?php if ($canBook): ?>
            <form method="POST" action="confirmWeeklyBooking.php">
                <input type="hidden" name="availability_id" value="<?= htmlspecialchars($slot->availability_id) ?>">
                <input type="hidden" name="teacher_email"   value="<?= htmlspecialchars($teacher->email_address) ?>">
                <button type="submit" class="btn-book">Confirm Weekly Booking</button>
            </form>
        <?php else: ?>
            <p class="login-note"><a href="sign_in.php">Sign in</a> as a student or parent to book this lesson.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>