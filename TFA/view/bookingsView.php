<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booked Lessons - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/bookingsView.css">
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
                <li class="nav-item">
                    <span class="user-email d-none d-lg-inline"><?= htmlspecialchars($_SESSION['email_address']) ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right me-1"></i>Sign Out
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width: 980px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 fw-normal mb-1">My Current Bookings</h1>
            <p class="text-secondary"><?= htmlspecialchars($booking_identity_label) ?></p>
        </div>
    </div>

    <div class="bg-white border rounded-3 p-3 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-bold fs-5"><?= $current_booking_count ?></span>
            <span class="text-secondary">active booking<?= $current_booking_count === 1 ? '' : 's' ?></span>
        </div>
    </div>

    <?php if (!empty($bookings)): ?>
        <div class="d-grid gap-3 mb-5">
            <?php foreach ($bookings as $booking): ?>
                <?php
                    $teacherName = trim(($booking->teacher_first_name ?? '') . ' ' . ($booking->teacher_last_name ?? ''));
                    $bookingDate = date('l, jS F Y', strtotime($booking->slot_date));
                    $bookingTime = date('H:i', strtotime($booking->start_time)) . ' - ' . date('H:i', strtotime($booking->end_time));
                ?>
                <div class="card border">
                    <div class="card-body">
                        <h2 class="h5 fw-normal mb-3"><?= htmlspecialchars($teacherName) ?></h2>
                        <?php if ($userType === 'parent' && isset($booking->student_first_name)): ?>
                            <div class="badge bg-primary-subtle text-primary-emphasis mb-3 p-2">👤 Booking for: <?= htmlspecialchars($booking->student_first_name . ' ' . $booking->student_last_name) ?></div>
                        <?php endif; ?>
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <div class="bg-light p-3 rounded-3">
                                    <span class="text-secondary text-uppercase small fw-semibold">Subject</span>
                                    <div><?= htmlspecialchars($booking->teacher_type ?? 'General lesson') ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light p-3 rounded-3">
                                    <span class="text-secondary text-uppercase small fw-semibold">Date</span>
                                    <div><?= htmlspecialchars($bookingDate) ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light p-3 rounded-3">
                                    <span class="text-secondary text-uppercase small fw-semibold">Time</span>
                                    <div><?= htmlspecialchars($bookingTime) ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light p-3 rounded-3">
                                    <span class="text-secondary text-uppercase small fw-semibold">Status</span>
                                    <span class="badge bg-success-subtle text-success-emphasis">Booked</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 pt-3 border-top">
                            <a href="viewBookingDetail.php?slot_id=<?= urlencode($booking->slot_id) ?>" class="btn btn-outline-dark">View details</a>
                            <a href="teacherProfile.php?id=<?= urlencode($booking->teacher_email_address) ?>" class="btn btn-outline-dark">View teacher</a>
                            <button
                                class="btn btn-outline-danger"
                                onclick="openCancelModal(
                                    <?= (int)$booking->slot_id ?>,
                                    '<?= addslashes(htmlspecialchars($teacherName)) ?>',
                                    '<?= addslashes(htmlspecialchars($bookingDate)) ?>',
                                    '<?= addslashes(htmlspecialchars($bookingTime)) ?>'
                                )"
                            >Cancel booking</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-white border rounded-3 p-5 text-center text-secondary">You do not have any active bookings yet.</div>
    <?php endif; ?>

    <!-- Invoices Section -->
    <div class="card border rounded-4 mt-5">
        <div class="card-header bg-white border-bottom py-3">
            <h2 class="h5 fw-normal mb-0">My Invoices</h2>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($invoices)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Teacher</th>
                                <?php if ($userType === 'parent'): ?><th>Student</th><?php endif; ?>
                                <th>Date</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $inv): ?>
                                <tr>
                                    <td>#<?= $inv->invoice_number ?></td>
                                    <td><?= htmlspecialchars($inv->teacher_first_name . ' ' . $inv->teacher_last_name) ?></td>
                                    <?php if ($userType === 'parent'): ?>
                                        <td><?= htmlspecialchars($inv->student_first_name . ' ' . $inv->student_last_name) ?></td>
                                    <?php endif; ?>
                                    <td><?= date('d M Y', strtotime($inv->invoice_date)) ?></td>
                                    <td class="text-end fw-semibold">£<?= number_format($inv->Total, 2) ?></td>
                                    <td class="text-center">
                                        <span class="badge 
                                            <?= $inv->status == 'paid' ? 'bg-success-subtle text-success-emphasis' : 
                                               ($inv->status == 'overdue' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-warning-subtle text-warning-emphasis') ?>">
                                            <?= ucfirst($inv->status) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="p-4 text-secondary fst-italic">No invoices yet.</p>
            <?php endif; ?>
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
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Keep booking</button>
                <form id="cancelForm" method="POST" action="cancelBooking.php">
                    <input type="hidden" name="slot_id" id="modalSlotId" value="">
                    <button type="submit" class="btn btn-danger">Yes, cancel it</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openCancelModal(slotId, teacherName, date, time) {
    document.getElementById('modalSlotId').value = slotId;
    document.getElementById('modalBody').textContent =
        'Are you sure you want to cancel your lesson with ' + teacherName +
        ' on ' + date + ' at ' + time + '? The slot will be released and made available to others.';
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}
</script>
</body>
</html>