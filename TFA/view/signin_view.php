<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/signin_view.css">
</head>
<body>

<nav class="navbar navbar-expand navbar-light">
    <div class="container-fluid px-0">
        <a href="homepage.php" class="navbar-brand">Tutoring For All</a>
        <div class="navbar-nav ms-auto">
            <a href="signup.php" class="nav-link">Register</a>
        </div>
    </div>
</nav>

<main class="main-content">
    <div class="signin-card">
        <div class="signin-header">
            <h1>Sign In</h1>
            <p>Welcome back &mdash; sign in to your account</p>
        </div>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i>Account created successfully! Please sign in below.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['reset'])): ?>
            <div class="alert alert-success mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i>Password reset successfully! Please sign in with your new password.
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="sign_in.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>">
            <?php if (isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect']) ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="email_address" class="form-label">Email Address</label>
                <input
                    type="email"
                    class="form-control form-control-lg"
                    id="email_address"
                    name="email_address"
                    value="<?= htmlspecialchars($_POST['email_address'] ?? '') ?>"
                    placeholder="you@example.com"
                    required
                    autofocus
                >
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control form-control-lg"
                    id="password"
                    name="password"
                    placeholder="Your password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-dark btn-lg w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>

        <div class="register-link">
            <a href="reset_password.php" class="text-decoration-none small">Forgot your password?</a>
        </div>

        <div class="register-link">
            Don't have an account? <a href="signup.php">Register here</a>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>