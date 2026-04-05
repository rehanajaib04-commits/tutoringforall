<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($teacher->first_name ?? 'Teacher') ?>'s Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            border-bottom: 2px solid #333;
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
            font-size: 16px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .nav-links a:hover {
            background-color: #f0f0f0;
        }
        .nav-links span {
            margin-left: 20px;
            font-size: 14px;
            color: #555;
        }

        .container {
            display: flex;
            width: 80%;
            max-width: 1200px;
            margin: 40px auto;
            gap: 50px;
        }

        .left-sidebar {
            width: 30%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-pic-container {
            width: 100%;
            aspect-ratio: 1 / 1;
            border: 2px solid #333;
            border-radius: 15px;
            overflow: hidden;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .profile-pic-placeholder {
            font-size: 80px;
            color: #bbb;
        }

        .right-content {
            width: 70%;
        }

        .teacher-name {
            font-size: 42px;
            margin: 0 0 20px 0;
            font-weight: normal;
        }

        .info-section {
            border-bottom: 1px solid #999;
            padding: 20px 0;
        }
        .info-section:first-of-type {
            border-top: 1px solid #999;
        }

        .section-label {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
            display: block;
        }

        .action-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            color: #000;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }
        .action-link:hover {
            opacity: 0.7;
        }
        .arrow {
            font-size: 24px;
        }

        .contact-details p {
            margin: 5px 0;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                width: 90%;
            }
            .left-sidebar, .right-content {
                width: 100%;
            }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="teacherlist.php" class="logo">Home</a>
    <div class="nav-links">
        <?php if (isset($_SESSION['email_address'])): ?>
            <span>Logged in as: <?= htmlspecialchars($_SESSION['email_address']) ?></span>
            <a href="logout.php">Sign Out</a>
        <?php else: ?>
            <a href="sign_in.php">Sign In</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">

    <div class="left-sidebar">
        <div class="profile-pic-container">
            <span class="profile-pic-placeholder">&#128100;</span>
        </div>
    </div>

    <div class="right-content">
        <h1 class="teacher-name"><?= htmlspecialchars(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')) ?></h1>

        <div class="info-section">
            <span class="section-label">Teaches:</span>
            <p><?= htmlspecialchars($teacher->teacher_type ?? 'General Subjects') ?></p>
        </div>

        <div class="info-section">
            <a href="availability.php?teacher_email=<?= urlencode($teacher->email_address) ?>" class="action-link">
                See Availability
                <span class="arrow">&rarr;</span>
            </a>
        </div>

        <div class="info-section">
            <span class="section-label">Contact Details:</span>
            <div class="contact-details">
                <?php if (!empty($teacher->contact_number)): ?>
                    <p>Number: <?= htmlspecialchars($teacher->contact_number) ?></p>
                <?php endif; ?>
                <?php if (!empty($teacher->email_address)): ?>
                    <p>Email: <?= htmlspecialchars($teacher->email_address) ?></p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

</body>
</html>