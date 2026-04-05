<!DOCTYPE html>
<html>
<head>
    <title>Availability - <?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f5f5f5; color: #333; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; border-bottom: 2px solid #333; background: #fff; }
        .navbar .logo { font-size: 24px; text-decoration: none; color: #333; }
        .nav-links a { text-decoration: none; color: #333; margin-left: 20px; font-size: 15px; padding: 7px 14px; border: 1px solid #ccc; border-radius: 4px; }
        .nav-links a:hover { background: #f0f0f0; }
        .nav-links span { margin-left: 20px; font-size: 14px; color: #555; }
        .container { width: 80%; max-width: 900px; margin: 40px auto; flex: 1; }
        .back-link { display: inline-block; text-decoration: none; color: #666; font-size: 14px; margin-bottom: 16px; }
        .back-link:hover { color: #333; text-decoration: underline; }
        .page-header { margin-bottom: 20px; padding-bottom: 18px; border-bottom: 1px solid #ccc; }
        .page-header h1 { font-size: 30px; font-weight: normal; margin: 0 0 4px; }
        .page-header p { margin: 0; color: #666; font-size: 15px; }
        .summary-bar { display: flex; justify-content: space-between; align-items: center; gap: 16px; background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 16px 18px; margin-bottom: 22px; }
        .summary-bar strong { display: block; font-size: 18px; margin-bottom: 3px; }
        .summary-bar span { color: #666; font-size: 13px; }
        .summary-actions a { text-decoration: none; color: #333; border: 1px solid #333; border-radius: 5px; padding: 9px 14px; font-size: 14px; }
        .summary-actions a:hover { background: #333; color: #fff; }
        .notice-banner { background: #fff8e1; border: 1px solid #f0c040; border-radius: 6px; padding: 13px 18px; margin-bottom: 22px; font-size: 14px; }
        .notice-banner a { color: #333; font-weight: bold; text-decoration: underline; }
        .date-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 28px; }
        .date-tab { display: flex; flex-direction: column; align-items: center; text-decoration: none; padding: 12px 10px; min-width: 90px; border: 2px solid #ccc; border-radius: 8px; background: #fff; color: #333; font-size: 13px; transition: all 0.15s ease; }
        .date-tab .date-day { font-size: 15px; font-weight: bold; margin-bottom: 2px; }
        .date-tab .date-label { font-size: 11px; color: #888; }
        .date-tab .slot-count { font-size: 11px; color: #888; margin-top: 3px; }
        .date-tab:hover { border-color: #333; background: #f9f9f9; }
        .date-tab.active { border-color: #333; background: #333; color: #fff; }
        .date-tab.active .date-label, .date-tab.active .slot-count { color: #ccc; }
        .slots-panel { background: #fff; border: 1px solid #ccc; border-radius: 10px; padding: 30px; }
        .slots-panel h2 { font-size: 22px; font-weight: normal; margin: 0 0 6px; padding-bottom: 14px; border-bottom: 1px solid #eee; }
        .slots-subtitle { margin: 0 0 20px; color: #666; font-size: 14px; }
        .time-grid { display: flex; flex-wrap: wrap; gap: 14px; }
        .time-slot-btn, .time-slot-disabled { display: flex; flex-direction: column; align-items: center; justify-content: center; width: 150px; padding: 14px 10px; border-radius: 8px; text-decoration: none; }
        .time-slot-btn { border: 2px solid #333; background: #fff; cursor: pointer; color: #333; transition: all 0.15s ease; }
        .time-slot-btn:hover { background: #333; color: #fff; }
        .time-slot-disabled { border: 2px solid #ddd; background: #f9f9f9; color: #bbb; cursor: not-allowed; }
        .t-start { font-size: 20px; font-weight: bold; }
        .t-end { font-size: 12px; margin-top: 3px; }
        .time-slot-btn .t-end { color: #666; }
        .time-slot-btn:hover .t-end { color: #ddd; }
        .no-slots-msg { color: #888; font-style: italic; padding: 10px 0; }
        @media (max-width: 680px) { .container { width: 92%; } .date-tab { min-width: calc(25% - 8px); } .time-slot-btn, .time-slot-disabled { width: calc(50% - 7px); } .navbar { padding: 12px 20px; } .nav-links { display: none; } .summary-bar { flex-direction: column; align-items: flex-start; } }
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
    <a href="teacherProfile.php?id=<?= urlencode($teacher->email_address) ?>" class="back-link">&larr; Back to <?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?>'s Profile</a>
    <div class="page-header">
        <h1>Availability</h1>
        <p><?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?> &mdash; <?= htmlspecialchars($teacher->teacher_type ?? 'General Lessons') ?></p>
    </div>

    <?php
        $loggedIn = isset($_SESSION['email_address']);
        $userType = $_SESSION['user_type'] ?? '';
        $canBook  = $loggedIn && in_array($userType, ['student', 'parent'], true);
    ?>

    <div class="summary-bar">
        <div>
            <strong><?= $total_open_slots ?></strong>
            <span>open slot<?= $total_open_slots === 1 ? '' : 's' ?> available to book</span>
        </div>
        <?php if ($canBook): ?>
            <div class="summary-actions"><a href="bookingspage.php">View current bookings</a></div>
        <?php endif; ?>
    </div>

    <?php if (!$loggedIn): ?>
        <div class="notice-banner"><a href="sign_in.php">Sign in</a> as a student or parent to book a lesson.</div>
    <?php elseif (!$canBook): ?>
        <div class="notice-banner">Only student or parent accounts can book lessons.</div>
    <?php endif; ?>

    <!-- Date tabs -->
    <div class="date-tabs">
        <?php foreach ($all_dates as $d): ?>
            <?php
                $count    = count($by_date[$d]);
                $isActive = ($d === $selected_date);
                $classes  = 'date-tab' . ($isActive ? ' active' : '');
            ?>
            <a href="availability.php?teacher_email=<?= urlencode($teacher->email_address) ?>&date=<?= urlencode($d) ?>" class="<?= $classes ?>">
                <span class="date-day"><?= date('D', strtotime($d)) ?></span>
                <span class="date-label"><?= date('d M', strtotime($d)) ?></span>
                <span class="slot-count"><?= $count ?> slot<?= $count === 1 ? '' : 's' ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Slots for selected date -->
    <div class="slots-panel">
        <h2><?= $selected_date ? date('l, jS F Y', strtotime($selected_date)) : 'No dates available' ?></h2>
        <p class="slots-subtitle"><?= count($slots_today) ?> slot<?= count($slots_today) === 1 ? '' : 's' ?> available on this date.</p>
        <div class="time-grid">
            <?php if (!empty($slots_today)): ?>
                <?php foreach ($slots_today as $slot): ?>
                    <?php
                        $start = date('H:i', strtotime($slot->start_time));
                        $end   = date('H:i', strtotime($slot->end_time));
                    ?>
                    <?php if ($canBook): ?>
                        <a href="viewSlotDetail.php?slot_id=<?= urlencode($slot->slot_id) ?>&teacher_email=<?= urlencode($teacher->email_address) ?>" class="time-slot-btn">
                            <span class="t-start"><?= htmlspecialchars($start) ?></span>
                            <span class="t-end">until <?= htmlspecialchars($end) ?></span>
                        </a>
                    <?php else: ?>
                        <div class="time-slot-disabled" title="Sign in to book a lesson">
                            <span class="t-start"><?= htmlspecialchars($start) ?></span>
                            <span class="t-end">until <?= htmlspecialchars($end) ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-slots-msg">No open slots available on this date.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>