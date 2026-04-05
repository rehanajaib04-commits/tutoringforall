<!DOCTYPE html>
<html>
<head>

</head>
<body>


    <?php if(isset($error_message)) { echo "<p style='color:red'>$error_message</p>"; } ?>

    <form action="signup.php" method="POST">
        


                 <div>
            <label>studentFirst Name:</label>
            <input type="text" name="student_first_name" required>
        </div>

        <div>
            <label>studentLast Name:</label>
            <input type="text" name="student_last_name" required>
        </div>
        
        <div>
            <label>StudentContact Number:</label>
            <input type="text" name="student_contact_number">
        </div>

        <div>
            <label>StudentEmail Address:</label>
            <input type="email" name="student_email_address" required>
        </div>
        
        
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
    <a href="sign_in.php">Sign in</a>
</body>
</html>