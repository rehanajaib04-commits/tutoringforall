<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $success ? 'Booking Confirmed' : 'Booking Failed' ?> - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
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
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5 d-flex justify-content-center">
    <div class="card border rounded-4 p-4 p-md-5 text-center <?= $success ? '' : 'border-danger' ?>" style="max-width: 580px;">
        <?php if ($success): ?>
            <div class="display-1 text-success mb-3">✅</div>
            <h1 class="h3 fw-normal mb-3">Weekly Booking Confirmed!</h1>
            <p class="text-secondary mb-4">Your recurring lesson has been booked successfully.</p>
            <div class="row g-3 mb-4 text-start">
                <div class="col-6">
                    <div class="bg-light p-3 rounded-3">
                        <span class="text-secondary text-uppercase small fw-semibold">Teacher</span>
                        <div class="fw-semibold"><?= htmlspecialchars($teacher_email) ?></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light p-3 rounded-3">
                        <span class="text-secondary text-uppercase small fw-semibold">Repeats</span>
                        <div class="fw-semibold">Every <?= htmlspecialchars($day) ?></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light p-3 rounded-3">
                        <span class="text-secondary text-uppercase small fw-semibold">First Lesson</span>
                        <div class="fw-semibold"><?= htmlspecialchars($date) ?></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-light p-3 rounded-3">
                        <span class="text-secondary text-uppercase small fw-semibold">Time</span>
                        <div class="fw-semibold"><?= htmlspecialchars($time) ?></div>
                    </div>
                </div>
            </div>
            <div class="alert alert-success mb-4">🔁 This lesson will repeat every <strong><?= htmlspecialchars($day) ?></strong> at <strong><?= htmlspecialchars(explode(' - ', $time)[0]) ?></strong>.</div>
            <div class="d-flex gap-2 justify-content-center">
                <a href="bookingspage.php" class="btn btn-dark">View My Bookings</a>
                <a href="teacherlist.php" class="btn btn-outline-dark">Find More Tutors</a>
            </div>
        <?php else: ?>
            <div class="display-1 text-danger mb-3">❌</div>
            <h1 class="h3 fw-normal mb-3">Booking Failed</h1>
            <p class="text-secondary mb-4"><?= htmlspecialchars($error_message) ?></p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">Go Back</a>
                <a href="bookingspage.php" class="btn btn-dark">My Bookings</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>