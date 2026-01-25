<!doctype html>
<html>
    <head>Signed-in</head>
    <body>
          
        <div>
            You have successfully signed in.
            <?php
            session_start();
            if (isset($_SESSION['email_address'])) {
                echo "Welcome, " . $_SESSION['email_address'];
                if (isset($_SESSION['user_type'])) {
                    echo "session user type: " . $_SESSION['user_type'];
                }
            } else {
                echo"Hello";
            }
            ?>
            <a href="../view/logout.php">Signed out</a>
       </div>

    </body>
</html>