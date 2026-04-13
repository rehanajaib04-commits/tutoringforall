<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as <?= ucfirst($user_type_selection) ?> - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid px-0">
        <a href="teacherlist.php" class="navbar-brand">Tutoring For All</a>
        <div class="navbar-nav ms-auto">
            <a href="sign_in.php" class="nav-link">Sign In</a>
        </div>
    </div>
</nav>

<div class="container py-5" style="max-width: 600px;">
    <div class="card border rounded-4 p-4 p-md-5">
        <h1 class="h3 fw-normal mb-1">Create your <?= htmlspecialchars($user_type_selection) ?> Account</h1>
        <p class="text-secondary mb-4">Fill in the details below.</p>

        <?php if(isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <input type="hidden" name="user_type" value="<?= htmlspecialchars($user_type_selection) ?>">

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

            <?php if ($user_type_selection === 'student'): ?>
            <div class="mb-3">
                <label class="form-label">Parent Email Address</label>
                <input type="email" name="parent_email" class="form-control" required>
                <div class="form-text">Your parent must register first before you can sign up.</div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Security Question</label>
                <select name="security_question" class="form-select">
                    <option value="pet">What is the name of your first pet?</option>
                    <option value="city">What city were you born in?</option>
                    <option value="school">What was the name of your first school?</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Security Answer</label>
                <input type="text" name="security_answer" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-dark w-100 py-2">Sign Up</button>
        </form>

        <div class="text-center mt-4">
            <a href="sign_in.php" class="text-secondary">Back to selection</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>