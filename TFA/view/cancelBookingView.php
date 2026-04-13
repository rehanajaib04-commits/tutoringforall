<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $success ? 'Booking Cancelled' : 'Cancellation Failed' ?> - Tutoring For All</title>
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

<div class="container d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="card border rounded-4 p-5 text-center" style="max-width: 520px;">
        <?php if ($success): ?>
            <div class="display-1 text-success mb-3">✅</div>
            <h1 class="h3 fw-normal mb-3">Booking Cancelled</h1>
            <p class="text-secondary mb-4">Your lesson has been successfully cancelled and the slot is now available for others to book.</p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="bookingspage.php" class="btn btn-dark">Back to My Bookings</a>
                <a href="teacherlist.php" class="btn btn-outline-dark">Find a Tutor</a>
            </div>
        <?php else: ?>
            <div class="display-1 text-danger mb-3">❌</div>
            <h1 class="h3 fw-normal mb-3">Cancellation Failed</h1>
            <p class="text-secondary mb-4"><?= htmlspecialchars($error_message) ?></p>
            <a href="bookingspage.php" class="btn btn-dark">Back to My Bookings</a>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>