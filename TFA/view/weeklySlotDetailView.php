<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Lesson Slot - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/weeklySlotDetailView.css">
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

<div class="container py-5" style="max-width: 640px;">
    <a href="availability.php?teacher_email=<?= urlencode($teacher->email_address) ?>" class="btn btn-link text-secondary text-decoration-none px-0 mb-3">
        <i class="bi bi-arrow-left me-1"></i>Back to availability
    </a>
    <div class="card border rounded-4 p-4">
        <span class="badge bg-success-subtle text-success-emphasis mb-3 align-self-start">🔁 Weekly Recurring Lesson</span>
        <h1 class="h3 fw-normal"><?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?></h1>
        <p class="text-secondary"><?= htmlspecialchars($teacher->teacher_type ?? 'General Lessons') ?></p>

        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="bg-light p-3 rounded-3">
                    <span class="text-secondary text-uppercase small fw-semibold">Day</span>
                    <div class="fw-semibold">Every <?= htmlspecialchars($display_day) ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-3 rounded-3">
                    <span class="text-secondary text-uppercase small fw-semibold">Time</span>
                    <div class="fw-semibold"><?= htmlspecialchars($display_start) ?> – <?= htmlspecialchars($display_end) ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-3 rounded-3">
                    <span class="text-secondary text-uppercase small fw-semibold">First Lesson</span>
                    <div class="fw-semibold"><?= htmlspecialchars($display_date) ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-3 rounded-3">
                    <span class="text-secondary text-uppercase small fw-semibold">Teacher</span>
                    <div class="fw-semibold"><?= htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name) ?></div>
                </div>
            </div>
        </div>

        <div class="alert alert-success mb-4">
            🔁 This is a <strong>weekly recurring</strong> booking. The lesson will repeat every <?= htmlspecialchars($display_day) ?> at <?= htmlspecialchars($display_start) ?>.
        </div>

        <?php if ($canBook): ?>
            <form method="POST" action="confirmWeeklyBooking.php">
                <input type="hidden" name="availability_id" value="<?= htmlspecialchars($slot->availability_id) ?>">
                <input type="hidden" name="teacher_email"   value="<?= htmlspecialchars($teacher->email_address) ?>">
                <button type="submit" class="btn btn-dark btn-lg w-100">Confirm Weekly Booking</button>
            </form>
        <?php else: ?>
            <p class="text-secondary"><a href="sign_in.php" class="fw-semibold text-dark">Sign in</a> as a student or parent to book this lesson.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>