<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Tutoring For All</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body { background: #f8f9fa; }
        .reset-card {
            max-width: 440px;
            margin: 4rem auto;
            padding: 2.5rem;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-light bg-white border-bottom">
    <div class="container-fluid px-4">
        <a href="homepage.php" class="navbar-brand">Tutoring For All</a>
        <div class="navbar-nav ms-auto">
            <a href="sign_in.php" class="nav-link">Sign In</a>
        </div>
    </div>
</nav>

<main class="container">
    <div class="reset-card">
        <div class="text-center mb-4">
            <h1 class="h3 mb-1">Reset Password</h1>
            <p class="text-secondary small mb-0">Follow the steps to recover your account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-3" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <form method="POST" action="reset_password.php">
                <input type="hidden" name="step1" value="1">
                <div class="mb-3">
                    <label for="email_address" class="form-label">Email Address</label>
                    <input type="email"
                           class="form-control form-control-lg"
                           id="email_address"
                           name="email_address"
                           placeholder="you@example.com"
                           required
                           autofocus>
                </div>
                <button type="submit" class="btn btn-dark btn-lg w-100">
                    Continue
                </button>
            </form>

        <?php elseif ($step === 2): ?>
            <?php $user = unserialize($_SESSION['reset_user']); ?>
            <form method="POST" action="reset_password.php">
                <input type="hidden" name="step2" value="1">
                <div class="mb-3">
                    <label class="form-label">Security Question</label>
                    <div class="form-control-plaintext fw-semibold ps-0">
                        <?= htmlspecialchars($user->security_question ?? '') ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="security_answer" class="form-label">Your Answer</label>
                    <input type="text"
                           class="form-control form-control-lg"
                           id="security_answer"
                           name="security_answer"
                           placeholder="Type your answer"
                           required
                           autofocus
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-dark btn-lg w-100">
                    Verify Answer
                </button>
            </form>

        <?php elseif ($step === 3): ?>
            <form method="POST" action="reset_password.php">
                <input type="hidden" name="step3" value="1">
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password"
                           class="form-control form-control-lg"
                           id="new_password"
                           name="new_password"
                           placeholder="Enter new password"
                           required
                           autofocus
                           autocomplete="new-password">
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password"
                           class="form-control form-control-lg"
                           id="confirm_password"
                           name="confirm_password"
                           placeholder="Repeat new password"
                           required
                           autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-dark btn-lg w-100">
                    Update Password
                </button>
            </form>
        <?php endif; ?>

   <div class="register-link">
    <a href="sign_in.php" class="text-decoration-none small" style="color: #212529; font-weight: 600;">Back to Sign In</a>
</div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>