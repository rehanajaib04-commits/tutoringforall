<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')) ?> - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/teacherProfileView.css">
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
                    <a class="nav-link" href="myprofile.php">
                        <i class="bi bi-person me-1"></i>My Profile
                    </a>
                </li>
                <?php if (isset($_SESSION['email_address'])): ?>
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

<div class="container py-5 profile-container">
    <a href="teacherlist.php" class="btn btn-outline-dark btn-sm btn-back">
        <i class="bi bi-arrow-left me-2"></i>Back to Teachers
    </a>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="profile-pic-container">
                <span class="profile-pic-placeholder"><i class="bi bi-person"></i></span>
            </div>
        </div>

        <div class="col-md-8">
            <h1 class="teacher-name"><?= htmlspecialchars(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')) ?></h1>

            <div class="info-section">
                <span class="section-label">Teaches</span>
                <p class="mb-0 fs-5"><?= htmlspecialchars($teacher->teacher_type ?? 'General Subjects') ?></p>
            </div>

            <div class="info-section">
                <span class="section-label">Hourly Rate</span>
                <p class="rate-display mb-0">
                    <?php if ($hourly_rate): ?>
                        £<?= number_format($hourly_rate, 2) ?> <span>/ hour</span>
                    <?php else: ?>
                        <span style="font-size: 1rem; color: var(--secondary-color);">Rate not set</span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="info-section">
                <a href="availability.php?teacher_email=<?= urlencode($teacher->email_address) ?>" class="action-link">
                    <span>See Availability</span>
                    <span class="arrow"><i class="bi bi-arrow-right"></i></span>
                </a>
            </div>

            <div class="info-section">
                <span class="section-label">Contact Details</span>
                <div class="contact-details">
                    <?php if (!empty($teacher->contact_number)): ?>
                        <p><i class="bi bi-telephone me-2"></i><?= htmlspecialchars($teacher->contact_number) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($teacher->email_address)): ?>
                        <p><i class="bi bi-envelope me-2"></i><?= htmlspecialchars($teacher->email_address) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>