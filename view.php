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

        $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id= :pid");
        $stmt->execute(array(
            ':pid' => $_GET['profile_id']
        ));

        echo('<ul style="list-style-type:none">');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo ('<li>First Name
                       <ul>
                       <li>' . $row['first_name'] . '</li>
                       </ul>
                    </li>');

            echo ('<li>Last Name
                       <ul>
                       <li>' . $row['last_name'] . '</li>
                       </ul>
                   </li>');

            echo ('<li>Email
                       <ul>
                       <li>' . $row['email'] . '</li>
                       </ul>
                   </li>');

            echo ('<li>Headline
                       <ul>
                       <li>' . $row['headline'] . '</li>
                       </ul>
                   </li>');

            echo ('<li>Summary
                       <ul>
                       <li>' . $row['summary'] . '</li>
                       </ul>
                   </li>');

        }

        $count = $pdo->prepare("SELECT COUNT(*) FROM position WHERE profile_id = :id");
        $count->execute(array(
            ':id' => htmlentities($_GET['profile_id'])
        ));

        if ($count->fetchColumn() > 0) {
             $stmt = $pdo->prepare('SELECT * FROM position WHERE profile_id = :id');
            $stmt->execute(array(
                ':id' => htmlentities($_GET['profile_id'])
            ));
            echo('<li>Positions');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo('<ul>');
                echo('<li> Year: '.htmlentities($row['year']).'</li>');
                echo('<li> Description: '.htmlentities($row['description']).'</li>');
                echo('</ul>');
            }
            echo('</li>');
        }
        echo('</ul>');
    }
    else {
        echo ('<p> No rows found</p><br/>');
    }
    ?>

    <a href="index.php">Home</a>
</body>
</html>
