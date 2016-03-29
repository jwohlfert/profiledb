<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 3/12/2016
 * Time: 4:18 PM
 */
    session_start();
    if (!isset($_SESSION['name'])){
        die("Not logged in");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <title>John Wohlfert's Auto's Database Entry</title>
    </head>
<body>
    <?php
        echo ('<h1>Tracking Autos for '.$_SESSION['name'].'</h1>');

        if ( isset($_SESSION['success']) ) {
            echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
            unset($_SESSION['success']);
        }
    ?>

    <h2>Automobiles</h2>
    <?php
    echo "<ul>\n";
    require_once "pdo.php";

    $stmt = $pdo->query("SELECT * FROM autos");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        echo "<li>".$row['year']." ".$row['make'].' / '.$row['mileage']."</li>";
    }
    echo "</ul>\n";?>

    <a href="add.php">Add New</a> |
    <a href="logout.php">Logout</a>


</body>
</html>
