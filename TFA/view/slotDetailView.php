<!DOCTYPE html>
<html>
<head>
    <title>Lesson Details</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: #f5f5f5; color: #333; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; border-bottom: 2px solid #333; background: #fff; }
        .navbar .logo { font-size: 24px; text-decoration: none; color: #333; }
        .nav-links a { text-decoration: none; color: #333; margin-left: 20px; font-size: 15px; padding: 7px 14px; border: 1px solid #ccc; border-radius: 4px; }
        .nav-links a:hover { background: #f0f0f0; }
        .nav-links span { margin-left: 20px; font-size: 14px; color: #555; }
        .page-body { flex: 1; display: flex; align-items: center; justify-content: center; padding: 50px 20px; }
        .card { background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 50px 55px; max-width: 520px; width: 100%; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .card h1 { font-size: 26px; font-weight: normal; margin: 0 0 6px; }
        .card .subtitle { font-size: 14px; color: #777; margin-bottom: 30px; }
        .detail-block { border: 1px solid #eee; border-radius: 8px; padding: 20px 22px; margin-bottom: 25px; background: #fafafa; }
        .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee; font-size: 15px; }
        .detail-row:last-child { border-bottom: none; }
        .lbl { color: #777; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.04em; }
        .val { font-size: 16px; font-weight: bold; color: #222; text-align: right; }
        .info-note { background: #fff8e1; border: 1px solid #f0c040; border-radius: 6px; padding: 13px 16px; font-size: 13px; color: #555; margin-bottom: 25px; line-height: 1.5; }
        .btn-group { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { flex: 1; text-align: center; padding: 12px 16px; font-size: 15px; text-decoration: none; border-radius: 5px; border: 2px solid #333; color: #333; transition: all 0.15s ease; background: #fff; }
        .btn:hover { background: #f0f0f0; }
        .btn-primary { background: #333; color: #fff; cursor: pointer; }
        .btn-primary:hover { background: #555; color: #fff; }
        @media (max-width: 480px) { .card { padding: 30px 22px; } .navbar { padding: 12px 20px; } .nav-links { display: none; } .btn-group { flex-direction: column; } }
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
<div class="page-body">
    <div class="card">
        <h1>Lesson Details</h1>
        <p class="subtitle">Review the next available lesson slot before you book it.</p>
        <div class="detail-block">
            <div class="detail-row"><span class="lbl">Teacher</span><span class="val"><?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?></span></div>
            <div class="detail-row"><span class="lbl">Subject</span><span class="val"><?= htmlspecialchars($teacher->teacher_type ?? 'General Lessons') ?></span></div>
            <div class="detail-row"><span class="lbl">Day</span><span class="val"><?= htmlspecialchars($display_day) ?></span></div>
            <div class="detail-row"><span class="lbl">Date</span><span class="val"><?= htmlspecialchars($display_date) ?></span></div>
            <div class="detail-row"><span class="lbl">Time</span><span class="val"><?= htmlspecialchars($display_start) ?> - <?= htmlspecialchars($display_end) ?></span></div>
        </div>
        <div class="info-note">Booking this slot reserves the next available occurrence only. After you book it, the number of open slots shown on the availability page will update.</div>
        <div class="btn-group">
            <a href="availability.php?teacher_email=<?= urlencode($teacher->email_address) ?>&day=<?= urlencode($slot->weekday) ?>" class="btn">&larr; Back</a>
            <?php if ($canBook): ?>
                <form action="confirmBooking.php" method="POST" style="flex: 1; margin: 0;">
    <input type="hidden" name="slot_id" value="<?= htmlspecialchars($slot->slot_id) ?>">
    <input type="hidden" name="teacher_email" value="<?= htmlspecialchars($teacher->email_address) ?>">
    <button type="submit" class="btn btn-primary" style="width: 100%;" onclick="return confirm('Book <?= htmlspecialchars($display_day) ?> <?= htmlspecialchars($display_date) ?> at <?= htmlspecialchars($display_start) ?>?')">Book This Slot</button>
</form>
            <?php else: ?>
                <a href="sign_in.php" class="btn btn-primary">Sign In to Book</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
