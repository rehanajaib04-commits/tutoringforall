<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEditing ? 'Edit Profile' : 'Teacher Profile' ?> - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/teacherProfileView.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="Tutoring For All" class="navbar-brand">Tutoring For All</a>
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
                <?php if (isset($_SESSION['email_address'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="myprofile.php">
                            <i class="bi bi-person me-1"></i>My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="user-email d-none d-lg-inline"><?= htmlspecialchars($_SESSION['email_address']) ?></span>
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

<div class="container profile-container py-4">
    <a href="teacherlist.php" class="btn btn-outline-dark btn-sm btn-back">
        <i class="bi bi-arrow-left me-2"></i>Back to Teachers
    </a>

    <?php if ($success_message): ?>
        <div class="alert alert-success mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger mb-4" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($isEditing && $isOwnProfile): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-dark mb-2">Edit Mode</span>
                <h1 class="teacher-name mb-1">Edit Your Profile</h1>
                <p class="teacher-meta">Make changes and save when done</p>
            </div>
            <div class="d-flex gap-2">
                <a href="teacherProfile.php?id=<?= urlencode($id) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-2"></i>Cancel
                </a>
                <button type="submit" form="editProfileForm" class="btn btn-dark">
                    <i class="bi bi-check-lg me-2"></i>Save Changes
                </button>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <?php if (!$isEditing): ?>
                <div class="profile-header">
                    <h1 class="teacher-name"><?= htmlspecialchars(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')) ?></h1>
                    <div class="teacher-meta">
                        <span><?= htmlspecialchars($teacher->teacher_type ?? 'Tutor') ?></span>
                        <span class="meta-separator">|</span>
                        <?php if ((int)($teacher->total_reviews ?? 0) > 0): ?>
                            <span class="rating-text">
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                <?= number_format((float)$teacher->rating, 1) ?>/10
                                (<?= (int)$teacher->total_reviews ?> review<?= ((int)$teacher->total_reviews === 1) ? '' : 's' ?>)
                            </span>
                        <?php else: ?>
                            <span class="text-secondary">No reviews yet</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="info-section">
                <div class="section-title">About Me</div>
                <?php if ($isEditing && $isOwnProfile): ?>
                    <form id="editProfileForm" method="POST" action="teacherProfile.php?id=<?= urlencode($id) ?>">
                        <input type="hidden" name="action" value="update_teacher_profile">
                        <textarea name="bio" class="form-control" rows="6" placeholder="Tell students about yourself, your teaching style, and what makes you unique..."><?= htmlspecialchars($teacher->bio ?? '') ?></textarea>
                    </form>
                <?php else: ?>
                    <div class="section-content">
                        <?php if ($teacher->bio): ?>
                            <p><?= nl2br(htmlspecialchars($teacher->bio)) ?></p>
                        <?php else: ?>
                            <p class="text-secondary fst-italic">No bio provided</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="info-section">
                <div class="section-title">Qualifications & Experience</div>
                <?php if ($isEditing && $isOwnProfile): ?>
                    <textarea name="experience" form="editProfileForm" class="form-control" rows="6" placeholder="List your qualifications, certifications, and years of experience..."><?= htmlspecialchars($teacher->experience ?? '') ?></textarea>
                <?php else: ?>
                    <div class="section-content">
                        <?php if ($teacher->experience): ?>
                            <p><?= nl2br(htmlspecialchars($teacher->experience)) ?></p>
                        <?php else: ?>
                            <p class="text-secondary fst-italic">No qualifications listed</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!$isEditing): ?>
                <div class="info-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mb-0">Reviews</div>
                        <span class="text-secondary small">
                            <?= (int)($teacher->total_reviews ?? 0) ?> total
                        </span>
                    </div>

                    <?php if (!$isOwnProfile && isset($_SESSION['email_address']) && in_array($viewerUserType, ['student', 'parent'], true)): ?>
                        <?php if ($canReview): ?>
                            <div class="border rounded-3 p-3 bg-light mb-4">
                                <h3 class="h6 mb-3"><?= $existingReview ? 'Update Your Review' : 'Leave a Review' ?></h3>
                                <form method="POST" action="teacherProfile.php?id=<?= urlencode($id) ?><?= $selectedReviewStudentEmail ? '&review_student=' . urlencode($selectedReviewStudentEmail) : '' ?>">
                                    <input type="hidden" name="action" value="submit_review">

                                    <?php if ($viewerUserType === 'parent'): ?>
                                        <div class="mb-3">
                                            <label class="form-label">Student</label>
                                            <select name="student_email" class="form-select" required onchange="window.location='teacherProfile.php?id=<?= urlencode($id) ?>&review_student=' + encodeURIComponent(this.value);">
                                                <?php foreach ($eligibleReviewStudents as $student): ?>
                                                    <option value="<?= htmlspecialchars($student->email_address) ?>" <?= strtolower($selectedReviewStudentEmail) === strtolower($student->email_address) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php else: ?>
                                        <input type="hidden" name="student_email" value="<?= htmlspecialchars($selectedReviewStudentEmail) ?>">
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <select name="rating" class="form-select" required>
                                            <option value="">Choose a rating...</option>
                                            <?php for ($i = 10; $i >= 1; $i--): ?>
                                                <option value="<?= $i ?>" <?= ((int)($existingReview->rating ?? 0) === $i) ? 'selected' : '' ?>>
                                                    <?= $i ?>/10
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Review</label>
                                        <textarea name="review_text" rows="4" class="form-control" placeholder="Share your experience with this teacher..." required><?= htmlspecialchars($existingReview->review_text ?? '') ?></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-dark">
                                        <i class="bi bi-star me-2"></i><?= $existingReview ? 'Update Review' : 'Post Review' ?>
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light border mb-4">
                                Book this teacher first, then you can leave a review here.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!empty($reviews)): ?>
                        <div class="d-grid gap-3">
                            <?php foreach ($reviews as $review): ?>
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                        <div>
                                            <strong>
                                                <?= htmlspecialchars(trim(($review->reviewer_first_name ?? '') . ' ' . ($review->reviewer_last_name ?? ''))) ?>
                                            </strong>
                                            <div class="text-secondary small">
                                                <?= ucfirst(htmlspecialchars($review->reviewer_type ?? 'student')) ?>
                                                <?php if (($review->reviewer_type ?? '') === 'parent' && strtolower($review->student_email_address ?? '') !== strtolower($review->reviewer_email_address ?? '')): ?>
                                                    for <?= htmlspecialchars(trim(($review->student_first_name ?? '') . ' ' . ($review->student_last_name ?? ''))) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-semibold text-warning">
                                                <i class="bi bi-star-fill me-1"></i><?= (int)$review->rating ?>/10
                                            </div>
                                            <div class="text-secondary small">
                                                <?= date('d M Y', strtotime($review->updated_at ?? $review->created_at)) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div><?= nl2br(htmlspecialchars($review->review_text ?? '')) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-secondary fst-italic mb-0">No reviews have been posted yet.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="info-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-title mb-0">Subjects & Levels</div>
                    <?php if ($isEditing && $isOwnProfile): ?>
                        <span class="text-secondary small">Manage your teaching subjects</span>
                    <?php endif; ?>
                </div>

                <div class="subjects-list">
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $subject): ?>
                            <div class="d-inline-block me-2 mb-2">
                                <span class="subject-tag">
                                    <?= htmlspecialchars($subject->subject_name ?? '') ?>
                                    <small>(<?= htmlspecialchars($subject->key_stage ?? '') ?>)</small>
                                    <?php if ($isEditing && $isOwnProfile): ?>
                                        <form method="POST" action="teacherProfile.php?id=<?= urlencode($id) ?>" class="d-inline" style="margin-left: -8px;">
                                            <input type="hidden" name="action" value="delete_subject">
                                            <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject->subject_id ?? '') ?>">
                                            <button type="submit" class="btn btn-link text-danger p-0" style="font-size: 0.875rem; vertical-align: middle;" onclick="return confirm('Remove <?= htmlspecialchars($subject->subject_name) ?>?')">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-secondary fst-italic">No subjects listed</p>
                    <?php endif; ?>
                </div>

                <?php if ($isEditing && $isOwnProfile): ?>
                    <div class="mt-3 pt-3 border-top">
                        <form method="POST" action="teacherProfile.php?id=<?= urlencode($id) ?>" class="row g-2 align-items-end">
                            <input type="hidden" name="action" value="add_subject">
                            <div class="col-md-8">
                                <label class="form-label small text-secondary">Add Subject</label>
                                <select name="subject_id" class="form-select form-select-sm" required>
                                    <option value="">Select subject...</option>
                                    <?php foreach ($allSubjects as $subj): ?>
                                        <?php
                                        $alreadyAssigned = false;
                                        foreach ($subjects as $s) {
                                            if ($s->subject_id == $subj->subject_id) {
                                                $alreadyAssigned = true;
                                                break;
                                            }
                                        }
                                        if (!$alreadyAssigned):
                                        ?>
                                            <option value="<?= htmlspecialchars($subj->subject_id) ?>">
                                                <?= htmlspecialchars($subj->subject_name) ?>
                                                (<?= htmlspecialchars($subj->key_stage) ?>)
                                                <?php if ($subj->year): ?> - Year <?= htmlspecialchars($subj->year) ?><?php endif; ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-dark btn-sm w-100">
                                    <i class="bi bi-plus-lg me-1"></i>Add
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($isEditing && $isOwnProfile): ?>
                <div class="info-section">
                    <div class="section-title">Specialization</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teacher Type</label>
                            <select name="teacher_type" form="editProfileForm" class="form-select" required>
                                <option value="">Select type...</option>
                                <option value="Early Years Teacher (yrs 0-5)" <?= ($teacher->teacher_type === 'Early Years Teacher (yrs 0-5)') ? 'selected' : '' ?>>Early Years Teacher (yrs 0-5)</option>
                                <option value="Primary Teacher (yrs 4-11)" <?= ($teacher->teacher_type === 'Primary Teacher (yrs 4-11)') ? 'selected' : '' ?>>Primary Teacher (yrs 4-11)</option>
                                <option value="Secondary Teacher (yrs 11-16/18)" <?= ($teacher->teacher_type === 'Secondary Teacher (yrs 11-16/18)') ? 'selected' : '' ?>>Secondary Teacher (yrs 11-16/18)</option>
                                <option value="FE Teacher (16+)" <?= ($teacher->teacher_type === 'FE Teacher (16+)') ? 'selected' : '' ?>>FE Teacher (16+)</option>
                                <option value="HE Lecturer (18+)" <?= ($teacher->teacher_type === 'HE Lecturer (18+)') ? 'selected' : '' ?>>HE Lecturer (18+)</option>
                                <option value="SEND Teacher (All ages)" <?= ($teacher->teacher_type === 'SEND Teacher (All ages)') ? 'selected' : '' ?>>SEND Teacher (All ages)</option>
                                <option value="Teaching Assistant (All ages)" <?= ($teacher->teacher_type === 'Teaching Assistant (All ages)') ? 'selected' : '' ?>>Teaching Assistant (All ages)</option>
                                <option value="Specialist Subject Teacher" <?= ($teacher->teacher_type === 'Specialist Subject Teacher') ? 'selected' : '' ?>>Specialist Subject Teacher</option>
                                <option value="Private Tutor (All ages)" <?= ($teacher->teacher_type === 'Private Tutor (All ages)') ? 'selected' : '' ?>>Private Tutor (All ages)</option>
                                <option value="Vocational Instructor (16+)" <?= ($teacher->teacher_type === 'Vocational Instructor (16+)') ? 'selected' : '' ?>>Vocational Instructor (16+)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hourly Rate (£)</label>
                            <input type="number" name="hourly_rate" form="editProfileForm" class="form-control" step="0.50" min="0" value="<?= htmlspecialchars($hourly_rate ?? '') ?>" placeholder="35.00">
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="section-title">Contact Details</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name</label>
                            <input type="text" name="first_name" form="editProfileForm" class="form-control" value="<?= htmlspecialchars($teacher->first_name ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name</label>
                            <input type="text" name="last_name" form="editProfileForm" class="form-control" value="<?= htmlspecialchars($teacher->last_name ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact_number" form="editProfileForm" class="form-control" value="<?= htmlspecialchars($teacher->contact_number ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($teacher->email_address ?? '') ?>" disabled>
                            <div class="form-text text-secondary">Email cannot be changed</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <?php if (!$isEditing): ?>
                <div class="sidebar-section rate-box">
                    <div class="section-title">Hourly Rate</div>
                    <div class="rate-header">
                        <span class="rate-label">Per hour</span>
                        <?php if ($hourly_rate): ?>
                            <span class="rate-value">£<?= number_format((float)$hourly_rate, 2) ?></span>
                        <?php else: ?>
                            <span class="rate-value">Not set</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="sidebar-section">
                    <div class="section-title">Contact Information</div>
                    <div class="contact-list">
                        <div class="contact-item">
                            <i class="bi bi-envelope"></i>
                            <span><?= htmlspecialchars($teacher->email_address ?? 'Not provided') ?></span>
                        </div>
                        <?php if ($teacher->contact_number): ?>
                            <div class="contact-item">
                                <i class="bi bi-telephone"></i>
                                <span><?= htmlspecialchars($teacher->contact_number) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="contact-item">
                            <i class="bi bi-briefcase"></i>
                            <span><?= htmlspecialchars($teacher->teacher_type ?? 'General Tutor') ?></span>
                        </div>
                    </div>
                </div>

                <div class="sidebar-section action-box d-grid gap-2">
                    <?php if (!$isOwnProfile): ?>
                        <a href="availability.php?teacher_email=<?= urlencode($teacher->email_address) ?>" class="btn btn-dark w-100">
                            <i class="bi bi-calendar-week me-2"></i>See Availability
                        </a>
                    <?php else: ?>
                        <a href="availability.php?teacher_email=<?= urlencode($id) ?>" class="btn btn-outline-dark w-100">
                            <i class="bi bi-calendar-week me-2"></i>My Availability
                        </a>
                        <a href="teacherProfile.php?id=<?= urlencode($id) ?>&edit=1" class="btn btn-dark w-100">
                            <i class="bi bi-pencil me-2"></i>Edit Profile
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
