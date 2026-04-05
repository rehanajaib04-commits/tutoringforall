<!DOCTYPE html>
<html>
<head>
    <title>My Booked Lessons</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; color: #333; background: #f5f5f5; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; border-bottom: 2px solid #333; background: #fff; }
        .navbar .logo { font-size: 24px; font-weight: normal; text-decoration: none; color: #333; }
        .nav-links a { text-decoration: none; color: #333; margin-left: 20px; font-size: 16px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; }
        .nav-links a:hover { background-color: #f0f0f0; }
        .nav-links span { margin-left: 20px; font-size: 14px; color: #555; }
        .container { width: 82%; max-width: 980px; margin: 40px auto; }
        .page-header { margin-bottom: 24px; border-bottom: 1px solid #ccc; padding-bottom: 16px; }
        .page-header h1 { margin: 0 0 8px; font-size: 32px; font-weight: normal; }
        .summary-card { background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 18px 20px; margin-bottom: 24px; }
        .summary-card strong { display: block; font-size: 22px; }
        .summary-card span { color: #666; font-size: 14px; }
        .booking-list { display: grid; gap: 18px; }
        .booking-card { background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 22px; }
        .booking-card h2 { margin: 0 0 12px; font-size: 22px; font-weight: normal; }
        .booking-meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px 18px; }
        .booking-meta div { background: #fafafa; border: 1px solid #eee; border-radius: 8px; padding: 12px 14px; }
        .booking-meta span { display: block; color: #777; font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; }
        .booking-actions { margin-top: 16px; }
        .booking-actions a { text-decoration: none; color: #333; border: 1px solid #333; border-radius: 5px; padding: 9px 14px; display: inline-block; }
        .booking-actions a:hover { background: #333; color: #fff; }
        .empty-state { background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 30px; color: #666; text-align: center; }
        .subnote { margin-top: 8px; color: #666; font-size: 14px; }
        @media (max-width: 768px) { .container { width: 92%; } .booking-meta { grid-template-columns: 1fr; } .nav-links { display: none; } }
    </style>
</head>
<body>
<nav class="navbar">
    <a href="teacherlist.php" class="logo">TutorFind</a>
    <div class="nav-links">
        <span>Logged in as: <?= htmlspecialchars($_SESSION['email_address']) ?></span>
        <a href="logout.php">Sign Out</a>
    </div>
</nav>
<div class="container">
    <div class="page-header">
        <h1>My Current Bookings</h1>
        <p>View the lesson slots you have already booked.</p>
        <p class="subnote"><?= htmlspecialchars($booking_identity_label) ?></p>
    </div>
    <div class="summary-card">
        <strong><?= $current_booking_count ?></strong>
        <span>active booking<?= $current_booking_count === 1 ? '' : 's' ?></span>
    </div>
    <?php if (!empty($bookings)): ?>
        <div class="booking-list">
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <h2><?= htmlspecialchars(trim(($booking->teacher_first_name ?? '') . ' ' . ($booking->teacher_last_name ?? ''))) ?></h2>
                    <div class="booking-meta">
                        <div>
                            <span>Subject</span>
                            <?= htmlspecialchars($booking->teacher_type ?? 'General lesson') ?>
                        </div>
                        <div>
                            <span>Date</span>
                            <?= htmlspecialchars(date('l, jS F Y', strtotime($booking->slot_date))) ?>
                        </div>
                        <div>
                            <span>Time</span>
                            <?= htmlspecialchars(date('H:i', strtotime($booking->start_time)) . ' - ' . date('H:i', strtotime($booking->end_time))) ?>
                        </div>
                        <div>
                            <span>Status</span>
                            Booked
                        </div>
                    </div>
                    <div class="booking-actions">
                        <a href="teacherProfile.php?id=<?= urlencode($booking->teacher_email_address) ?>">View teacher</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">You do not have any active bookings yet.</div>
    <?php endif; ?>
</div>
</body>
</html>