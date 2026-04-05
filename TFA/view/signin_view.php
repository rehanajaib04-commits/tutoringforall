<!DOCTYPE html>
<html>
<head>
    <title>Sign In</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            border-bottom: 2px solid #333;
            background-color: #fff;
        }
        .navbar .logo {
            font-size: 24px;
            font-weight: normal;
            text-decoration: none;
            color: #333;
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
            margin-left: 20px;
            font-size: 15px;
            padding: 7px 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .nav-links a:hover { background-color: #f0f0f0; }

        .page-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 50px 50px 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .card h1 {
            font-size: 28px;
            font-weight: normal;
            margin: 0 0 8px 0;
        }

        .card .subtitle {
            font-size: 14px;
            color: #777;
            margin-bottom: 30px;
        }

        .error-box {
            background-color: #fdecea;
            border: 1px solid #f5c6c6;
            color: #c62828;
            border-radius: 5px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 6px;
            color: #444;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 11px 14px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.2s;
        }
        input:focus {
            outline: none;
            border-color: #333;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
            transition: background-color 0.2s;
        }
        .btn-submit:hover { background-color: #555; }

        .footer-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .footer-link a {
            color: #333;
            font-weight: bold;
            text-decoration: none;
        }
        .footer-link a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .card { padding: 30px 25px; }
            .navbar { padding: 12px 20px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="teacherlist.php" class="logo">TutorFind</a>
    <div class="nav-links">
        <a href="signup.php">Register</a>
    </div>
</nav>

<div class="page-body">
    <div class="card">
        <h1>Sign In</h1>
        <p class="subtitle">Welcome back — sign in to your account.</p>

        <?php if (isset($_GET['registered'])): ?>
            <div style="background:#e8f5e9;border:1px solid #a5d6a7;color:#2e7d32;border-radius:5px;padding:12px 15px;margin-bottom:20px;font-size:14px;">
                &#10003; Account created successfully! Please sign in below.
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error-box"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="sign_in.php<?= isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '' ?>">
            <!-- Preserve redirect through POST as well -->
            <?php if (isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect']) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="email_address">Email Address</label>
                <input
                    type="email"
                    id="email_address"
                    name="email_address"
                    value="<?= htmlspecialchars($_POST['email_address'] ?? '') ?>"
                    placeholder="you@example.com"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Your password"
                    required
                >
            </div>

            <button type="submit" class="btn-submit">Sign In</button>
        </form>

        <div class="footer-link">
            Don't have an account? <a href="signup.php">Register here</a>
        </div>
    </div>
</div>

</body>
</html>