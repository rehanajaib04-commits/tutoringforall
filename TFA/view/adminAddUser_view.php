<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add New User - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <style>
        .section-header { font-weight: 600; margin-top: 2rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--primary-color); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="adminDashboard.php" class="navbar-brand">Tutoring For All Admin</a>
        <div class="navbar-nav ms-auto">
            <a href="logout.php" class="nav-link">Sign Out</a>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width: 700px;">
    <h1 class="h2 fw-normal mb-4">System Administration - Add New User</h1>

    <?php if(!empty($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>
    <?php if(!empty($success_message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if (!isset($_GET['step']) || $_GET['step'] != '2'): ?>
        <div class="card border rounded-4 p-4">
            <h5 class="fw-semibold mb-3">Step 1: Select User Type</h5>
            <form method="get" action="">
                <input type="hidden" name="step" value="2">
                <div class="mb-3">
                    <label class="form-label">User Type</label>
                    <select name="type" class="form-select" required>
                        <option value="">-- Select User Type --</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                        <option value="parent">Parent</option>
                        <option value="admin">System Administrator</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-dark">Next Step</button>
            </form>
        </div>
    <?php else: ?>
        <?php $user_type = $_GET['type']; ?>
        <?php if (!in_array($user_type, ['teacher', 'student', 'parent', 'admin'])): ?>
            <div class="alert alert-danger">Invalid user type selected.</div>
            <a href="adminAddUser.php" class="btn btn-outline-secondary">← Back to Selection</a>
        <?php else: ?>
            <div class="card border rounded-4 p-4">
                <h5 class="fw-semibold mb-3">Step 2: Enter <?= ucfirst($user_type) ?> Details</h5>
                <form method="POST" action="?step=2&type=<?= htmlspecialchars($user_type) ?>">
                    <input type="hidden" name="user_type" value="<?= htmlspecialchars($user_type) ?>">

                    <!-- Common Fields -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name *</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name *</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email_address" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Security Question *</label>
                            <select name="security_question" class="form-select" required>
                                <option value="pet">What is the name of your first pet?</option>
                                <option value="city">What city were you born in?</option>
                                <option value="school">What was the name of your first school?</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Security Answer *</label>
                            <input type="text" name="security_answer" class="form-control" required>
                        </div>
                    </div>

                    <?php if ($user_type == 'teacher'): ?>
                        <div class="section-header">Professional Details</div>
                        <div class="mb-3">
                            <label class="form-label">Teacher Type</label>
                            <input type="text" name="teacher_type" class="form-control" placeholder="e.g., Full-time, Part-time">
                        </div>
                    <?php elseif ($user_type == 'student'): ?>
                        <div class="section-header">Academic Details</div>
                        <div class="mb-3">
                            <label class="form-label">Student Type</label>
                            <input type="text" name="student_type" class="form-control" placeholder="e.g., Undergraduate">
                        </div>
                        <div class="section-header">Parent/Guardian Link</div>
                        <div class="mb-3">
                            <label class="form-label">Link to Parent</label>
                            <select name="parent_email" class="form-select">
                                <option value="">-- Select Existing Parent (Optional) --</option>
                                <?php foreach ($parents_list as $parent): ?>
                                    <option value="<?= htmlspecialchars($parent['email_address']) ?>">
                                        <?= htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name'] . ' - ' . $parent['email_address']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($parents_list)): ?>
                                <div class="form-text text-warning">No parents found. You can link later.</div>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($user_type == 'parent'): ?>
                        <div class="section-header">Parent Details</div>
                        <div class="mb-3">
                            <label class="form-label">Parent Type</label>
                            <input type="text" name="parent_type" class="form-control" placeholder="e.g., Father, Mother">
                        </div>
                        <div class="section-header">Address Information</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Address Line 1 *</label>
                                <input type="text" name="address_line1" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address Line 2</label>
                                <input type="text" name="address_line2" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Town *</label>
                                <input type="text" name="town" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">County</label>
                                <input type="text" name="county" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Post Code *</label>
                                <input type="text" name="post_code" class="form-control" required>
                            </div>
                        </div>
                    <?php elseif ($user_type == 'admin'): ?>
                        <div class="section-header">Administrator Account</div>
                        <p class="text-secondary">This will create a system administrator account with full access privileges.</p>
                    <?php endif; ?>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-dark">Create <?= ucfirst($user_type) ?> Account</button>
                        <a href="adminAddUser.php" class="btn btn-outline-secondary">← Start Over</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>