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
        <a href="homepage.php" class="navbar-brand">Tutoring For All Admin</a>
        <div class="navbar-nav ms-auto">
            <a href="logout.php" class="nav-link">Sign Out</a>
        </div>
    </div>
</nav>

<div class="container py-4" style="max-width: 600px;">
    <h1 class="h2 fw-normal mb-4">System Administrator: Add New User</h1>

    <?php if ($message): ?>
        <div class="alert <?= $message_type === 'success' ? 'alert-success' : 'alert-danger' ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card border rounded-4 p-4">
        <form method="post" action="systemAdminstrator.php">
            <div class="mb-3">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($formData['first_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($formData['last_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($formData['contact_number']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email_address" class="form-control" value="<?= htmlspecialchars($formData['email_address']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select name="user_type" class="form-select" required>
                    <option value="student" <?= $formData['user_type'] === 'student' ? 'selected' : '' ?>>Student</option>
                    <option value="teacher" <?= $formData['user_type'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                    <option value="parent" <?= $formData['user_type'] === 'parent' ? 'selected' : '' ?>>Parent</option>
                    <option value="admin" <?= $formData['user_type'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                </select>
            </div>
            <!-- New fields: Date of Birth, Gender, Ethnicity -->
            <div class="mb-3">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($formData['date_of_birth'] ?? '') ?>">
               
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Select...</option>
                    <option value="Male" <?= ($formData['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($formData['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($formData['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                    <option value="Prefer not to say" <?= ($formData['gender'] ?? '') === 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Ethnicity</label>
                <input type="text" name="ethnicity" class="form-control" value="<?= htmlspecialchars($formData['ethnicity'] ?? '') ?>" placeholder="e.g., White British, Indian">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Security Question</label>
                <input type="text" name="security_question" class="form-control" value="<?= htmlspecialchars($formData['security_question']) ?>">
            </div>
            <div class="mb-4">
                <label class="form-label">Security Answer</label>
                <input type="text" name="security_answer" class="form-control" value="<?= htmlspecialchars($formData['security_answer']) ?>">
            </div>
            <button type="submit" name="addUser" class="btn btn-dark w-100">Create User</button>
        </form>
    </div>

    <div class="mt-4">
        <a href="userlist.php" class="btn btn-outline-secondary">&larr; Back to User List</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>