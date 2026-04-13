<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Detail - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/Bookingdetailview.css">
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

<div class="container py-5" style="max-width: 680px;">
    <a href="bookingspage.php" class="btn btn-link text-secondary text-decoration-none px-0 mb-3">
        <i class="bi bi-arrow-left me-1"></i>Back to My Bookings
    </a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 fw-normal">Booking Detail</h1>
    </div>

    <div class="card border rounded-4 p-4">
        <h2 class="h4 fw-normal mb-4 pb-3 border-bottom"><?= htmlspecialchars(trim($teacher->first_name . ' ' . $teacher->last_name)) ?></h2>
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="bg-light p-3 rounded-3">
                    <span class="text-secondary text-uppercase small fw-semibold">Subject</span>
                    <div class="fw-semibold"><?= htmlspecialchars($teacher->teacher_type ?? 'General lesson') ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-3 rounded-3">
                    <span class="text-secondary text-uppercase small fw-semibold">Date</span>
                    <div class="fw-semibold"><?= htmlspecialchars($display_date) ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-3 rounded-3">
                    <span class="text-secondary text-uppercase small fw-semibold">Time</span>
                    <div class="fw-semibold"><?= htmlspecialchars($display_start . ' – ' . $display_end) ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-3 rounded-3">
                    <span class="text-secondary text-uppercase small fw-semibold">Status</span>
                    <span class="badge bg-success-subtle text-success-emphasis">Booked</span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 pt-3 border-top">
            <a href="teacherProfile.php?id=<?= urlencode($teacher->email_address) ?>" class="btn btn-outline-dark">View teacher profile</a>
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel this booking</button>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-normal">Cancel this lesson?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel your lesson with
                <strong><?= htmlspecialchars(trim($teacher->first_name . ' ' . $teacher->last_name)) ?></strong>
                on <?= htmlspecialchars($display_date) ?> at <?= htmlspecialchars($display_start) ?>?
                The slot will be released and made available to others.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Keep booking</button>
                <form method="POST" action="cancelBooking.php">
                    <input type="hidden" name="slot_id" value="<?= (int)$slot->slot_id ?>">
                    <input type="hidden" name="redirect" value="bookingspage.php">
                    <button type="submit" class="btn btn-danger">Yes, cancel it</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>