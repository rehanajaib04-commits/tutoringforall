<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/teacherBookingsView.css">
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
                    <a class="nav-link" href="availability.php?teacher_email=<?= urlencode($_SESSION['email_address']) ?>">
                        <i class="bi bi-calendar-week me-1"></i>My Availability
                    </a>
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

<div class="container py-4">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="sidebar">
                <div class="profile-card text-center p-4 bg-white border rounded-3 mb-3">
                    <div class="profile-avatar bg-dark text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:80px;height:80px;font-size:1.75rem;">
                        <?= strtoupper(substr($_SESSION['email_address'] ?? 'T', 0, 1)) ?>
                    </div>
                    <div class="sidebar-name fw-semibold">Teacher Dashboard</div>
                    <div class="sidebar-role text-secondary small text-capitalize"><?= htmlspecialchars($_SESSION['user_type'] ?? 'teacher') ?></div>
                </div>
                <nav class="sidebar-nav bg-white border rounded-3 overflow-hidden">
                    <a href="myprofile.php" class="d-block p-3 text-dark text-decoration-none border-bottom">My Profile</a>
                    <a href="availability.php?teacher_email=<?= urlencode($_SESSION['email_address']) ?>" class="d-block p-3 text-dark text-decoration-none border-bottom">My Availability</a>
                    <a href="teacherBookings.php" class="d-block p-3 text-white bg-dark text-decoration-none border-bottom">Student Bookings</a>
                    <a href="teacherLessons.php" class="d-block p-3 text-dark text-decoration-none">Manage Status</a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="main-content bg-white border rounded-3 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 fw-normal mb-0">Student Bookings</h1>
                    <span class="badge bg-dark"><?= $bookings_count ?> booking<?= $bookings_count === 1 ? '' : 's' ?></span>
                </div>

                <?php if (!empty($bookedSlots)): ?>
                    <div class="booking-list d-grid gap-3">
                        <?php
                            $today = date('Y-m-d');
                            foreach ($bookedSlots as $booking):
                                $bookingDate = $booking->slot_date;
                                $isToday = ($bookingDate === $today);
                                $isPast = ($bookingDate < $today);
                                
                                if ($isToday) {
                                    $badgeClass = 'bg-warning-subtle text-warning-emphasis';
                                    $badgeText = 'Today';
                                } elseif ($isPast) {
                                    $badgeClass = 'bg-success-subtle text-success-emphasis';
                                    $badgeText = 'Completed';
                                } else {
                                    $badgeClass = 'bg-primary-subtle text-primary-emphasis';
                                    $badgeText = 'Upcoming';
                                }
                                
                                $studentName = trim(($booking->student_first_name ?? '') . ' ' . ($booking->student_last_name ?? ''));
                                $displayDate = date('l, jS F Y', strtotime($bookingDate));
                                $displayTime = date('H:i', strtotime($booking->start_time)) . ' - ' . date('H:i', strtotime($booking->end_time));
                        ?>
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h2 class="h5 fw-normal mb-0"><?= htmlspecialchars($studentName ?: 'Student') ?></h2>
                                        <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-4">
                                            <div class="bg-light p-3 rounded-3">
                                                <span class="text-secondary text-uppercase small fw-semibold">Date</span>
                                                <div class="fw-semibold"><?= htmlspecialchars($displayDate) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="bg-light p-3 rounded-3">
                                                <span class="text-secondary text-uppercase small fw-semibold">Time</span>
                                                <div class="fw-semibold"><?= htmlspecialchars($displayTime) ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="bg-light p-3 rounded-3">
                                                <span class="text-secondary text-uppercase small fw-semibold">Duration</span>
                                                <div class="fw-semibold"><?= htmlspecialchars(calculateDuration($booking->start_time, $booking->end_time)) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($booking->student_email_address)): ?>
                                        <div class="bg-light p-3 rounded-3 mb-3">
                                            <span class="text-secondary text-uppercase small fw-semibold">Student Contact</span>
                                            <div class="fw-semibold"><?= htmlspecialchars($booking->student_email_address) ?>
                                                <?php if (!empty($booking->student_contact_number)): ?>
                                                    <span class="mx-2">|</span> <?= htmlspecialchars($booking->student_contact_number) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex gap-2 pt-3 border-top">
                                        <?php if (!$isPast): ?>
                                            <a href="viewStudentProfile.php?email=<?= urlencode($booking->student_email_address ?? '') ?>" class="btn btn-outline-dark">View Student</a>
                                            <button 
                                                class="btn btn-outline-danger" 
                                                onclick="openCancelModal(
                                                    <?= (int)$booking->slot_id ?>,
                                                    '<?= addslashes(htmlspecialchars($studentName)) ?>',
                                                    '<?= addslashes(htmlspecialchars($displayDate)) ?>',
                                                    '<?= addslashes(htmlspecialchars($displayTime)) ?>'
                                                )"
                                            >Release Slot</button>
                                        <?php else: ?>
                                            <span class="text-secondary small">This lesson has already taken place.</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="display-1 text-secondary mb-3">📅</div>
                        <h3 class="fw-normal">No Student Bookings Yet</h3>
                        <p class="text-secondary">When students book your lessons, they'll appear here.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-normal">Release this slot?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Keep Booking</button>
                <form id="cancelForm" method="POST" action="releaseSlot.php">
                    <input type="hidden" name="slot_id" id="modalSlotId" value="">
                    <button type="submit" class="btn btn-danger">Release Slot</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openCancelModal(slotId, studentName, date, time) {
    document.getElementById('modalSlotId').value = slotId;
    document.getElementById('modalBody').textContent =
        'Are you sure you want to release the slot for ' + studentName +
        ' on ' + date + ' at ' + time + '? The student will be notified and the slot will become available again.';
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}
</script>

<?php
function calculateDuration($start, $end) {
    $startTime = strtotime($start);
    $endTime = strtotime($end);
    $diff = $endTime - $startTime;
    $hours = floor($diff / 3600);
    $minutes = floor(($diff % 3600) / 60);
    
    if ($hours > 0 && $minutes > 0) {
        return $hours . 'h ' . $minutes . 'm';
    } elseif ($hours > 0) {
        return $hours . ' hour' . ($hours > 1 ? 's' : '');
    } else {
        return $minutes . ' minutes';
    }
}
?>
</body>
</html>