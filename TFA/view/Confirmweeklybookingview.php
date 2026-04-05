<!DOCTYPE html>
<html>
<head>
    <title><?= $success ? 'Booking Confirmed' : 'Booking Failed' ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: #f5f5f5; color: #333; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; border-bottom: 2px solid #333; background: #fff; }
        .navbar .logo { font-size: 24px; text-decoration: none; color: #333; }
        .nav-links a { text-decoration: none; color: #333; margin-left: 20px; font-size: 15px; padding: 7px 14px; border: 1px solid #ccc; border-radius: 4px; }
        .nav-links a:hover { background: #f0f0f0; }
        .nav-links span { margin-left: 20px; font-size: 14px; color: #555; }
        .container { width: 80%; max-width: 580px; margin: 60px auto; }
        .card { background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 36px; text-align: center; }
        .icon { font-size: 48px; margin-bottom: 16px; }
        .card h1 { font-size: 26px; font-weight: normal; margin: 0 0 10px; }
        .card p { color: #666; margin: 0 0 24px; font-size: 15px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 28px; text-align: left; }
        .detail-item { background: #fafafa; border: 1px solid #eee; border-radius: 8px; padding: 12px 14px; }
        .detail-item span { display: block; font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px; }
        .detail-item strong { font-size: 15px; }
        .recurs-note { background: #e8f4e8; border: 1px solid #b8ddb8; border-radius: 8px; padding: 12px 16px; font-size: 14px; color: #2a7a2a; margin-bottom: 24px; text-align: left; }
        .btn { display: inline-block; padding: 11px 24px; border-radius: 6px; text-decoration: none; font-size: 15px; margin: 4px; }
        .btn-primary { background: #333; color: #fff; border: 1px solid #333; }
        .btn-primary:hover { background: #111; }
        .btn-secondary { background: #fff; color: #333; border: 1px solid #ccc; }
        .btn-secondary:hover { background: #f0f0f0; }
        .error-card { border-color: #f5c6c6; }
        .error-card h1 { color: #b00; }
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
        <?php endif; ?>
    </div>
</nav>
<div class="container">
    <?php if ($success): ?>
        <div class="card">
            <div class="icon">✅</div>
            <h1>Weekly Booking Confirmed!</h1>
            <p>Your recurring lesson has been booked successfully.</p>
            <div class="detail-grid">
                <div class="detail-item"><span>Teacher</span><strong><?= htmlspecialchars($teacher_email) ?></strong></div>
                <div class="detail-item"><span>Repeats</span><strong>Every <?= htmlspecialchars($day) ?></strong></div>
                <div class="detail-item"><span>First Lesson</span><strong><?= htmlspecialchars($date) ?></strong></div>
                <div class="detail-item"><span>Time</span><strong><?= htmlspecialchars($time) ?></strong></div>
            </div>
            <div class="recurs-note">🔁 This lesson will repeat every <strong><?= htmlspecialchars($day) ?></strong> at <strong><?= htmlspecialchars(explode(' - ', $time)[0]) ?></strong>.</div>
            <a href="bookingspage.php" class="btn btn-primary">View My Bookings</a>
            <a href="teacherlist.php" class="btn btn-secondary">Find More Tutors</a>
        </div>
    <?php else: ?>
        <div class="card error-card">
            <div class="icon">❌</div>
            <h1>Booking Failed</h1>
            <p><?= htmlspecialchars($error_message) ?></p>
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
            <a href="bookingspage.php" class="btn btn-primary">My Bookings</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>