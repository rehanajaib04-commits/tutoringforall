<!DOCTYPE html>
<html>
<head>
    <title>Book <?= htmlspecialchars($teacher->first_name) ?></title>
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
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .nav-links a:hover {
            background-color: #f0f0f0;
        }

        /* Main Content */
        .container {
            width: 80%;
            max-width: 1000px;
            margin: 40px auto;
        }

        /* Header Section */
        .page-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #999;
            padding-bottom: 20px;
        }
        .page-header h1 {
            font-size: 32px;
            font-weight: normal;
            margin: 0 0 10px 0;
        }
        .back-link {
            text-decoration: none;
            color: #666;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .back-link:hover {
            text-decoration: underline;
            color: #333;
        }

        /* Availability Grid */
        .schedule-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .day-block {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            background-color: #fff;
        }

        .date-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .time-slots {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        /* Time Slot Buttons */
        .time-btn {
            background-color: white;
            border: 2px solid #333;
            color: #333;
            padding: 10px 25px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.2s ease;
            text-align: center;
            min-width: 100px;
        }

        .time-btn:hover {
            background-color: #333;
            color: white;
        }

        .no-slots {
            color: #777;
            font-style: italic;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
            .time-btn {
                width: 100%; 
            }
            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>

   <nav class="navbar">
    <div class="logo">Home</div>
    <div class="nav-links">
        <?php if(isset($_SESSION['email_address'])): ?>
            <span>Logged in as: <?= htmlspecialchars($_SESSION['email_address']) ?></span>
            <a href="logout.php">Sign Out</a>
        <?php else: ?>
            <a href="sign_in.php">Sign In</a>
        <?php endif; ?>
    </div>
</nav>

    <div class="container">
        <a href="teacherProfile.php?id=<?= $teacher->teacher_id ?>" class="back-link">&larr; Back to Profile</a>

        <div class="page-header">
            <h1>Select a time with <?= htmlspecialchars($teacher->first_name) ?></h1>
            <p>Subject: <?= htmlspecialchars($teacher->teacher_type ?? 'General Lesson') ?></p>
        </div>

        <div class="schedule-container">
            <?php if (!empty($availability)): ?>
                <?php foreach ($availability as $date => $times): ?>
                    <div class="day-block">
                        <h3 class="date-title"><?= $date ?></h3>
                        
                        <div class="time-slots">
                            <?php if (!empty($times)): ?>
                                <?php foreach ($times as $time): ?>
                                   <form action="confirmBooking.php" method="POST" style="display:inline;">
    <input type="hidden" name="booking_id" value="<?= $slot->booking_id ?>">
    <button type="submit" class="time-btn" onclick="return confirm('Book this lesson?')">
        <?= $timeValue ?>
    </button>
</form>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="no-slots">No times available</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-slots">No future availability found for this teacher.</p>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>