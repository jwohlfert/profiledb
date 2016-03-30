<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 3/10/2016
 * Time: 10:05 PM
 */
session_start();

if (!isset($_SESSION['name'])){
    die("Not logged in");
}

$_SESSION['name'] = htmlentities($_SESSION['name']);
$_SESSION['user_id'] = htmlentities($_SESSION['user_id']);

if(!empty($_POST)){
    $id = $_SESSION['profile_id'];
    unset($_SESSION['profile_id']);

    if ( isset($_POST['cancel'] ) ) {
        // Redirect the browser to index.php
        $_SESSION['success'] = "Edit canceled";
        header("Location: index.php");
        return;
    }

    if( !isset($_POST['first_name']) || (strlen($_POST['first_name'])<1) ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=".$id);
        return;
    }
    if( !isset($_POST['last_name']) || strlen($_POST['last_name'])<1 ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=".$id);
        return;
    }
    if( !isset($_POST['email']) || strlen($_POST['email'])<1 ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=".$id);
        return;
    }
    if( !isset($_POST['headline']) || strlen($_POST['headline'])<1 ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=".$id);
        return;
    }
    if( !isset($_POST['summary']) || strlen($_POST['summary'])<1 ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=".$id);
        return;
    }
    if( strpos($_POST['email'], '@') == false){
        $_SESSION['error'] = "Email address must contain @";
        header("Location: edit.php?profile_id=".$id);
        return;
    }
    else{
        require_once "pdo.php";

        $_POST['first_name'] = htmlentities($_POST['first_name']);
        $_POST['last_name'] = htmlentities($_POST['last_name']);
        $_POST['email'] = htmlentities($_POST['email']);
        $_POST['headline'] = htmlentities($_POST['headline']);
        $_POST['summary'] = htmlentities($_POST['summary']);


        $stmt = $pdo->prepare('INSERT INTO profile(user_id,first_name, last_name, email, headline, summary) VALUES ( :ui, :fn, :ln, :em, :hl, :sum)');
        $stmt->execute(array(
            ':ui' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':hl' => $_POST['headline'],
            ':sum' => $_POST['summary']));

        $_SESSION['success'] = "Record edited";
        header("Location: index.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>John Wohlfert's Profiles Database Edit Page</title>
</head>

<body>
<?php
echo ('<h1>Tracking Profiles for '.$_SESSION['name'].'</h1>');

if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>

<form method="POST">
    <?php
    require_once "pdo.php";
    $_SESSION['profile_id'] =  htmlentities($_GET['profile_id']);
    $count = $pdo->prepare("SELECT COUNT(*) FROM profile WHERE profile_id = :id and user_id=:ui");
    $count->execute(array(
        ':id' => htmlentities($_GET['profile_id']),
        ':ui' => $_SESSION['user_id']
    ));
    if ($count->fetchColumn() > 0) {
        $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :id and user_id=:ui");
        $stmt->execute(array(
            ':id' => htmlentities($_GET['profile_id']),
            ':ui' => $_SESSION['user_id']
        ));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo('<label for="mk">First Name</label>
            <input type="text" name="first_name" id="mk" value="' . $row['first_name'] . '"><br/>
            <label for="ml">Last Name</label>
            <input type="text" name="last_name" id="ml" value="' . $row['last_name'] . '"><br/>
            <label for="yr">Email</label>
            <input type="text" name="email" id="yr" value="' . $row['email'] . '"><br/>
            <label for="miles">Headline</label>
            <input type="text" name="headline" id="miles" value="' . $row['headline'] . '"><br/>
            <label for="miles">Summary</label>
            <input type="text" name="summary" id="miles" value="' . $row['summary'] . '"><br/>');
        }
    }
    else{
        echo ('<p> You do not have permission to edit this profile</p><br/>');
    }

    ?>

    <input type="submit" value="Save">
    <input type="submit" name="cancel" value="cancel">
</form>

</body>
</html>
