<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson Details - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/slotDetailView.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="teacherlist.php" class="navbar-brand">Tutoring For All</a>
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

<div class="container d-flex align-items-center justify-content-center py-5">
    <div class="card border rounded-4 p-4 p-md-5" style="max-width: 520px;">
        <h1 class="h3 fw-normal mb-1">Lesson Details</h1>
        <p class="text-secondary mb-4">Review the next available lesson slot before you book it.</p>
        <div class="bg-light p-3 rounded-3 mb-4">
            <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary text-uppercase small fw-semibold">Teacher</span><span class="fw-semibold"><?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?></span></div>
            <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary text-uppercase small fw-semibold">Subject</span><span class="fw-semibold"><?= htmlspecialchars($teacher->teacher_type ?? 'General Lessons') ?></span></div>
            <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary text-uppercase small fw-semibold">Day</span><span class="fw-semibold"><?= htmlspecialchars($display_day) ?></span></div>
            <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary text-uppercase small fw-semibold">Date</span><span class="fw-semibold"><?= htmlspecialchars($display_date) ?></span></div>
            <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary text-uppercase small fw-semibold">Time</span><span class="fw-semibold"><?= htmlspecialchars($display_start) ?> - <?= htmlspecialchars($display_end) ?></span></div>
            <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary text-uppercase small fw-semibold">Duration</span><span class="fw-semibold"><?= $duration_hours ?> hour<?= $duration_hours != 1 ? 's' : '' ?></span></div>
            <?php if ($hourly_rate): ?>
            <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary text-uppercase small fw-semibold">Rate</span><span class="fw-semibold">£<?= number_format($hourly_rate, 2) ?>/hour</span></div>
            <div class="d-flex justify-content-between py-2 bg-success-subtle rounded-2 mt-2 px-2"><span class="fw-semibold">Total Cost</span><span class="fw-bold text-success">£<?= number_format($total_cost, 2) ?></span></div>
            <?php endif; ?>
        </div>
        <div class="alert alert-warning mb-4">Booking this slot reserves the next available occurrence only.</div>
        <div class="d-flex gap-2">
            <a href="availability.php?teacher_email=<?= urlencode($teacher->email_address) ?>&day=<?= urlencode($slot->weekday) ?>" class="btn btn-outline-secondary">← Back</a>
            <?php if ($canBook): ?>
                <form action="confirmBooking.php" method="POST" class="flex-grow-1">
                    <input type="hidden" name="slot_id" value="<?= htmlspecialchars($slot->slot_id) ?>">
                    <input type="hidden" name="teacher_email" value="<?= htmlspecialchars($teacher->email_address) ?>">
                    <button type="submit" class="btn btn-dark w-100" onclick="return confirm('Book <?= htmlspecialchars($display_day) ?> <?= htmlspecialchars($display_date) ?> at <?= htmlspecialchars($display_start) ?> for £<?= number_format($total_cost ?? 0, 2) ?>?')">Book (£<?= number_format($total_cost ?? 0, 2) ?>)</button>
                </form>
            <?php else: ?>
                <a href="sign_in.php" class="btn btn-dark flex-grow-1">Sign In to Book</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>