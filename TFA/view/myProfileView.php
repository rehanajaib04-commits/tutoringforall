<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/myProfileView.css">
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
                    <a class="nav-link" href="teacherlist.php">
                        <i class="bi bi-search me-1"></i>Find Teachers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="myprofile.php">
                        <i class="bi bi-person me-1"></i>My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <span class="user-email d-none d-lg-inline"><?= htmlspecialchars($session_email) ?></span>
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
                <div class="profile-card">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($user->first_name ?? '?', 0, 1) . substr($user->last_name ?? '', 0, 1)) ?>
                    </div>
                    <div class="sidebar-name">
                        <?= htmlspecialchars(trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))) ?>
                    </div>
                    <div class="sidebar-role"><?= htmlspecialchars($userType) ?></div>
                </div>
                
                <nav class="sidebar-nav">
                    <a href="#profile" class="active"><i class="bi bi-person-vcard me-2"></i>My Details</a>
                    <?php if ($userType === 'teacher'): ?>
                        <a href="#rate"><i class="bi bi-currency-pound me-2"></i>Hourly Rate</a>
                        <a href="#slots"><i class="bi bi-calendar-week me-2"></i>Lesson Slots</a>
                        <a href="teacherBookings.php"><i class="bi bi-journal-bookmark me-2"></i>Student Bookings</a>
                    <?php elseif ($userType === 'parent'): ?>
                        <a href="#students"><i class="bi bi-people me-2"></i>My Students</a>
                        <a href="#bookings"><i class="bi bi-calendar-check me-2"></i>Student Bookings</a>
                    <?php else: ?>
                        <a href="#bookings"><i class="bi bi-calendar-check me-2"></i>My Bookings</a>
                    <?php endif; ?>
                    <a href="#password"><i class="bi bi-shield-lock me-2"></i>Change Password</a>
                    <a href="#delete" class="text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Delete Account</a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="main-content">
                <div class="page-header">
                    <h1>My Profile</h1>
                    <p>Manage your account settings and preferences</p>
                </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success mb-4">
                        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success_message) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <!-- Personal Details -->
                <section id="profile" class="section-card">
                    <div class="section-title">
                        <span><i class="bi bi-person-vcard me-2"></i>Personal Details</span>
                        <span>Changes save immediately</span>
                    </div>
                    <form method="POST" action="myprofile.php">
                        <input type="hidden" name="action" value="update_profile">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user->first_name ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user->last_name ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($user->contact_number ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($session_email) ?>" disabled>
                                <div class="form-text">Email address cannot be changed</div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-save me-2"></i>Save Changes
                        </button>
                    </form>
                </section>

                <!-- Change Password -->
                <section id="password" class="section-card">
                    <div class="section-title">
                        <span><i class="bi bi-shield-lock me-2"></i>Change Password</span>
                        <span>Leave blank to keep current</span>
                    </div>
                    <form method="POST" action="myprofile.php">
                        <input type="hidden" name="action" value="update_profile">
                        <input type="hidden" name="first_name" value="<?= htmlspecialchars($user->first_name ?? '') ?>">
                        <input type="hidden" name="last_name" value="<?= htmlspecialchars($user->last_name ?? '') ?>">
                        <input type="hidden" name="contact_number" value="<?= htmlspecialchars($user->contact_number ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Enter new password" autocomplete="new-password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password" autocomplete="new-password">
                            </div>
                        </div>
                        <hr class="my-4">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-key me-2"></i>Update Password
                        </button>
                    </form>
                </section>

                <?php if ($userType === 'parent'): ?>
                <!-- My Students -->
                <section id="students" class="section-card">
                    <div class="section-title">
                        <span><i class="bi bi-people me-2"></i>My Students</span>
                        <span><?= count($linkedStudents) ?> student<?= count($linkedStudents) === 1 ? '' : 's' ?></span>
                    </div>
                    
                    <?php if (!empty($linkedStudents)): ?>
                        <?php foreach ($linkedStudents as $index => $student): ?>
                        <div class="mb-4 <?= $index > 0 ? 'pt-4 border-top' : '' ?>">
                            <h3 class="h5 mb-1"><?= htmlspecialchars(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?></h3>
                            <p class="text-muted small mb-3"><?= htmlspecialchars($student->email_address) ?></p>
                            
                            <form method="POST" action="myprofile.php">
                                <input type="hidden" name="action" value="update_student_profile">
                                <input type="hidden" name="student_email" value="<?= htmlspecialchars($student->email_address) ?>">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($student->first_name ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($student->last_name ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($student->contact_number ?? '') ?>">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-dark btn-sm">
                                        <i class="bi bi-save me-2"></i>Update Student
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted fst-italic">No students linked to your account.</p>
                    <?php endif; ?>
                </section>

                <!-- Bookings (Parent) -->
                <section id="bookings" class="section-card">
                    <div class="section-title">
                        <span><i class="bi bi-calendar-check me-2"></i>Student Bookings</span>
                        <span><?= count($bookings) ?> active</span>
                    </div>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $b): ?>
                            <div class="booking-item">
                                <div>
                                    <strong class="d-block mb-1"><?= htmlspecialchars(trim(($b->teacher_first_name ?? '') . ' ' . ($b->teacher_last_name ?? ''))) ?></strong>
                                    <span class="text-muted small">
                                        <?= htmlspecialchars($b->teacher_type ?? 'General lesson') ?> &middot;
                                        <?= htmlspecialchars(date('D d M Y', strtotime($b->slot_date))) ?> &middot;
                                        <?= htmlspecialchars(date('H:i', strtotime($b->start_time)) . ' – ' . date('H:i', strtotime($b->end_time))) ?>
                                    </span>
                                    <span class="d-block mt-1 text-success small">
                                        <i class="bi bi-person-circle me-1"></i>For: <?= htmlspecialchars(($b->student_first_name ?? '') . ' ' . ($b->student_last_name ?? '')) ?>
                                    </span>
                                </div>
                                <a href="viewBookingDetail.php?slot_id=<?= urlencode($b->slot_id) ?>" class="btn btn-outline-dark btn-sm">
                                    View <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                        <a href="bookingspage.php" class="btn btn-link text-dark text-decoration-none ps-0">
                            View all bookings <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <p class="text-muted fst-italic">No active bookings for your students.</p>
                        <a href="teacherlist.php" class="btn btn-dark btn-sm">
                            <i class="bi bi-search me-2"></i>Find a Tutor
                        </a>
                    <?php endif; ?>
                </section>

                <?php elseif ($userType === 'teacher' && $teacher): ?>
                <!-- Hourly Rate -->
                <section id="rate" class="section-card">
                    <div class="section-title">
                        <span><i class="bi bi-currency-pound me-2"></i>Hourly Rate</span>
                    </div>
                    <?php $currentRate = getTeacherRate($session_email); ?>
                    <?php if ($currentRate !== null): ?>
                        <div class="rate-display mb-1">£<?= number_format((float)$currentRate, 2) ?></div>
                        <div class="text-muted small mb-4">per hour &mdash; visible to students and parents</div>
                    <?php endif; ?>
                    <form method="POST" action="myprofile.php">
                        <input type="hidden" name="action" value="update_rate">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Rate (&pound; per hour)</label>
                                <input type="number" name="hourly_rate" min="0" step="0.50" class="form-control" 
                                       value="<?= htmlspecialchars($currentRate ?? '') ?>" placeholder="e.g. 35.00">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-dark">
                                    <i class="bi bi-save me-2"></i>Save Rate
                                </button>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- Lesson Slots -->
                <section id="slots" class="section-card">
                    <div class="section-title">
                        <span><i class="bi bi-calendar-week me-2"></i>Lesson Slots</span>
                        <span>Upcoming only</span>
                    </div>

                    <form method="POST" action="myprofile.php" class="slot-form">
                        <input type="hidden" name="action" value="add_slot">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Date</label>
                                <input type="date" name="slot_date" min="<?= date('Y-m-d') ?>" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Time</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-dark w-100">
                                    <i class="bi bi-plus-lg"></i> Add
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php $allSlots = getTeacherAllSlots($session_email); ?>
                    <?php if (!empty($allSlots)): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Status</th>
                                        <th>Student</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allSlots as $slot): ?>
                                        <tr>
                                            <td><?= htmlspecialchars(date('D d M Y', strtotime($slot->slot_date))) ?></td>
                                            <td><?= htmlspecialchars(date('H:i', strtotime($slot->start_time))) ?></td>
                                            <td><?= htmlspecialchars(date('H:i', strtotime($slot->end_time))) ?></td>
                                            <td>
                                                <?php if ($slot->is_booked): ?>
                                                    <span class="badge bg-warning-subtle">Booked</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success-subtle">Open</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $slot->is_booked ? htmlspecialchars($slot->student_email_address ?? '—') : '—' ?></td>
                                            <td>
                                                <?php if ($slot->is_booked): ?>
                                                    <form method="POST" action="myprofile.php" class="d-inline" 
                                                          onsubmit="return confirm('Cancel this booking? The student will be notified.');">
                                                        <input type="hidden" name="action" value="cancel_booking">
                                                        <input type="hidden" name="slot_id" value="<?= htmlspecialchars($slot->slot_id) ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            Cancel
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" action="myprofile.php" class="d-inline"
                                                          onsubmit="return confirm('Delete this slot?');">
                                                        <input type="hidden" name="action" value="delete_slot">
                                                        <input type="hidden" name="slot_id" value="<?= htmlspecialchars($slot->slot_id) ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="bi bi-trash me-1"></i>Delete
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted fst-italic">No upcoming slots. Add one above.</p>
                    <?php endif; ?>
                </section>

                <?php elseif ($userType === 'student'): ?>
                <!-- Bookings (Student) -->
                <section id="bookings" class="section-card">
                    <div class="section-title">
                        <span><i class="bi bi-calendar-check me-2"></i>My Bookings</span>
                        <span><?= count($bookings) ?> active</span>
                    </div>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $b): ?>
                            <div class="booking-item">
                                <div>
                                    <strong class="d-block mb-1"><?= htmlspecialchars(trim(($b->teacher_first_name ?? '') . ' ' . ($b->teacher_last_name ?? ''))) ?></strong>
                                    <span class="text-muted small">
                                        <?= htmlspecialchars($b->teacher_type ?? 'General lesson') ?> &middot;
                                        <?= htmlspecialchars(date('D d M Y', strtotime($b->slot_date))) ?> &middot;
                                        <?= htmlspecialchars(date('H:i', strtotime($b->start_time)) . ' – ' . date('H:i', strtotime($b->end_time))) ?>
                                    </span>
                                </div>
                                <a href="viewBookingDetail.php?slot_id=<?= urlencode($b->slot_id) ?>" class="btn btn-outline-dark btn-sm">
                                    View <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                        <a href="bookingspage.php" class="btn btn-link text-dark text-decoration-none ps-0">
                            View all bookings <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <p class="text-muted fst-italic">You have no active bookings.</p>
                        <a href="teacherlist.php" class="btn btn-dark btn-sm">
                            <i class="bi bi-search me-2"></i>Find a Tutor
                        </a>
                    <?php endif; ?>
                </section>
                <?php endif; ?>

                <!-- Delete Account -->
                <section id="delete" class="danger-zone">
                    <div class="section-title">
                        <span><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</span>
                    </div>
                    <h3 class="h5 text-danger mb-3">Delete Account</h3>
                    <p class="text-muted mb-4">
                        This cannot be undone. 
                        <?php if ($userType === 'parent'): ?>
                            Your students' accounts will remain active.
                        <?php elseif ($userType === 'teacher'): ?>
                            All your lesson slots and booking history will be removed.
                        <?php else: ?>
                            All your bookings will be cancelled.
                        <?php endif; ?>
                    </p>
                    
                    <form method="POST" action="myprofile.php" onsubmit="return confirm('Permanently delete your account? This cannot be undone.');">
                        <input type="hidden" name="action" value="delete_account">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Type DELETE to confirm</label>
                                <input type="text" name="confirm_text" class="form-control" placeholder="DELETE" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Confirm password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Your password" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-2"></i>Delete Account
                                </button>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.sidebar-nav a').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                document.querySelectorAll('.sidebar-nav a').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        }
    });
});
</script>
</body>
</html>