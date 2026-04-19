<?php
session_start();
require_once "../model/user.php";
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php");
    exit();
}

$session_email = trim($_SESSION['email_address']);
$userResults = getUserByEmail($session_email);
if (empty($userResults)) {
    die("User not found.");
}
$user = $userResults[0];
$userType = strtolower(trim($user->user_type ?? ''));

// Only students should be here
if ($userType !== 'student') {
    header("Location: myprofile.php");
    exit();
}

// Check if they actually still need a parent
$stmt = $pdo->prepare("
    SELECT s.student_type, sp.parent_email_address 
    FROM students s 
    LEFT JOIN student_parent sp 
        ON LOWER(TRIM(s.email_address)) = LOWER(TRIM(sp.student_email_address))
    WHERE LOWER(TRIM(s.email_address)) = LOWER(TRIM(?))
    LIMIT 1
");
$stmt->execute([$session_email]);
$studentInfo = $stmt->fetch(PDO::FETCH_OBJ);

$studentType = strtolower(trim($studentInfo->student_type ?? ''));
if ($studentType === 'colleges, sixth form' || !empty($studentInfo->parent_email_address)) {
    header("Location: myprofile.php");
    exit();
}

$error = '';
$active_tab = $_POST['action'] ?? 'link_existing';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'link_existing') {
        $parent_email = trim($_POST['parent_email'] ?? '');

        if (empty($parent_email)) {
            $error = "Please enter a parent email address.";
        } elseif (!filter_var($parent_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (strcasecmp($session_email, $parent_email) === 0) {
            $error = "You cannot link to yourself.";
        } else {
            $parent_user = getUserByEmail($parent_email);
            $is_parent = false;
            if (!empty($parent_user)) {
                $chk = $pdo->prepare("SELECT COUNT(*) FROM parents WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))");
                $chk->execute([$parent_email]);
                $is_parent = (int)$chk->fetchColumn() > 0;
            }

            if (empty($parent_user) || !$is_parent) {
                $error = "No parent account found with that email address.";
            } else {
                $dup = $pdo->prepare("
                    SELECT COUNT(*) FROM student_parent 
                    WHERE LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))
                      AND LOWER(TRIM(parent_email_address)) = LOWER(TRIM(?))
                ");
                $dup->execute([$session_email, $parent_email]);
                if ($dup->fetchColumn() > 0) {
                    $error = "You are already linked to this parent.";
                } else {
                    linkStudentParent($session_email, $parent_email);
                    header("Location: myprofile.php?parent_linked=1");
                    exit();
                }
            }
        }

    } elseif ($action === 'create_parent') {
        $p_first   = trim($_POST['first_name'] ?? '');
        $p_last    = trim($_POST['last_name'] ?? '');
        $p_email   = trim($_POST['email_address'] ?? '');
        $p_pass    = $_POST['password'] ?? '';
        $p_contact = trim($_POST['contact_number'] ?? '');
        $p_type    = $_POST['parent_type'] ?? '';
        $p_gender  = trim($_POST['parent_gender'] ?? '');
        $p_eth     = trim($_POST['parent_ethnicity'] ?? '');
        $p_sec_q   = $_POST['security_question'] ?? 'pet';
        $p_sec_a   = trim($_POST['security_answer'] ?? '');

        $errors = [];
        if ($p_first === '' || $p_last === '' || $p_email === '' || $p_pass === '' || $p_sec_a === '') {
            $errors[] = "All required fields must be filled.";
        }
        if (!empty($p_email) && !filter_var($p_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address.";
        }
        if (strcasecmp($session_email, $p_email) === 0) {
            $errors[] = "Student and parent must have different email addresses.";
        }
        if (empty($p_type)) {
            $errors[] = "Please select a relationship.";
        }

        if (!empty($errors)) {
            $error = implode(' ', $errors);
        } else {
            if (!empty(getUserByEmail($p_email))) {
                $error = "That email is already registered.";
            } else {
                try {
                    $pdo->beginTransaction();

                    $ok = addUser(
                        $p_first, $p_last, $p_contact,
                        $p_email, 'Parent', $p_pass,
                        $p_sec_q, $p_sec_a,
                        null, $p_gender, $p_eth
                    );

                    if ($ok) {
                        addParent($p_email, $p_type);
                        linkStudentParent($session_email, $p_email);
                        $pdo->commit();
                        header("Location: myprofile.php?parent_linked=1");
                        exit();
                    } else {
                        $pdo->rollBack();
                        $error = "Failed to create parent account.";
                    }
                } catch (Exception $e) {
                    if ($pdo->inTransaction()) $pdo->rollBack();
                    $error = "Error: " . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Parent/Guardian - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .link-card { border: none; border-radius: 1rem; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand navbar-light bg-white border-bottom">
    <div class="container-fluid px-4">
        <a href="teacherlist.php" class="navbar-brand fw-bold">Tutoring For All</a>
        <div class="navbar-nav ms-auto">
            <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-1"></i>Sign Out</a>
        </div>
    </div>
</nav>

<main class="flex-grow-1 d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                <div class="card link-card p-4 p-md-5">
                    <div class="card-body">
                        <h1 class="h3 mb-2 fw-normal">Parent/Guardian Required</h1>
                        <p class="text-secondary mb-4">
                            Because you are under 18, a parent or guardian must be linked to your account before you can continue.
                        </p>

                        <?php if ($error): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <ul class="nav nav-pills mb-4" id="parentTab" role="tablist">
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link w-100 <?= $active_tab === 'link_existing' ? 'active' : '' ?>" id="link-tab" data-bs-toggle="pill" data-bs-target="#link-existing" type="button" role="tab">
                                    <i class="bi bi-link-45deg me-1"></i>Link Existing
                                </button>
                            </li>
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link w-100 <?= $active_tab === 'create_parent' ? 'active' : '' ?>" id="create-tab" data-bs-toggle="pill" data-bs-target="#create-parent" type="button" role="tab">
                                    <i class="bi bi-person-plus me-1"></i>Create New
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="parentTabContent">
                            <!-- LINK EXISTING -->
                            <div class="tab-pane fade <?= $active_tab === 'link_existing' ? 'show active' : '' ?>" id="link-existing" role="tabpanel">
                                <form method="POST" action="linkparent.php">
                                    <input type="hidden" name="action" value="link_existing">
                                    <div class="mb-3">
                                        <label for="parent_email" class="form-label fw-semibold">Parent/Guardian Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-lg py-3" id="parent_email" name="parent_email"
                                            value="<?= htmlspecialchars($_POST['parent_email'] ?? '') ?>"
                                            placeholder="parent@example.com" required>
                                        <div class="form-text">Your parent/guardian must already have an account with us.</div>
                                    </div>
                                    <button type="submit" class="btn btn-dark btn-lg w-100 py-3">Link Parent Account</button>
                                </form>
                            </div>

                            <!-- CREATE NEW -->
                            <div class="tab-pane fade <?= $active_tab === 'create_parent' ? 'show active' : '' ?>" id="create-parent" role="tabpanel">
                                <form method="POST" action="linkparent.php">
                                    <input type="hidden" name="action" value="create_parent">

                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg py-3" name="first_name" required
                                                value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg py-3" name="last_name" required
                                                value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Relationship <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-lg py-3" name="parent_type" required>
                                            <option value="">Select relationship...</option>
                                            <option value="Father" <?= (($_POST['parent_type'] ?? '') === 'Father') ? 'selected' : '' ?>>Father</option>
                                            <option value="Mother" <?= (($_POST['parent_type'] ?? '') === 'Mother') ? 'selected' : '' ?>>Mother</option>
                                            <option value="Guardian" <?= (($_POST['parent_type'] ?? '') === 'Guardian') ? 'selected' : '' ?>>Guardian</option>
                                            <option value="Other" <?= (($_POST['parent_type'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-lg py-3" name="email_address" required
                                            value="<?= htmlspecialchars($_POST['email_address'] ?? '') ?>" placeholder="parent@example.com">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control form-control-lg py-3" name="password" required placeholder="Choose a password">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Contact Number</label>
                                        <input type="text" class="form-control form-control-lg py-3" name="contact_number"
                                            value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>" placeholder="+44 7700 000000">
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold">Gender</label>
                                            <select class="form-select form-select-lg py-3" name="parent_gender">
                                                <option value="">Select...</option>
                                                <option value="Male" <?= (($_POST['parent_gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                                <option value="Female" <?= (($_POST['parent_gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                                                <option value="Other" <?= (($_POST['parent_gender'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                                <option value="Prefer not to say" <?= (($_POST['parent_gender'] ?? '') === 'Prefer not to say') ? 'selected' : '' ?>>Prefer not to say</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold">Ethnicity</label>
                                            <input type="text" class="form-control form-control-lg py-3" name="parent_ethnicity"
                                                value="<?= htmlspecialchars($_POST['parent_ethnicity'] ?? '') ?>" placeholder="e.g., White British">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Security Question</label>
                                        <select class="form-select form-select-lg py-3" name="security_question">
                                            <option value="pet">What is the name of your first pet?</option>
                                            <option value="city">What city were you born in?</option>
                                            <option value="school">What was the name of your first school?</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Security Answer <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg py-3" name="security_answer" required placeholder="Your answer"
                                            value="<?= htmlspecialchars($_POST['security_answer'] ?? '') ?>">
                                    </div>

                                    <button type="submit" class="btn btn-dark btn-lg w-100 py-3">Create & Link Parent</button>
                                </form>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="logout.php" class="text-secondary text-decoration-none small">Sign out and use a different account</a>
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