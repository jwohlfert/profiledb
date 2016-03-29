<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 3/10/2016
 * Time: 10:05 PM
 */
session_start();

if (!isset($_SESSION['name'])){
    die("ACCESS DENIED");
}

    if ( isset($_POST['Cancel'] ) ) {
        // Redirect the browser to index.php
        header("Location: index.php");
        exit();
    }

    if(!empty($_POST) && empty($_POST['first_name']) ){
        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        exit();
    }
    if(!empty($_POST) && empty($_POST['last_name']) ){
        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        exit();
    }
    if(!empty($_POST) && empty($_POST['email']) ) {
        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        exit();
    }
    if(!empty($_POST) && empty($_POST['headline']) ){
        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        exit();
    }
    if(!empty($_POST) && empty($_POST['summary']) ){
    	$_SESSION['error'] = "All values are required";
    	header("Location: add.php");
    	exit();
    }

    $_POST['first_name'] = htmlentities($_POST['first_name']);
    $_POST['last_name'] = htmlentities($_POST['last_name']);
    $_POST['email'] = htmlentities($_POST['email']);
    $_POST['headline'] = htmlentities($_POST['headline']);
    $_POST['summary'] = htmlentities($_POST['summary']);

    if (!empty($_POST)){
        require_once "pdo.php";

        $stmt = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :ui, :fn, :ln, :em, :hl, :sum)');
        $stmt->execute(array(
            ':ui' => $_Session['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
        	':hl' => $_POST['headline'],
        	':sum' => $_POST['summary']
        ));

        $_SESSION['success'] = 'Record added';
        header("Location: index.php");
        exit();
    }

?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>John Wohlfert's Profiles Database Entry</title>
</head>

<body>
    <?php
    echo ('<h1>Tracking Autos for '.$_SESSION['name'].'</h1>');

    if ( isset($_SESSION['error']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
    }
    ?>

    <form method="POST">
        <label for="f_name">First Name</label>
        <input type="text" name="first_name" id="f_name"><br/>
        <label for="l_name">Last Name</label>
        <input type="text" name="last_name" id="l_name"><br/>
        <label for="mail">Email</label>
        <input type="text" name="email" id="mail"><br/>
        <label for="hline">Headline</label>
        <input type="text" name="headline" id="hline"><br/>
        <label for="summ">Summary</label>
        <input type="text" name="summary" id="summ"><br/>
        <input type="submit" value="Add">
        <input type="submit" name="Cancel" value="Cancel">
    </form>
</body>
</html>
