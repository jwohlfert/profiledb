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
    $id = htmlentities($_SESSION['profile_id']);
    unset($_SESSION['profile_id']);

    if ( isset($_POST['cancel'] ) ) {
        // Redirect the browser to index.php
        $_SESSION['success'] = "Delete canceled";
        header("Location: index.php");
        return;
    }

    if( htmlentities($_POST['delete'])=="Delete" ){
        require_once "pdo.php";

        $stmt = $pdo->prepare('DELETE FROM profile WHERE profile_id = :id');
        $stmt->execute(array(
            ':id' => $id));

        $_SESSION['success'] = "Record deleted";
        header("Location: index.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>John Wohlfert's Auto's Database Edit</title>
</head>

<body>
<?php
require_once "pdo.php";
$_SESSION['profile_id'] =  htmlentities($_GET['profile_id']);

    $count = $pdo->prepare("SELECT COUNT(*) FROM profile WHERE profile_id = :id and user_id=:ui");
    $count->execute(array(
        ':id' => htmlentities($_GET['profile_id']),
        ':ui' => $_SESSION['user_id']
    ));
    if ($count->fetchColumn() > 0) {
        $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :id");
        $stmt->execute(array(
            ':id' => htmlentities($_GET['profile_id'])
        ));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo('<p>Confirm: Deleting ' . $row['first_name'] . ' ' . $row['last_name'] . '\'s Profile?</p>');
        }
    }
    else{
    echo ('<p> You do not have permission to delete this </p><br/>');
    }

?>

<form method="POST">

    <input type="submit" name="delete" value="Delete">
    <input type="submit" name="cancel" value="cancel">
</form>
</body>
</html>
