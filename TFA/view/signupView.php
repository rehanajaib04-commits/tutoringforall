<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
</head>
<body>
    <h2>Create an Account</h2>
    
    <?php if (isset($message) && $message): ?>
        <p><strong><?= $message ?></strong></p>
    <?php endif; ?>

    <form method="post" action="../controller/signup.php">
        <input type="text" name="first_name" placeholder="First Name" required><br>
        <input type="text" name="last_name" placeholder="Last Name" required><br>
        <input type="text" name="contact_number" placeholder="Contact Number"><br>
        <input type="email" name="email_address" placeholder="Email Address" required><br>
        
        <input type="hidden" name="user_type" value="student">
        
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="text" name="security_question" placeholder="Security Question"><br>
        <input type="text" name="security_answer" placeholder="Security Answer"><br>
        
        <button type="submit" name="signup">Sign Up</button>
    </form>
    <p>Already have an account? <a href="sign_in.php">Sign In</a></p>
</body>
</html>