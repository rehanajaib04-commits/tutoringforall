<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body { background: #f8f9fa; }
        .booking-card {
            border: 1px solid #e9ecef;
            border-radius: 0.75rem;
            background: #fff;
            padding: 1.25rem;
            transition: box-shadow .2s ease;
        }
        .booking-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .section-title { font-size: 0.875rem; font-weight: 500; color: #6c757d; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid px-4">
        <a href="homepage.php" class="navbar-brand ">Tutoring For All</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item"><a class="nav-link" href="teacherlist.php"><i class="bi bi-search me-1"></i>Find Teachers</a></li>
                <li class="nav-item"><a class="nav-link" href="myprofile.php"><i class="bi bi-person me-1"></i>My Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Sign Out</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="flex-grow-1 py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 mb-1"><i class="bi bi-calendar-check me-2"></i>Student Bookings</h2>
                <p class="text-secondary mb-0 small"><?= htmlspecialchars($booking_identity_label) ?></p>
            </div>
            <span class="badge bg-light text-dark border"><?= $current_booking_count ?> active</span>
        </div>

        <?php if (!empty($cancel_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($cancel_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($cancel_error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($cancel_error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($feedback_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($feedback_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($feedback_error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($feedback_error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">You have no upcoming bookings.</div>
        <?php else: ?>
            <div class="vstack gap-3">
                <?php foreach ($bookings as $booking):
                    $teacherName = htmlspecialchars(($booking->teacher_first_name ?? '') . ' ' . ($booking->teacher_last_name ?? ''));
                    $teacherType = htmlspecialchars($booking->teacher_type ?? 'Private Tutor (All ages)');
                    $date = date('D d M Y', strtotime($booking->slot_date));
                    $start = substr($booking->start_time, 0, 5);
                    $end   = substr($booking->end_time, 0, 5);
                    $studentName = htmlspecialchars(($booking->student_first_name ?? '') . ' ' . ($booking->student_last_name ?? ''));
                    $studentEmail = htmlspecialchars($booking->student_email ?? $booking->student_email_address ?? '');
                ?>
                <div class="booking-card d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                    <div>
                        <h5 class="mb-1 fw-semibold"><?= $teacherName ?></h5>
                        <p class="text-secondary mb-1 small">
                            <?= $teacherType ?> · <?= $date ?> · <?= $start ?> - <?= $end ?>
                        </p>
                        <?php if ($studentName !== ''): ?>
                            <p class="text-success mb-0 small"><i class="bi bi-person-circle me-1"></i>For: <?= $studentName ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2 flex-wrap justify-content-start justify-content-sm-end">
                        <button type="button"
                                class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#feedbackModal"
                                data-slot-id="<?= $booking->slot_id ?>"
                                data-user-type="<?= htmlspecialchars($userType) ?>">
                            <i class="bi bi-chat-left-text me-1"></i>Feedback
                        </button>

                        <?php if ($userType === 'teacher'): ?>
                            <a href="teacherBookingDetail.php?slot_id=<?= (int)$booking->slot_id ?>" class="btn btn-outline-dark btn-sm">
                                <i class="bi bi-gear me-1"></i>Manage
                            </a>
                        <?php endif; ?>

                        <?php if (in_array($userType, ['student', 'parent'], true)): ?>
                        <button type="button"
                                class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#cancelConfirmModal"
                                data-slot-id="<?= $booking->slot_id ?>"
                                data-student-email="<?= $studentEmail ?>"
                                data-teacher-name="<?= $teacherName ?>"
                                data-date="<?= date('l, d F Y', strtotime($booking->slot_date)) ?>"
                                data-time="<?= $start ?> - <?= $end ?>">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <?php endif; ?>

                        <button type="button"
                                class="btn btn-dark btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#bookingDetailModal"
                                data-teacher-name="<?= $teacherName ?>"
                                data-teacher-type="<?= $teacherType ?>"
                                data-date="<?= date('l, d F Y', strtotime($booking->slot_date)) ?>"
                                data-time="<?= $start ?> - <?= $end ?>"
                                data-student-name="<?= $studentName ?>"
                                data-student-email="<?= $studentEmail ?>">
                            View <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Feedback JSON payload for JS -->
            <?php foreach ($bookings as $booking): ?>
            <script type="application/json" class="feedback-data" data-slot-id="<?= $booking->slot_id ?>">
<?= json_encode([
    'teacher_email'    => $booking->teacher_email_address ?? '',
    'student_email'    => $booking->student_email ?? $booking->student_email_address ?? '',
    'parent_email'     => $booking->parent_email_address ?? '',
    'student_feedback' => $booking->student_feedback_text ?? '',
    'parent_feedback'  => $booking->parent_feedback_text ?? '',
    'teacher_name'     => ($booking->teacher_first_name ?? '') . ' ' . ($booking->teacher_last_name ?? '')
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
            </script>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="mt-4">
            <a href="teacherlist.php" class="text-dark text-decoration-none fw-semibold">View all bookings →</a>
        </div>
    </div>
</main>

<!-- Booking Detail Modal -->
<div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="mb-3">
                    <span class="section-title text-uppercase d-block mb-1">Teacher</span>
                    <span class="fw-semibold fs-5" id="modal-teacher-name"></span>
                </div>
                <div class="mb-3">
                    <span class="section-title text-uppercase d-block mb-1">Type</span>
                    <span id="modal-teacher-type"></span>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <span class="section-title text-uppercase d-block mb-1">Date</span>
                        <span id="modal-date"></span>
                    </div>
                    <div class="col-6">
                        <span class="section-title text-uppercase d-block mb-1">Time</span>
                        <span id="modal-time"></span>
                    </div>
                </div>
                <div id="modal-student-wrapper" class="d-none">
                    <span class="section-title text-uppercase d-block mb-1">Student</span>
                    <span id="modal-student-name"></span>
                    <div class="text-secondary small" id="modal-student-email"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <a href="teacherlist.php" class="btn btn-dark">Find More Teachers</a>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Cancel Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <p class="mb-1">Are you sure you want to cancel this booking?</p>
                <div class="bg-light p-3 rounded mt-3">
                    <div class="mb-1"><strong id="cancel-teacher-name"></strong></div>
                    <div class="text-secondary small" id="cancel-date-time"></div>
                </div>
                <p class="text-secondary small mt-3 mb-0">This action cannot be undone. The time slot will be released for others to book.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Keep Booking</button>
                <form method="POST" action="" class="d-inline">
                    <input type="hidden" name="cancel_slot_id" id="cancel-slot-id" value="">
                    <input type="hidden" name="cancel_student_email" id="cancel-student-email" value="">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Yes, Cancel Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold"><i class="bi bi-chat-left-text me-2"></i>Lesson Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="mb-3">
                    <span class="section-title text-uppercase d-block mb-1">Teacher</span>
                    <span class="fw-semibold fs-5" id="feedback-teacher-name"></span>
                </div>
                <div id="feedback-content"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* ---------- Feedback Data ---------- */
    const feedbackData = {};
    document.querySelectorAll('.feedback-data').forEach(el => {
        feedbackData[el.dataset.slotId] = JSON.parse(el.textContent);
    });

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderReadOnly(title, text) {
        const display = text
            ? escapeHtml(text).replace(/\n/g, '<br>')
            : '<em class="text-secondary">No feedback available yet.</em>';
        return `
            <div class="mb-3">
                <span class="section-title text-uppercase d-block mb-1">${title}</span>
                <div class="bg-light p-3 rounded">${display}</div>
            </div>
        `;
    }

    function renderTeacherForms(data) {
        return `
            <form method="POST" action="" class="mb-3">
                <input type="hidden" name="submit_feedback" value="1">
                <input type="hidden" name="teacher_email" value="${escapeHtml(data.teacher_email)}">
                <input type="hidden" name="feedback_type" value="parent">
                <input type="hidden" name="recipient_email" value="${escapeHtml(data.parent_email)}">
                <div class="mb-2">
                    <span class="section-title text-uppercase d-block mb-1">Feedback for Parent</span>
                    <textarea name="feedback_text" class="form-control" rows="3" placeholder="Write feedback for parent...">${escapeHtml(data.parent_feedback)}</textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-dark btn-sm">Save Parent Feedback</button>
                </div>
            </form>
            <hr>
            <form method="POST" action="">
                <input type="hidden" name="submit_feedback" value="1">
                <input type="hidden" name="teacher_email" value="${escapeHtml(data.teacher_email)}">
                <input type="hidden" name="feedback_type" value="student">
                <input type="hidden" name="recipient_email" value="${escapeHtml(data.student_email)}">
                <div class="mb-2">
                    <span class="section-title text-uppercase d-block mb-1">Feedback for Student</span>
                    <textarea name="feedback_text" class="form-control" rows="3" placeholder="Write feedback for student...">${escapeHtml(data.student_feedback)}</textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-dark btn-sm">Save Student Feedback</button>
                </div>
            </form>
        `;
    }

    /* ---------- Booking Detail Modal ---------- */
    const detailModal = document.getElementById('bookingDetailModal');
    detailModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;

        document.getElementById('modal-teacher-name').textContent = btn.getAttribute('data-teacher-name');
        document.getElementById('modal-teacher-type').textContent = btn.getAttribute('data-teacher-type');
        document.getElementById('modal-date').textContent = btn.getAttribute('data-date');
        document.getElementById('modal-time').textContent = btn.getAttribute('data-time');

        const studentName = btn.getAttribute('data-student-name').trim();
        const studentEmail = btn.getAttribute('data-student-email').trim();
        const studentWrapper = document.getElementById('modal-student-wrapper');

        if (studentName || studentEmail) {
            document.getElementById('modal-student-name').textContent = studentName || '—';
            document.getElementById('modal-student-email').textContent = studentEmail;
            studentWrapper.classList.remove('d-none');
        } else {
            studentWrapper.classList.add('d-none');
        }
    });

    /* ---------- Cancel Confirmation Modal ---------- */
    const cancelModal = document.getElementById('cancelConfirmModal');
    cancelModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;

        document.getElementById('cancel-slot-id').value = btn.getAttribute('data-slot-id');
        document.getElementById('cancel-student-email').value = btn.getAttribute('data-student-email');
        document.getElementById('cancel-teacher-name').textContent = btn.getAttribute('data-teacher-name');
        document.getElementById('cancel-date-time').textContent = btn.getAttribute('data-date') + ' · ' + btn.getAttribute('data-time');
    });

    /* ---------- Feedback Modal ---------- */
    const feedbackModal = document.getElementById('feedbackModal');
    feedbackModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        const slotId = btn.getAttribute('data-slot-id');
        const userType = btn.getAttribute('data-user-type');
        const data = feedbackData[slotId] || {};

        document.getElementById('feedback-teacher-name').textContent = data.teacher_name || '';

        const container = document.getElementById('feedback-content');

        if (userType === 'student') {
            container.innerHTML = renderReadOnly('Feedback for You', data.student_feedback);
        } else if (userType === 'parent') {
            container.innerHTML = renderReadOnly('Feedback for You', data.parent_feedback);
        } else if (userType === 'teacher') {
            container.innerHTML = renderTeacherForms(data);
        } else if (userType === 'admin') {
            container.innerHTML =
                renderReadOnly('Parent Feedback', data.parent_feedback) +
                renderReadOnly('Student Feedback', data.student_feedback);
        } else {
            container.innerHTML = '<p class="text-secondary">Unable to determine feedback view.</p>';
        }
    });
});
</script>
</body>
</html>