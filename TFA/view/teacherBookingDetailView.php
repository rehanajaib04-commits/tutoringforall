<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .detail-card { border: none; border-radius: 1rem; background: #fff; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
        .info-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; margin-bottom: .25rem; }
        .info-value { font-size: 1.1rem; font-weight: 500; }
        .status-badge-lg { font-size: 1rem; padding: .5rem 1rem; border-radius: .5rem; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid px-4">
        <a href="homepage.php" class="navbar-brand fw-bold">Tutoring For All</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item"><a class="nav-link" href="myprofile.php"><i class="bi bi-person me-1"></i>My Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Sign Out</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="flex-grow-1 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <a href="myprofile.php#bookings" class="text-dark text-decoration-none mb-3 d-inline-block">
                    <i class="bi bi-arrow-left me-1"></i>Back to Bookings
                </a>

                <div class="detail-card p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
                        <h1 class="h3 mb-0 fw-normal">Booking Details</h1>
                        <?php if ($booking->invoice_status === 'paid'): ?>
                            <span class="badge bg-success-subtle text-success status-badge-lg"><i class="bi bi-check-circle me-1"></i>Paid</span>
                        <?php elseif ($booking->invoice_status === 'overdue'): ?>
                            <span class="badge bg-danger-subtle text-danger status-badge-lg"><i class="bi bi-exclamation-circle me-1"></i>Overdue</span>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning status-badge-lg"><i class="bi bi-hourglass me-1"></i>Unpaid</span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Student</div>
                        <div class="info-value"><?= htmlspecialchars(trim(($booking->student_first_name ?? '') . ' ' . ($booking->student_last_name ?? ''))) ?></div>
                        <div class="text-secondary small"><?= htmlspecialchars($booking->student_email_address ?? '') ?></div>
                        <?php if (!empty($booking->student_contact)): ?>
                            <div class="text-secondary small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($booking->student_contact) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="info-label">Date</div>
                            <div class="info-value"><i class="bi bi-calendar me-2 text-secondary"></i><?= htmlspecialchars($display_date) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Time</div>
                            <div class="info-value"><i class="bi bi-clock me-2 text-secondary"></i><?= htmlspecialchars($display_start) ?> – <?= htmlspecialchars($display_end) ?></div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="info-label">Duration</div>
                            <div class="info-value"><?= number_format($duration, 1) ?> hours</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Hourly Rate</div>
                            <div class="info-value">&pound;<?= number_format($hourly_rate, 2) ?></div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded mb-4">
                        <div class="info-label">Total Due</div>
                        <div class="info-value fs-4">&pound;<?= number_format($total_due, 2) ?></div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Update Payment Status</h5>
                    <form method="POST" action="teacherBookingDetail.php?slot_id=<?= (int)$slot_id ?>" class="mb-3">
                        <input type="hidden" name="action" value="update_payment">
                        <?php if ($booking->invoice_number): ?>
                            <input type="hidden" name="invoice_number" value="<?= htmlspecialchars($booking->invoice_number) ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <select name="status" class="form-select form-select-lg" required>
                                <option value="unpaid" <?= (($booking->invoice_status ?? 'unpaid') === 'unpaid') ? 'selected' : '' ?>>Unpaid</option>
                                <option value="paid" <?= (($booking->invoice_status ?? '') === 'paid') ? 'selected' : '' ?>>Paid</option>
                                <option value="overdue" <?= (($booking->invoice_status ?? '') === 'overdue') ? 'selected' : '' ?>>Overdue</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 py-2">
                            <i class="bi bi-save me-2"></i>Save Status
                        </button>
                    </form>

                    <hr class="my-4">

                    <form method="POST" action="teacherBookingDetail.php?slot_id=<?= (int)$slot_id ?>" onsubmit="return confirm('Cancel this booking? The student will be notified.');">
                        <input type="hidden" name="action" value="cancel_booking">
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-2"></i>Cancel Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>