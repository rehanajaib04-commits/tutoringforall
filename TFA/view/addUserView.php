<!DOCTYPE html>
<html>
<head><title>Add New User</title></head>
<body>
    <h2>System Administrator: Add New User</h2>
    
    <?php if ($message): ?>
        <p><strong><?= $message ?></strong></p>
    <?php endif; ?>

    <form method="post" action="../controller/addUser.php">
        <input type="text" name="first_name" placeholder="First Name" required><br>
        <input type="text" name="last_name" placeholder="Last Name" required><br>
        <input type="text" name="contact_number" placeholder="Contact Number"><br>
        <input type="email" name="email_address" placeholder="Email Address" required><br>
        
        <label>User Type:</label>
        <select name="user_type">
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="admin">Administrator</option>
        </select><br>
        
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="text" name="security_question" placeholder="Security Question"><br>
        <input type="text" name="security_answer" placeholder="Security Answer"><br>
        
        <button type="submit" name="addUser">Create User</button>
    </form>
    <br>
    <a href="userlist.php">Back to User List</a>
</body>
</html>