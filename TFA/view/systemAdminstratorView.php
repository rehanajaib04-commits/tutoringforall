<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Tutoring For All Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/common.css">
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

<div class="container py-4" style="max-width: 600px;">
    <h1 class="h2 fw-normal mb-4">System Administrator: Add New User</h1>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card border rounded-4 p-4">
        <form method="post" action="../controller/SystemAdminstrator.php">
            <div class="mb-3">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email_address" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select name="user_type" class="form-select">
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Security Question</label>
                <input type="text" name="security_question" class="form-control">
            </div>
            <div class="mb-4">
                <label class="form-label">Security Answer</label>
                <input type="text" name="security_answer" class="form-control">
            </div>
            <button type="submit" name="addUser" class="btn btn-dark w-100">Create User</button>
        </form>
    </div>

    <div class="mt-4">
        <a href="systemAdminstrator.php" class="btn btn-outline-secondary">← Back to User List</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>