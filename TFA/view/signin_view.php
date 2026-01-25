 <!doctype html>
<html>
    <head>
        <title>Sign In</title>  
    </head>
    <body>
    <header>
    <div class = "container">  
         <?php if (!empty($error)): ?>
    <p style="color:red"><?= $error ?></p>
    <?php endif; ?>   
    <h1>Enter Details</h1>
    </div>
    </header>
    <br>
    <form method="post" action="../controller/sign_in.php">
        email address: <input type="text" name="email_address" required/>
        <br><br>
        Password: <input type="password" name="password" required/>
        <br><br>
        <input type="submit" name="submit" value="Sign In">
    </form>  
    <p>Dont have an account? <a href="signup.php">Sign Up</a></p>   
    </body>
</html>