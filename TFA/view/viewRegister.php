<!DOCTYPE html>
<html>
<head>
    <title>Register as <?php echo ucfirst($user_type_selection); ?></title>
</head>
<body>
    <h2>Create your <?php echo htmlspecialchars($user_type_selection); ?> Account</h2>

    <?php if(isset($error_message)) { echo "<p style='color:red'>$error_message</p>"; } ?>

    <form action="signup.php" method="POST">
        
        <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($user_type_selection); ?>">

        <div>
            <label>First Name:</label>
            <input type="text" name="first_name" required>
        </div>

        <div>
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
        </div>

        <div>
            <label>Contact Number:</label>
            <input type="text" name="contact_number">
        </div>

        <div>
            <label>Email Address:</label>
            <input type="email" name="email_address" required>
        </div>

        <?php if ($user_type_selection === 'student') { ?>
        <div>
            <label>Parent Email Address:</label>
            <input type="email" name="parent_email" required>
            <small style="color: #666; display: block; margin-top: 5px;">Your parent must register first before you can sign up.</small>
        </div>
        <?php } ?>

        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>

        <div>
            <label>Security Question:</label>
            <select name="security_question">
                <option value="pet">What is the name of your first pet?</option>
                <option value="city">What city were you born in?</option>
                <option value="school">What was the name of your first school?</option>
            </select>
        </div>

        <div>
            <label>Security Answer:</label>
            <input type="text" name="security_answer" required>
        </div>

        <br>
        <button type="submit">Sign Up</button>
    </form>
    
    <br>
    <a href="signup.php">Back to selection</a>
</body>
</html>