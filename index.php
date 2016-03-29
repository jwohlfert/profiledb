<?php
    session_start();

    /*index.php Will present a list of all profiles in the system with a link to a detailed view with view.php whether or not you are logged in. If you are not logged in, you will be given a link to login.php. If you are logged in you will see a link to add.php add a new resume and links to delete or edit any resumes that are owned by the logged in user. */

?>
<!DOCTYPE html>
<html>

<head>
    <title>John Wohlfert - Profiles Db</title>
    <?php require_once "bootstrap.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Welcome to the Profiles Database</h1>
        <?php
        require_once "pdo.php";

        if ( isset($_SESSION['success']) ) {
            echo('<p style="color: green;">'.htmlentities($_SESSION['success']).'</p>\n');
            unset($_SESSION['success']);
        }

        $count = $pdo->query("SELECT COUNT(*) FROM profile");

        if ($count->fetchColumn() > 0) {
            echo('<table border="1">
                <thead><tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Headline</th>
                    <th>Summary</th>
                    <th>Actions</th>
                </tr></thead>'
            );
            $stmt = $pdo->query("SELECT * FROM autos");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo ('<td>'.$row['first_name'].'</td>');
                echo ('<td>'.$row['last_name'].'</td>');
                echo ('<td>'.$row['headline'].'</td>');
                if ($row['user_id'] == $_SESSION['user_id']){
                    echo ('<td><a href="view.php?profile_id='.$row['profile_id'].'">View</a> / <a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / <a href = "delete.php?profile_id='.$row['profile_id'].'">Delete</a></td>');
                }else{
                    echo('<td><a href="view.php?profile_id='.$row['profile_id'].'">View</a></td>');
                }
                echo '</tr>';
                $_SESSION['profile_id']= htmlentities($row['profile_id']);
            }
            echo '</table>';
        }
        else {
            echo ('<p> No rows found</p><br/>');
        }

        if (isset($_SESSION['name'])){
            echo ('<a href="add.php"> Add New Entry</a><br/>');
            echo ('<a href="logout.php">Logout</a>');
        }
        else {
            echo('<p> <a href="login.php">Please log in</a> </p>');
        }
        ?>
    </div>
</body>
