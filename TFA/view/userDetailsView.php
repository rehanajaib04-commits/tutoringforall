<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="homepage.php" class="navbar-brand">Tutoring For All Admin</a>
        <div class="navbar-nav ms-auto">
            <a href="logout.php" class="nav-link">Sign Out</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 fw-normal">User Details</h1>
        <a href="userlist.php" class="btn btn-outline-secondary">&larr; Back to User List</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($user): ?>
        <div class="card border rounded-4 p-4">
            <h3 class="h4 mb-3">Basic Information</h3>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 200px;">Email Address</th>
                    <td><?= htmlspecialchars($user->email_address) ?></td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td><?= htmlspecialchars($user->first_name) ?></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><?= htmlspecialchars($user->last_name) ?></td>
                </tr>
                <tr>
                    <th>Contact Number</th>
                    <td><?= htmlspecialchars($user->contact_number ?: '—') ?></td>
                </tr>
                <tr>
                    <th>User Type</th>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($user->user_type) ?></span></td>
                </tr>
                <tr>
                    <th>Date of Birth</th>
                    <td><?= htmlspecialchars($user->date_of_birth ?: '—') ?></td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td><?= htmlspecialchars($user->gender ?: '—') ?></td>
                </tr>
                <tr>
                    <th>Ethnicity</th>
                    <td><?= htmlspecialchars($user->ethnicity ?: '—') ?></td>
                </tr>
                <tr>
                    <th>Security Question</th>
                    <td><?= htmlspecialchars($user->security_question ?: '—') ?></td>
                </tr>
                <tr>
                    <th>Security Answer</th>
                    <td><?= htmlspecialchars($user->security_answer ?: '—') ?></td>
                </tr>
            </table>

            <?php if ($user->user_type === 'teacher' && $teacherDetails): ?>
                <h3 class="h4 mt-4 mb-3">Teacher Profile</h3>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Teacher Type</th>
                        <td><?= htmlspecialchars($teacherDetails->teacher_type ?: '—') ?></td>
                    </tr>
                    <tr>
                        <th>Hourly Rate</th>
                        <td>£<?= htmlspecialchars($teacherDetails->hourly_rate ?: '0.00') ?></td>
                    </tr>
                    <tr>
                        <th>Bio</th>
                        <td><?= nl2br(htmlspecialchars($teacherDetails->bio ?: '—')) ?></td>
                    </tr>
                    <tr>
                        <th>Experience</th>
                        <td><?= htmlspecialchars($teacherDetails->experience ?: '—') ?></td>
                    </tr>
                    <tr>
                        <th>Rating</th>
                        <td><?= htmlspecialchars($teacherDetails->rating ?: '0') ?> / 5 (<?= (int)($teacherDetails->total_reviews ?? 0) ?> reviews)</td>
                    </tr>
                </table>
            <?php endif; ?>

            <?php if ($user->user_type === 'student' && $studentDetails): ?>
                <h3 class="h4 mt-4 mb-3">Student Profile</h3>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Student Type</th>
                        <td><?= htmlspecialchars($studentDetails->student_type ?: '—') ?></td>
                    </tr>
                </table>
            <?php endif; ?>

            <?php if ($user->user_type === 'parent'): ?>
                <h3 class="h4 mt-4 mb-3">Parent Profile</h3>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Parent Type</th>
                        <td><?= htmlspecialchars($parentDetails->parent_type ?? '—') ?></td>
                    </tr>
                </table>
                <?php if (!empty($linkedStudents)): ?>
                    <h4 class="h5 mt-3">Linked Students</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Student Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($linkedStudents as $student): ?>
                                <tr>
                                    <td><?= htmlspecialchars($student->email_address) ?></td>
                                    <td><?= htmlspecialchars($student->first_name . ' ' . $student->last_name) ?></td>
                                    <td><?= htmlspecialchars($student->student_type ?: '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>

            <div class="mt-4">
                <a href="userlist.php" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>