<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; text-align: center; padding: 50px; }
        .message-box { border: 1px solid #ccc; padding: 30px; display: inline-block; border-radius: 8px; }
        .success { color: green; }
        .error { color: red; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; text-decoration: none; border: 1px solid #333; color: #333; }
    </style>
</head>
<body>

    <div class="message-box">
        <?php if ($success): ?>
            <h1 class="success">Booking Confirmed!</h1>
            <p>Your lesson for <strong><?= htmlspecialchars($date) ?></strong> at <strong><?= htmlspecialchars($time) ?></strong> has been scheduled.</p>
        <?php else: ?>
            <h1 class="error">Booking Failed</h1>
            <p>We're sorry, that slot may have just been taken or is no longer available.</p>
        <?php endif; ?>

        <a href="bookingspage.php?teacher_id=<?= $teacher_id ?>" class="btn">Back to Availability</a>
        <a href="dashboard.php" class="btn">Go to My Lessons</a>
    </div>

</body>
</html>