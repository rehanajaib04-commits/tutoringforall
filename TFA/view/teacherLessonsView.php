<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lessons - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/teacherLessonsView.css">
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
                <li class="nav-item">
                    <span class="user-email d-none d-lg-inline"><?= htmlspecialchars($_SESSION['email_address']) ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="myprofile.php">
                        <i class="bi bi-person me-1"></i>My Profile
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
        <div class="col-lg-3">
            <div class="sidebar">
                <div class="profile-card text-center p-4 bg-white border rounded-3 mb-3">
                    <div class="profile-avatar bg-dark text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:80px;height:80px;font-size:1.75rem;">
                        <?= strtoupper(substr($_SESSION['email_address'] ?? 'T', 0, 1)) ?>
                    </div>
                    <div class="sidebar-name fw-semibold">Teacher Dashboard</div>
                    <div class="sidebar-role text-secondary small">Manage Lessons</div>
                </div>
                <nav class="sidebar-nav bg-white border rounded-3 overflow-hidden">
                    <a href="myprofile.php" class="d-block p-3 text-dark text-decoration-none border-bottom">My Profile</a>
                    <a href="teacherBookings.php" class="d-block p-3 text-dark text-decoration-none border-bottom">Student Bookings</a>
                    <a href="teacherLessons.php" class="d-block p-3 text-white bg-dark text-decoration-none">Manage Status</a>
                </nav>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="main-content bg-white border rounded-3 p-4">
                <h1 class="h3 fw-normal mb-1">Lesson & Payment Management</h1>
                <p class="text-secondary mb-4">Update lesson completion status and payment status for your students.</p>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($bookings)): ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($bookings as $b): 
                            $displayDate = date('l, jS F Y', strtotime($b->slot_date));
                            $displayTime = date('H:i', strtotime($b->start_time)) . ' - ' . date('H:i', strtotime($b->end_time));
                            $studentName = trim($b->student_first_name . ' ' . $b->student_last_name);
                        ?>
                            <div class="card border">
                                <div class="card-body">
                                    <h2 class="h5 fw-normal mb-3"><?= htmlspecialchars($studentName) ?></h2>
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
                                                <span class="text-secondary text-uppercase small fw-semibold">Student Email</span>
                                                <div class="fw-semibold"><?= htmlspecialchars($b->student_email_address) ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4 pt-3 border-top">
                                        <div class="col-md-6">
                                            <h4 class="h6 text-secondary text-uppercase mb-3">Lesson Status</h4>
                                            <form method="POST" class="d-flex gap-2">
                                                <input type="hidden" name="lesson_id" value="<?= $b->lesson_id ?>">
                                                <input type="hidden" name="update_booking_status" value="1">
                                                <select name="new_status" class="form-select">
                                                    <option value="scheduled" <?= $b->booking_status === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                                    <option value="completed" <?= $b->booking_status === 'completed' ? 'selected' : '' ?>>Completed</option>
                                                    <option value="cancelled" <?= $b->booking_status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                                <button type="submit" class="btn btn-dark">Update</button>
                                            </form>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="h6 text-secondary text-uppercase mb-3">Payment Status</h4>
                                            <?php if ($b->invoice_number): ?>
                                                <form method="POST" class="d-flex gap-2">
                                                    <input type="hidden" name="invoice_number" value="<?= $b->invoice_number ?>">
                                                    <input type="hidden" name="update_invoice_status" value="1">
                                                    <select name="new_status" class="form-select">
                                                        <option value="unpaid" <?= $b->invoice_status === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                                        <option value="paid" <?= $b->invoice_status === 'paid' ? 'selected' : '' ?>>Paid</option>
                                                        <option value="overdue" <?= $b->invoice_status === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-dark">Update</button>
                                                </form>
                                                <div class="mt-2 small text-secondary">Amount: <strong>£<?= number_format($b->invoice_total, 2) ?></strong></div>
                                            <?php else: ?>
                                                <span class="text-secondary fst-italic">No invoice found</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="display-1 text-secondary mb-3">📚</div>
                        <h3 class="fw-normal">No Lessons Found</h3>
                        <p class="text-secondary">You don't have any scheduled lessons yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>