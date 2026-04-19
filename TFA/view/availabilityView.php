<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Availability - <?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?> - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/availabilityView.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="homepage.php" class="navbar-brand">Tutoring For All</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <?php if (isset($_SESSION['email_address'])): ?>
                    <li class="nav-item">
                        <span class="user-email d-none d-lg-inline"><?= htmlspecialchars($_SESSION['email_address']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bookingspage.php">
                            <i class="bi bi-calendar-check me-1"></i>My Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Sign Out
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="sign_in.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    <a href="teacherProfile.php?id=<?= urlencode($teacher->email_address) ?>" class="btn btn-link text-secondary text-decoration-none px-0 mb-3">
        <i class="bi bi-arrow-left me-1"></i>Back to <?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?>'s Profile
    </a>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-normal mb-1">Availability</h1>
            <p class="text-secondary"><?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?> — <?= htmlspecialchars($teacher->teacher_type ?? 'General Lessons') ?></p>
        </div>
    </div>

    <?php
        $loggedIn = isset($_SESSION['email_address']);
        $userType = strtolower($_SESSION['user_type'] ?? '');
        $canBook  = $loggedIn && in_array($userType, ['student', 'parent'], true);
    ?>

    <div class="d-flex justify-content-between align-items-center bg-white border rounded-3 p-3 mb-4">
        <div>
            <span class="fw-bold fs-5"><?= $total_open_slots ?></span>
            <span class="text-secondary">open slot<?= $total_open_slots === 1 ? '' : 's' ?> available</span>
        </div>
        <?php if ($canBook): ?>
            <a href="bookingspage.php" class="btn btn-outline-dark">View current bookings</a>
        <?php endif; ?>
    </div>

    <?php if (!$loggedIn): ?>
        <div class="alert alert-warning"><a href="sign_in.php" class="alert-link">Sign in</a> as a student or parent to book a lesson.</div>
    <?php elseif (!$canBook): ?>
        <div class="alert alert-warning">Only student or parent accounts can book lessons.</div>
    <?php endif; ?>

    <!-- Date tabs -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php foreach ($all_dates as $d): ?>
            <?php
                $count    = count($by_date[$d]);
                $isActive = ($d === $selected_date);
                $classes  = 'btn ' . ($isActive ? 'btn-dark' : 'btn-outline-secondary');
            ?>
            <a href="availability.php?teacher_email=<?= urlencode($teacher->email_address) ?>&date=<?= urlencode($d) ?>" class="<?= $classes ?> d-flex flex-column align-items-center py-2 px-3" style="min-width: 90px;">
                <span class="fw-semibold"><?= date('D', strtotime($d)) ?></span>
                <span class="small"><?= date('d M', strtotime($d)) ?></span>
                <span class="small text-secondary"><?= $count ?> slot<?= $count === 1 ? '' : 's' ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Slots for selected date -->
    <div class="bg-white border rounded-4 p-4">
        <h2 class="h4 fw-normal mb-1"><?= $selected_date ? date('l, jS F Y', strtotime($selected_date)) : 'No dates available' ?></h2>
        <p class="text-secondary mb-4"><?= count($slots_today) ?> slot<?= count($slots_today) === 1 ? '' : 's' ?> available on this date.</p>
        <div class="d-flex flex-wrap gap-3">
            <?php if (!empty($slots_today)): ?>
                <?php foreach ($slots_today as $slot): ?>
                    <?php
                        $start = date('H:i', strtotime($slot->start_time));
                        $end   = date('H:i', strtotime($slot->end_time));
                    ?>
                    <?php if ($canBook): ?>
                        <a href="viewSlotDetail.php?slot_id=<?= urlencode($slot->slot_id) ?>&teacher_email=<?= urlencode($teacher->email_address) ?>" class="btn btn-outline-dark d-flex flex-column align-items-center py-3 px-4" style="min-width: 150px;">
                            <span class="fw-bold fs-5"><?= htmlspecialchars($start) ?></span>
                            <span class="small text-secondary">until <?= htmlspecialchars($end) ?></span>
                        </a>
                    <?php else: ?>
                        <div class="btn btn-outline-secondary disabled d-flex flex-column align-items-center py-3 px-4" style="min-width: 150px;" title="Sign in to book">
                            <span class="fw-bold fs-5"><?= htmlspecialchars($start) ?></span>
                            <span class="small">until <?= htmlspecialchars($end) ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-secondary fst-italic">No open slots available on this date.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>