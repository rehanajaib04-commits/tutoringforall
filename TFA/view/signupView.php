<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Tutoring For All</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/signupView.css">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Primary Navigation (consistent with other pages) -->
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

<!-- Main Content -->
<main class="flex-grow-1 d-flex align-items-center py-4 py-md-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-7 col-xl-6">
                <div class="card border rounded-4 p-4 p-md-5">
                    <div class="card-body p-0">

                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger py-3 mb-4" role="alert">
                                <?= htmlspecialchars($error_message) ?>
                            </div>
                        <?php endif; ?>

                        <?php $current = $step ?? 1; ?>

                        <?php if ($current == 1): ?>
                        <!-- STEP 1 — Student Details + Security -->
                            <h1 class="card-title h2 mb-1 fw-normal">Student Details</h1>
                            <p class="text-secondary mb-4">Tell us about the student being tutored.</p>

                            <form method="POST" action="signup.php">
                                <input type="hidden" name="step" value="1">

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label for="student_first_name" class="form-label fw-semibold">First Name</label>
                                        <input type="text" class="form-control form-control-lg py-3"
                                            id="student_first_name" name="student_first_name"
                                            value="<?= htmlspecialchars($_POST['student_first_name'] ?? '') ?>"
                                            placeholder="Type first name" required autofocus>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="student_last_name" class="form-label fw-semibold">Last Name</label>
                                        <input type="text" class="form-control form-control-lg py-3"
                                            id="student_last_name" name="student_last_name"
                                            value="<?= htmlspecialchars($_POST['student_last_name'] ?? '') ?>"
                                            placeholder="Type last name" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="student_contact_number" class="form-label fw-semibold">Contact Number</label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="student_contact_number" name="student_contact_number"
                                        value="<?= htmlspecialchars($_POST['student_contact_number'] ?? '') ?>"
                                        placeholder="+44 7700 000000">
                                </div>

                                <div class="mb-3">
                                    <label for="student_email_address" class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control form-control-lg py-3"
                                        id="student_email_address" name="student_email_address"
                                        value="<?= htmlspecialchars($_POST['student_email_address'] ?? '') ?>"
                                        placeholder="student@example.com" required>
                                </div>

                                <div class="section-divider"></div>
                                <p class="section-label">Security</p>

                                <div class="mb-3">
                                    <label for="student_password" class="form-label fw-semibold">Password</label>
                                    <input type="password" class="form-control form-control-lg py-3"
                                        id="student_password" name="student_password"
                                        placeholder="Choose a password" required>
                                </div>

                                <div class="mb-3">
                                    <label for="student_security_question" class="form-label fw-semibold">Security Question</label>
                                    <select class="form-select form-select-lg py-3"
                                        id="student_security_question" name="student_security_question">
                                        <option value="pet">What is the name of your first pet?</option>
                                        <option value="city">What city were you born in?</option>
                                        <option value="school">What was the name of your first school?</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="student_security_answer" class="form-label fw-semibold">Security Answer</label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="student_security_answer" name="student_security_answer"
                                        value="<?= htmlspecialchars($_POST['student_security_answer'] ?? '') ?>"
                                        placeholder="Your answer" required>
                                </div>

                                <button type="submit" class="btn btn-dark btn-lg w-100 py-3">Continue</button>
                            </form>

                        <?php elseif ($current == 2): ?>
                        <!-- STEP 2 — Account Holder Details + Security -->
                            <h1 class="card-title h2 mb-1 fw-normal">Account Holder</h1>
                            <p class="text-secondary mb-4">Details for the parent or guardian managing this account.</p>

                            <form method="POST" action="signup.php">
                                <input type="hidden" name="step" value="2">

                                <!-- Carry all student data forward -->
                                <input type="hidden" name="student_first_name"        value="<?= htmlspecialchars($student_data['student_first_name'] ?? '') ?>">
                                <input type="hidden" name="student_last_name"         value="<?= htmlspecialchars($student_data['student_last_name'] ?? '') ?>">
                                <input type="hidden" name="student_contact_number"    value="<?= htmlspecialchars($student_data['student_contact_number'] ?? '') ?>">
                                <input type="hidden" name="student_email_address"     value="<?= htmlspecialchars($student_data['student_email_address'] ?? '') ?>">
                                <input type="hidden" name="student_password"          value="<?= htmlspecialchars($student_data['student_password'] ?? '') ?>">
                                <input type="hidden" name="student_security_question" value="<?= htmlspecialchars($student_data['student_security_question'] ?? '') ?>">
                                <input type="hidden" name="student_security_answer"   value="<?= htmlspecialchars($student_data['student_security_answer'] ?? '') ?>">

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label for="first_name" class="form-label fw-semibold">First Name</label>
                                        <input type="text" class="form-control form-control-lg py-3"
                                            id="first_name" name="first_name"
                                            value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                                            placeholder="Type first name" required autofocus>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="last_name" class="form-label fw-semibold">Last Name</label>
                                        <input type="text" class="form-control form-control-lg py-3"
                                            id="last_name" name="last_name"
                                            value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
                                            placeholder="Type last name" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="contact_number" class="form-label fw-semibold">Contact Number</label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="contact_number" name="contact_number"
                                        value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>"
                                        placeholder="+44 7700 000000">
                                </div>

                                <div class="mb-3">
                                    <label for="email_address" class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control form-control-lg py-3"
                                        id="email_address" name="email_address"
                                        value="<?= htmlspecialchars($_POST['email_address'] ?? '') ?>"
                                        placeholder="you@example.com" required>
                                </div>

                                <div class="section-divider"></div>
                                <p class="section-label">Security</p>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <input type="password" class="form-control form-control-lg py-3"
                                        id="password" name="password"
                                        placeholder="Choose a password" required>
                                </div>

                                <div class="mb-3">
                                    <label for="security_question" class="form-label fw-semibold">Security Question</label>
                                    <select class="form-select form-select-lg py-3"
                                        id="security_question" name="security_question">
                                        <option value="pet">What is the name of your first pet?</option>
                                        <option value="city">What city were you born in?</option>
                                        <option value="school">What was the name of your first school?</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="security_answer" class="form-label fw-semibold">Security Answer</label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="security_answer" name="security_answer"
                                        value="<?= htmlspecialchars($_POST['security_answer'] ?? '') ?>"
                                        placeholder="Your answer" required>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="signup.php" class="btn btn-outline-secondary btn-lg py-3 px-4">Back</a>
                                    <button type="submit" class="btn btn-dark btn-lg flex-grow-1 py-3">Create Account</button>
                                </div>
                            </form>

                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <span class="text-secondary">Already have an account?</span>
                            <a href="sign_in.php" class="fw-bold text-dark text-decoration-none"> Sign in</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>