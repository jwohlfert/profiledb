<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 3/12/2016
 * Time: 4:18 PM
 */
    session_start();

    $_SESSION['name'] = htmlentities($_SESSION['name']);
    $_SESSION['user_id'] = htmlentities($_SESSION['user_id']);
    $_GET['profile_id'] = htmlentities($_GET['profile_id']);


?>

<!DOCTYPE html>
<html>
    <head>
        <?php require_once "bootstrap.php"; ?>
        <title>John Wohlfert's Profiles Database Entry</title>
    </head>
<body>
    <?php
        echo ('<h1>Tracking Profiles for '.$_SESSION['name'].'</h1>');

        if ( isset($_SESSION['success']) ) {
            echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
            unset($_SESSION['success']);
        }
    ?>

    <h2>Profiles</h2>
    <?php

    require_once "pdo.php";
    $count = $pdo->prepare("SELECT COUNT(*) FROM profile WHERE profile_id= :pid");
    $count->execute(array(
        ':pid' => $_GET['profile_id']
    ));

    if ($count->fetchColumn() > 0) {
        echo('<table border="1">');
        echo('<thead>');
        echo('<tr>');
        echo('<th>First Name</th>');
        echo('<th>Last Name</th>');
        echo('<th>Email</th>');
        echo('<th>Headline</th>');
        echo('<th>Summary</th>');
        echo('<th>Actions</th>');
        echo('</tr>');
        echo('</thead>');

        echo('<tbody>');

        $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id= :pid");
        $count->execute(array(
            ':pid' => $_GET['profile_id']
        ));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo('<tr>');
            echo('<td>' . $row['first_name'] . '</td>');
            echo('<td>' . $row['last_name'] . '</td>');
            echo('<td>' . $row['email'] . '</td>');
            echo('<td>' . $row['headline'] . '</td>');
            echo('<td>' . $row['summary'] . '</td>');
            if ($row['user_id'] == $_SESSION['user_id']) {
                echo('<td><a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / <a href = "delete.php?profile_id='.$row['profile_id'].'">Delete</a></td>');
            }
            echo('</tr>');
        }
        echo('</tbody>');
    }
    else {
        echo ('<p> No rows found</p><br/>');
    }
    ?>

    <a href="index.php">Home</a>
</body>
</html>
