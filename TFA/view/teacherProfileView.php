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

        /* Navigation Bar */
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
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
            margin-left: 20px;
            font-size: 16px;
            padding: 8px 12px;
            border: 1px solid #ccc; /* replicating the button look in wireframe */
            border-radius: 4px;
        }
        .nav-links a:hover {
            background-color: #f0f0f0;
        }

        /* Main Container */
        .container {
            display: flex;
            width: 80%;
            max-width: 1200px;
            margin: 40px auto;
            gap: 50px;
        }

        /* Left Sidebar (Image, Rating, Bio) */
        .left-sidebar {
            width: 30%;
            display: flex;
            flex-direction: column;
        }

        .profile-pic-container {
            width: 100%;
            aspect-ratio: 1 / 1;
            border: 2px solid #333;
            border-radius: 15px;
            overflow: hidden;
            background-color: #f9f9f9;
            position: relative;
            margin-bottom: 10px;
        }
        
        /* Placeholder icon style if no image */
        .profile-pic-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            color: #ccc;
        }

        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .rating-block {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .star-icon {
            font-size: 24px;
        }

        .bio-section h3 {
            font-size: 18px;
            margin-bottom: 10px;
            text-decoration: underline; /* Matches wireframe style somewhat */
        }
        .bio-text {
            font-size: 14px;
            line-height: 1.6;
            color: #555;
            white-space: pre-line; /* Preserves line breaks */
        }

        /* Right Content (Details) */
        .right-content {
            width: 70%;
        }

        .teacher-name {
            font-size: 42px;
            margin: 0 0 20px 0;
            font-weight: normal;
        }

        /* Section Dividers and Layout */
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

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .info-list li {
            margin-bottom: 5px;
            font-size: 16px;
        }

        /* Action Links with Arrows */
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

        /* Contact Details */
        .contact-details p {
            margin: 5px 0;
            font-size: 16px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                width: 90%;
            }
            .left-sidebar, .right-content {
                width: 100%;
            }
            .navbar {
                padding: 10px 20px;
            }
            .nav-links {
                display: none; /* Hide nav links on mobile for simplicity, or add hamburger menu logic */
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">Home</div>
        <div class="nav-links">
            <a href="#">Search</a>
            <a href="#">Lessons</a>
            <a href="#">Sign Out</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="left-sidebar">
          

            <div class="rating-block">
                Reviews: <?= number_format($teacher->rating ?? 0, 1) ?> 
            </div>

            <div class="bio-section">
                <h3>Teachers Bio:</h3>
                <div class="bio-text">
                    <?= !empty($teacher->bio) ? htmlspecialchars($teacher->bio) : "-------------------\n-------------------\n-------------------" ?>
                </div>
            </div>
        </div>

        <div class="right-content">
            <h1 class="teacher-name"><?= htmlspecialchars(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')) ?></h1>

            <div class="info-section">
                <span class="section-label">Qualifications:</span>
                <ul class="info-list">
                    <?php if (!empty($teacher->qualifications)): ?>
                        <?php 
                        $qualifications = explode(',', $teacher->qualifications);
                        foreach ($qualifications as $qual): ?>
                            <li><?= htmlspecialchars(trim($qual)) ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No qualifications listed</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="info-section">
                <span class="section-label">Teaches:</span>
                <ul class="info-list">
                    <li><?= htmlspecialchars($teacher->teacher_type ?? 'General Subjects') ?></li>
                    <?php if (!empty($teacher->subjects)): ?>
                         <li><?= htmlspecialchars($teacher->subjects) ?></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="info-section">
                <a href="reviews.php?teacher_id=<?= $teacher->teacher_id ?>" class="action-link">
                    See Reviews:
                    <span class="arrow">&rarr;</span>
                </a>
            </div>

            <div class="info-section">
                <a href="bookingspage.php?teacher_id=<?= $teacher->teacher_id ?>" class="action-link">
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