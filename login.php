<?php // Do not put any HTML above this line
    session_start();

    /*
    will present the user the login screen with an email address and password to get the user to log in. If there is an error, redirect the user back to the login page with a message. If the login is successful, redirect the user back to index.php after setting up the session. In this assignment, you will need to store the user's hashed password in the users table as described below.
    */
    if ( isset($_POST['cancel'] ) ) {
        // Redirect the browser to index.php
        $_SESSION['success'] = "Login canceled";
        header("Location: index.php");
        return;
    }

    if ($_POST){
        if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
            $_SESSION['error'] = "User name and password are required";
            header("Location: login.php");
            return;
        }
        require_once "pdo.php";
        $_POST['email'] = htmlentities($_POST['email']);
        $_POST['pass'] = htmlentities($_POST['pass']);
        $salt = 'XyZzy12*_';
        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name, password FROM users WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( $check == $row['password'] ) {
            error_log("Login success ".$_POST['email']);
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: index.php");
            return;

        }
        else {
            $_SESSION['error'] = "Incorrect password";
            header("Location: login.php");
            exit();

        }
    }



    // Fall through into the View
?>
<!DOCTYPE html>
<html>

<head>
    <?php require_once "bootstrap.php"; ?>
    <title>John Wohlfert's Login Page</title>
</head>

<body>
    <div class="container">
        <h1>Please Log In</h1>

        <?php
        if ( isset($_SESSION['error']) ) {
            echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
            unset($_SESSION['error']);
        }
        //$md5 = hash('md5', 'XyZzy12*_php123');
        //echo($md5."\n");
        ?>

        <form method="POST">
            <label for="nam">Email</label>
            <input type="text" name="email" id="nam"><br/>
            <label for="id_1723">Password</label>
            <input type="text" name="pass" id="id_1723"><br/>
            <input type="submit" value="Log In">
            <input type="submit" name="Cancel" value="Cancel">
        </form>

        <p>
        For a password hint, view source and find a password hint
        in the HTML comments.
        <!-- Hint: The password is the three character name (all lower case) of the
        programming language that runs on the server side of this page
        followed by 123. -->
        </p>

    </div>
</body>
