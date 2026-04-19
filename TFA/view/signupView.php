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

                        <?php $current = $step ?? '1'; ?>

                        <?php if ($current == '1'): ?>
                        <!-- STEP 1 — Student Details -->
                            <h1 class="card-title h2 mb-1 fw-normal">Student Details</h1>
                            <p class="text-secondary mb-4">Tell us about the student being tutored.</p>

                            <form method="POST" action="signup.php">
                                <input type="hidden" name="step" value="1">

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label for="student_first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg py-3"
                                            id="student_first_name" name="student_first_name"
                                            value="<?= htmlspecialchars($_POST['student_first_name'] ?? '') ?>"
                                            placeholder="Type first name" required autofocus>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="student_last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg py-3"
                                            id="student_last_name" name="student_last_name"
                                            value="<?= htmlspecialchars($_POST['student_last_name'] ?? '') ?>"
                                            placeholder="Type last name" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="student_type" class="form-label fw-semibold">Student Type <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg py-3" id="student_type" name="student_type" required>
                                        <option value="">Select education level...</option>
                                        <option value="Preschool" <?= (($_POST['student_type'] ?? '') === 'Preschool') ? 'selected' : '' ?>>Preschool</option>
                                        <option value="Primary School" <?= (($_POST['student_type'] ?? '') === 'Primary School') ? 'selected' : '' ?>>Primary School</option>
                                        <option value="Secondary School" <?= (($_POST['student_type'] ?? '') === 'Secondary School') ? 'selected' : '' ?>>Secondary School</option>
                                        <option value="Colleges, Sixth Form" <?= (($_POST['student_type'] ?? '') === 'Colleges, Sixth Form') ? 'selected' : '' ?>>Colleges, Sixth Form</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="student_contact_number" class="form-label fw-semibold">Contact Number</label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="student_contact_number" name="student_contact_number"
                                        value="<?= htmlspecialchars($_POST['student_contact_number'] ?? '') ?>"
                                        placeholder="+44 7700 000000">
                                </div>

                                <div class="mb-3">
                                    <label for="student_email_address" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-lg py-3"
                                        id="student_email_address" name="student_email_address"
                                        value="<?= htmlspecialchars($_POST['student_email_address'] ?? '') ?>"
                                        placeholder="student@example.com" required>
                                </div>

                                <div class="mb-3">
                                    <label for="student_date_of_birth" class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control form-control-lg py-3"
                                        id="student_date_of_birth" name="student_date_of_birth"
                                        value="<?= htmlspecialchars($_POST['student_date_of_birth'] ?? '') ?>"
                                        required>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label for="student_gender" class="form-label fw-semibold">Gender</label>
                                        <select class="form-select form-select-lg py-3" id="student_gender" name="student_gender">
                                            <option value="">Select...</option>
                                            <option value="Male" <?= (($_POST['student_gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= (($_POST['student_gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                                            <option value="Other" <?= (($_POST['student_gender'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                            <option value="Prefer not to say" <?= (($_POST['student_gender'] ?? '') === 'Prefer not to say') ? 'selected' : '' ?>>Prefer not to say</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="student_ethnicity" class="form-label fw-semibold">Ethnicity</label>
                                        <input type="text" class="form-control form-control-lg py-3"
                                            id="student_ethnicity" name="student_ethnicity"
                                            value="<?= htmlspecialchars($_POST['student_ethnicity'] ?? '') ?>"
                                            placeholder="e.g., White British, Indian">
                                    </div>
                                </div>

                                <div class="section-divider"></div>
                                <p class="section-label">Address <span class="text-secondary fw-normal">(Optional)</span></p>

                                <div class="mb-3">
                                    <label for="post_code" class="form-label fw-semibold">Postcode</label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="post_code" name="post_code"
                                        value="<?= htmlspecialchars($_POST['post_code'] ?? '') ?>"
                                        placeholder="e.g., SW1A 1AA">
                                </div>

                                <div class="mb-3">
                                    <label for="address_line1" class="form-label fw-semibold">Address Line 1</label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="address_line1" name="address_line1"
                                        value="<?= htmlspecialchars($_POST['address_line1'] ?? '') ?>"
                                        placeholder="House number and street">
                                </div>

                                <div class="mb-3">
                                    <label for="addressline2" class="form-label fw-semibold">Address Line 2 <span class="text-secondary fw-normal">(Optional)</span></label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="addressline2" name="addressline2"
                                        value="<?= htmlspecialchars($_POST['addressline2'] ?? '') ?>"
                                        placeholder="Apartment, suite, unit, etc.">
                                </div>

                                <div class="mb-3">
                                    <label for="town" class="form-label fw-semibold">Town / City <span class="text-secondary fw-normal">(Optional)</span></label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="town" name="town"
                                        value="<?= htmlspecialchars($_POST['town'] ?? '') ?>"
                                        placeholder="e.g., London">
                                </div>

                                <div class="mb-3">
                                    <label for="county" class="form-label fw-semibold">County <span class="text-secondary fw-normal">(Optional)</span></label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="county" name="county"
                                        value="<?= htmlspecialchars($_POST['county'] ?? '') ?>"
                                        placeholder="e.g., Greater London">
                                </div>

                                <div class="section-divider"></div>
                                <p class="section-label">Security</p>

                                <div class="mb-3">
                                    <label for="student_password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control form-control-lg py-3"
                                        id="student_password" name="student_password"
                                        placeholder="Choose a password" required>
                                </div>

                                <div class="mb-3">
                                    <label for="student_security_question" class="form-label fw-semibold">Security Question</label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="student_security_question" name="student_security_question"
                                        value="<?= htmlspecialchars($_POST['student_security_question'] ?? '') ?>"
                                        placeholder="e.g. What is the name of your first pet?">
                                </div>

                                <div class="mb-4">
                                    <label for="student_security_answer" class="form-label fw-semibold">Security Answer <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="student_security_answer" name="student_security_answer"
                                        value="<?= htmlspecialchars($_POST['student_security_answer'] ?? '') ?>"
                                        placeholder="Your answer" required>
                                </div>

                                <button type="submit" class="btn btn-dark btn-lg w-100 py-3">Continue</button>
                            </form>

                        <?php elseif ($current == '1.5'): ?>
                        <!-- STEP 1.5 — Parent/Guardian Choice -->
                            <?php
                                $step15_type = $student_data['student_type'] ?? '';
                                $is_college = ($step15_type === 'Colleges, Sixth Form');
                            ?>
                            <h1 class="card-title h2 mb-1 fw-normal">Guardian Details</h1>
                            <p class="text-secondary mb-4">
                                <?= $is_college
                                    ? 'Would you like to add a parent or guardian to your account?'
                                    : 'A parent or guardian is required for your account.' ?>
                            </p>

                            <form method="POST" action="signup.php">
                                <input type="hidden" name="step" value="1.5">

                                <!-- Carry all student data forward -->
                                <input type="hidden" name="student_first_name"        value="<?= htmlspecialchars($student_data['student_first_name'] ?? '') ?>">
                                <input type="hidden" name="student_last_name"         value="<?= htmlspecialchars($student_data['student_last_name'] ?? '') ?>">
                                <input type="hidden" name="student_contact_number"    value="<?= htmlspecialchars($student_data['student_contact_number'] ?? '') ?>">
                                <input type="hidden" name="student_email_address"     value="<?= htmlspecialchars($student_data['student_email_address'] ?? '') ?>">
                                <input type="hidden" name="student_password"          value="<?= htmlspecialchars($student_data['student_password'] ?? '') ?>">
                                <input type="hidden" name="student_security_question" value="<?= htmlspecialchars($student_data['student_security_question'] ?? '') ?>">
                                <input type="hidden" name="student_security_answer"   value="<?= htmlspecialchars($student_data['student_security_answer'] ?? '') ?>">
                                <input type="hidden" name="student_type"              value="<?= htmlspecialchars($student_data['student_type'] ?? '') ?>">
                                <input type="hidden" name="student_date_of_birth"     value="<?= htmlspecialchars($student_data['student_date_of_birth'] ?? '') ?>">
                                <input type="hidden" name="student_gender"            value="<?= htmlspecialchars($student_data['student_gender'] ?? '') ?>">
                                <input type="hidden" name="student_ethnicity"         value="<?= htmlspecialchars($student_data['student_ethnicity'] ?? '') ?>">
                                <input type="hidden" name="post_code"                 value="<?= htmlspecialchars($student_data['post_code'] ?? '') ?>">
                                <input type="hidden" name="address_line1"             value="<?= htmlspecialchars($student_data['address_line1'] ?? '') ?>">
                                <input type="hidden" name="addressline2"              value="<?= htmlspecialchars($student_data['addressline2'] ?? '') ?>">
                                <input type="hidden" name="town"                      value="<?= htmlspecialchars($student_data['town'] ?? '') ?>">
                                <input type="hidden" name="county"                    value="<?= htmlspecialchars($student_data['county'] ?? '') ?>">

                                <div class="mb-3">
                                    <div class="form-check border rounded p-3 mb-3">
                                        <input class="form-check-input" type="radio" name="add_parent" id="add_parent_yes" value="new" required <?= (($_POST['add_parent'] ?? '') === 'new') ? 'checked' : '' ?>>
                                        <label class="form-check-label ms-2" for="add_parent_yes">
                                            <strong>Yes — Create a new parent/guardian account</strong><br>
                                            <small class="text-secondary">Your parent/guardian will create their profile alongside yours.</small>
                                        </label>
                                    </div>

                                    <div class="form-check border rounded p-3 mb-3">
                                        <input class="form-check-input" type="radio" name="add_parent" id="add_parent_existing" value="existing" required <?= (($_POST['add_parent'] ?? '') === 'existing') ? 'checked' : '' ?>>
                                        <label class="form-check-label ms-2" for="add_parent_existing">
                                            <strong>Yes — Link to an existing parent/guardian</strong><br>
                                            <small class="text-secondary">My parent/guardian already has an account.</small>
                                        </label>
                                    </div>

                                    <div id="existing_parent_email_wrap" class="mb-3 ps-4" style="display: <?= (($_POST['add_parent'] ?? '') === 'existing') ? 'block' : 'none' ?>;">
                                        <label for="existing_parent_email" class="form-label fw-semibold">Parent/Guardian Email</label>
                                        <input type="email" class="form-control form-control-lg py-3" id="existing_parent_email" name="existing_parent_email" value="<?= htmlspecialchars($_POST['existing_parent_email'] ?? '') ?>" placeholder="parent@example.com">
                                    </div>

                                    <?php if ($is_college): ?>
                                    <div class="form-check border rounded p-3">
                                        <input class="form-check-input" type="radio" name="add_parent" id="add_parent_no" value="solo" required <?= (($_POST['add_parent'] ?? '') === 'solo') ? 'checked' : '' ?>>
                                        <label class="form-check-label ms-2" for="add_parent_no">
                                            <strong>No — I'll manage my own account</strong><br>
                                            <small class="text-secondary">You'll be responsible for your own bookings and profile.</small>
                                        </label>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="signup.php" class="btn btn-outline-secondary btn-lg py-3 px-4">Back</a>
                                    <button type="submit" class="btn btn-dark btn-lg flex-grow-1 py-3">Continue</button>
                                </div>
                            </form>

                            <script>
                            (function() {
                                const radios = document.querySelectorAll('input[name="add_parent"]');
                                const emailWrap = document.getElementById('existing_parent_email_wrap');
                                radios.forEach(radio => {
                                    radio.addEventListener('change', function() {
                                        emailWrap.style.display = this.value === 'existing' ? 'block' : 'none';
                                        if (this.value !== 'existing') {
                                            document.getElementById('existing_parent_email').value = '';
                                        }
                                    });
                                });
                            })();
                            </script>

                        <?php elseif ($current == '2'): ?>
                        <!-- STEP 2 — Account Holder Details -->
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
                                <input type="hidden" name="student_type"              value="<?= htmlspecialchars($student_data['student_type'] ?? '') ?>">
                                <input type="hidden" name="student_date_of_birth"     value="<?= htmlspecialchars($student_data['student_date_of_birth'] ?? '') ?>">
                                <input type="hidden" name="student_gender"            value="<?= htmlspecialchars($student_data['student_gender'] ?? '') ?>">
                                <input type="hidden" name="student_ethnicity"         value="<?= htmlspecialchars($student_data['student_ethnicity'] ?? '') ?>">
                                <input type="hidden" name="post_code"                 value="<?= htmlspecialchars($student_data['post_code'] ?? '') ?>">
                                <input type="hidden" name="address_line1"             value="<?= htmlspecialchars($student_data['address_line1'] ?? '') ?>">
                                <input type="hidden" name="addressline2"              value="<?= htmlspecialchars($student_data['addressline2'] ?? '') ?>">
                                <input type="hidden" name="town"                      value="<?= htmlspecialchars($student_data['town'] ?? '') ?>">
                                <input type="hidden" name="county"                    value="<?= htmlspecialchars($student_data['county'] ?? '') ?>">

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg py-3"
                                            id="first_name" name="first_name"
                                            value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                                            placeholder="Type first name" required autofocus>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg py-3"
                                            id="last_name" name="last_name"
                                            value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
                                            placeholder="Type last name" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="parent_type" class="form-label fw-semibold">Relationship to Student <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg py-3" id="parent_type" name="parent_type" required>
                                        <option value="">Select relationship...</option>
                                        <option value="Father" <?= (($_POST['parent_type'] ?? '') === 'Father') ? 'selected' : '' ?>>Father</option>
                                        <option value="Mother" <?= (($_POST['parent_type'] ?? '') === 'Mother') ? 'selected' : '' ?>>Mother</option>
                                        <option value="Guardian" <?= (($_POST['parent_type'] ?? '') === 'Guardian') ? 'selected' : '' ?>>Guardian</option>
                                        <option value="Other" <?= (($_POST['parent_type'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="contact_number" class="form-label fw-semibold">Contact Number</label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="contact_number" name="contact_number"
                                        value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>"
                                        placeholder="+44 7700 000000">
                                </div>

                                <div class="mb-3">
                                    <label for="email_address" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-lg py-3"
                                        id="email_address" name="email_address"
                                        value="<?= htmlspecialchars($_POST['email_address'] ?? '') ?>"
                                        placeholder="you@example.com" required>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label for="parent_gender" class="form-label fw-semibold">Gender</label>
                                        <select class="form-select form-select-lg py-3" id="parent_gender" name="parent_gender">
                                            <option value="">Select...</option>
                                            <option value="Male" <?= (($_POST['parent_gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= (($_POST['parent_gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                                            <option value="Other" <?= (($_POST['parent_gender'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                            <option value="Prefer not to say" <?= (($_POST['parent_gender'] ?? '') === 'Prefer not to say') ? 'selected' : '' ?>>Prefer not to say</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="parent_ethnicity" class="form-label fw-semibold">Ethnicity</label>
                                        <input type="text" class="form-control form-control-lg py-3"
                                            id="parent_ethnicity" name="parent_ethnicity"
                                            value="<?= htmlspecialchars($_POST['parent_ethnicity'] ?? '') ?>"
                                            placeholder="e.g., White British, Indian">
                                    </div>
                                </div>

                                <div class="section-divider"></div>
                                <p class="section-label">Security</p>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control form-control-lg py-3"
                                        id="password" name="password"
                                        placeholder="Choose a password" required>
                                </div>

                                <div class="mb-3">
                                    <label for="security_question" class="form-label fw-semibold">Security Question</label>
                                    <input type="text" class="form-control form-control-lg py-3"
                                        id="security_question" name="security_question"
                                        value="<?= htmlspecialchars($_POST['security_question'] ?? '') ?>"
                                        placeholder="e.g. What is the name of your first pet?">
                                </div>

                                <div class="mb-4">
                                    <label for="security_answer" class="form-label fw-semibold">Security Answer <span class="text-danger">*</span></label>
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

                        <?php else: ?>
                            <div class="alert alert-warning">
                                Unknown step. <a href="signup.php" class="alert-link">Start over</a>.
                            </div>
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