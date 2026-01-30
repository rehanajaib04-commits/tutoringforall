<!doctype html> 
    <head>
        <title>Sign Up Details</title>  
    </head>
    <?php
    ?>
    <body>
    <header>
    <div class = "container">
    <h1>Enter Guardain's Details</h1>
    </div>
    </header>
    <br>
    <form action= "addParentStudent.php" method="POST"> 
    Parent's Email address*: <input type="text" name="parent_email_address" id="parent_email_address" required>
    <br><br>
    Parent's First name: <input type="text" name="parent_first_name" id="parent_first_name">
    <br><br>
    Parents's Last name: <input type = "text" name="parent_last_name" id="parent_last_name">
    <br><br>
    Parent's Contact number: <input type = "text" name="parent_contact_number" id="parent_contact_number">
    <br><br>
    <input type="hidden" name="parent_user_type"  id="parent_user_type" value="parent">
    <br><br>
    password*: <input type = "password" name="parent_password" id="parent_password" required>
    <br><br>
    Security Question: <input type = "text" name="parent_security_question" id="parent_security_question">
    <br><br>
    Security Answer: <input type = "text" name="parent_security_answer" id="parent_security_answer">
    <br><br>
     <select name="parent_type" id="parent_type" required>
            <option value="Parent">Parent</option>
            <option value="other">other</option>
        </select><br>
    
    Student's Email address*: <input type="text" name="student_email_address" id="student_email_address" required>
    <br><br>
    Student's First name: <input type="text" name="student_first_name" id="student_first_name">
    <br><br>
    Student's Last name: <input type = "text" name="student_last_name" id="student_last_name">
    <br><br>
    Student's Contact number: <input type = "text" name="student_contact_number" id="student_contact_number">
    <br><br>
    <input type="hidden" name="student_user_type"  id="student_user_type" value="student">
    <br><br>
    password*: <input type = "password" name="student_password" id="student_password" required>
    <br><br>
    Security Question: <input type = "text" name="student_security_question" id="student_security_question">
    <br><br>
    Security Answer: <input type = "text" name="student_security_answer" id="student_security_answer">
    <br><br>
     <select name="student_type" id="student_type" required>
            <option value="Undergraduate">Undergraduate</option>
            <option value="Postgraduate">Postgraduate</option>
        </select><br>
    <br><br>
    <input type="submit" name="submit" id="Submit" value="Submit">
    </form>
    <br><br>
    <br>
    <header>
    </body>
</html>