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

if(!empty($_POST)){
    $id = htmlentities($_SESSION['autos_id']);
    unset($_SESSION['autos_id']);

    if ( isset($_POST['cancel'] ) ) {
        // Redirect the browser to index.php
        $_SESSION['success'] = "Delete canceled";
        header("Location: index.php");
        return;
    }

    if( htmlentities($_POST['delete'])=="delete" ){
        require_once "pdo.php";

        $stmt = $pdo->prepare('DELETE FROM autos WHERE autos_id = :id');
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
    echo ('<h1>Tracking Autos for '.$_SESSION['name'].'</h1>');

    if ( isset($_SESSION['error']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
    }

    $_SESSION['autos_id'] =  htmlentities($_GET['autos_id']);

    $stmt = $pdo->prepare("SELECT * FROM autos WHERE autos_id = :id");
    $stmt->execute(array(
        ':id' => htmlentities($_GET['autos_id'])
    ));

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo ('<p>Confirm: Deleting '.$row['make'].' '.$row['model'].'</p>');
    }
?>

<form method="POST">
    <input type="submit" name="delete" value="delete">
    <input type="submit" name="cancel" value="cancel">
</form>
</body>
</html>
