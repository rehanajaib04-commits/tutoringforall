<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutoring For All</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/homeView.css">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Primary Navigation -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="homepage.php" class="navbar-brand">Tutoring For All</a>
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
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="userlist.php">
                                <i class="bi bi-people me-1"></i>User List
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="myprofile.php">
                                <i class="bi bi-person me-1"></i>My Profile
                            </a>
                        </li>
                    <?php endif; ?>
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
                    <li class="nav-item">
                        <a class="nav-link" href="signup.php">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Secondary Navigation -->
<nav class="navbar navbar-expand bg-light border-bottom py-2">
    <div class="container-fluid">
        <div class="navbar-nav mx-auto">
            <a class="nav-link" href="#featured-tutor">Featured Tutor</a>
            <a class="nav-link" href="teacherlist.php">Search Teacher</a>
            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                <a class="nav-link" href="userlist.php">User List</a>
            <?php elseif (isset($_SESSION['email_address'])): ?>
                <a class="nav-link" href="myprofile.php">My Profile</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<main class="hero-section flex-grow-1 d-flex align-items-center text-center">
    <div class="container">
        <h1 class="hero-title">Tutoring For All</h1>
        <p class="hero-subtitle mx-auto">Connect with qualified tutors who help students reach their full potential.</p>
        <a href="teacherlist.php" class="hero-cta">Browse Tutors</a>

        <div class="hero-divider"></div>

        <div class="features-strip d-flex justify-content-center gap-5 flex-wrap">
            <div class="feature-item">
                <div class="feature-label">Verified Tutors</div>
            </div>
            <div class="feature-item">
                <div class="feature-label">Flexible Scheduling</div>
            </div>
            <div class="feature-item">
                <div class="feature-label">Non‑Profit</div>
            </div>
        </div>
    </div>
</main>

<!-- Featured Tutor Section -->
<section id="featured-tutor" class="py-5 bg-white border-top">
    <div class="container" style="max-width: 800px;">
        <h2 class="h4 fw-normal text-center mb-4">Meet a Featured Tutor</h2>
        <?php if (isset($randomTeacher) && $randomTeacher): ?>
            <div class="card border rounded-4 p-4">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <div class="profile-avatar bg-dark text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            <?= strtoupper(substr($randomTeacher->first_name ?? 'T', 0, 1) . substr($randomTeacher->last_name ?? '', 0, 1)) ?>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h3 class="h5 fw-normal mb-1"><?= htmlspecialchars(trim(($randomTeacher->first_name ?? '') . ' ' . ($randomTeacher->last_name ?? ''))) ?></h3>
                        <p class="text-secondary mb-2"><?= htmlspecialchars($randomTeacher->teacher_type ?? 'General Subjects') ?></p>
                        <p class="small text-secondary mb-3">
                            <i class="bi bi-star-fill text-warning"></i> 4.9 · 120+ lessons taught
                        </p>
                        <a href="teacherProfile.php?id=<?= urlencode($randomTeacher->email_address) ?>" class="btn btn-outline-dark">
                            View Profile <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center text-secondary">No featured tutor available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>