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
if (isset($_SESSION['name']) && isset($_SESSION['user_id'])){
    $_SESSION['name'] = htmlentities($_SESSION['name']);
    $_SESSION['user_id'] = htmlentities($_SESSION['user_id']);

}
if (!empty($_POST)){
    if (isset($_POST['Cancel'] ) ) {
        // Redirect the browser to index.php
        header("Location: index.php");
        exit();
    }

    $_POST['first_name'] = htmlentities($_POST['first_name']);
    $_POST['last_name'] = htmlentities($_POST['last_name']);
    $_POST['email'] = htmlentities($_POST['email']);
    $_POST['headline'] = htmlentities($_POST['headline']);
    $_POST['summary'] = htmlentities($_POST['summary']);

    if(empty($_SESSION['user_id']) || $_SESSION['user_id'] == ""){
        $_SESSION['error'] = "Must be logged in";
        header("Location: add.php");
        exit();
    }
    if(empty($_POST['first_name']) || $_POST['first_name'] == ""){
        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        exit();
    }
    if(empty($_POST['last_name']) || $_POST['last_name'] == ""){
        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        exit();
    }
    if(empty($_POST['email']) || $_POST['email'] == "") {
        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        exit();
    }
    if(empty($_POST['headline']) || $_POST['headline'] == ""){
        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        exit();
    }
    if(empty($_POST['summary']) || $_POST['summary'] == ""){
        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        exit();
    }

    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        if ($_POST['year'.$i]==""){
            $_SESSION['error'] = "All values are required";
            header("Location: add.php");
            exit();
        }
        if ($_POST['desc'.$i]==""){
            $_SESSION['error'] = "All values are required";
            header("Location: add.php");
            exit();
        }
        if (!is_numeric($_POST['year'.$i])){
            $_SESSION['error'] = "Years must be numeric";
            header("Location: add.php");
            exit();
        }
        $_POST['year'.$i] = htmlentities($_POST['year'.$i]);
        $_POST['desc'.$i] = htmlentities($_POST['desc'.$i]);
    }
    if( strpos($_POST['email'], '@') == false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: add.php");
        exit();
    }

    if (!empty($_POST)){
        require_once "pdo.php";

        $stmt = $pdo->prepare('INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :ui, :fn, :ln, :em, :hl, :sum)');
        $stmt->execute(array(
            ':ui' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':hl' => $_POST['headline'],
            ':sum' => $_POST['summary']
        ));
        $stmt = $pdo->prepare('SELECT profile_id FROM profile WHERE user_id = :ui AND first_name = :fn AND last_name = :ln');
        $stmt->execute(array(
            ':ui' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name']
        ));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $profile_id = $row['profile_id'];
        }

        for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
            $rank = $i;
            $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
            $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc)
            );
        }

        $_SESSION['success'] = 'Records added';
        header("Location: index.php");
        exit();
    }
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
    echo ('<h1>Tracking Profiles for '.$_SESSION['name'].'</h1>');

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
        <p>Position: <input type="submit" id="addPos" value="+">
        <div id="position_fields">
        </div>
        </p>
        <input type="submit" value="Add">
        <input type="submit" name="Cancel" value="Cancel">
    </form>
    <script src="jquery-1.10.2.js"></script>
    <script src="jquery-ui-1.11.4.js"></script>
    <script>
        countPos = 0;

        // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
        $(document).ready(function(){
            window.console && console.log('Document ready called');
            $('#addPos').click(function(event){
                // http://api.jquery.com/event.preventdefault/
                event.preventDefault();
                if ( countPos >= 9 ) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                window.console && console.log("Adding position "+countPos);
                $('#position_fields').append(
                    '<div id="position'+countPos+'"> \
                    <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                    <input type="button" value="-" \
                    onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                    <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                    </div>'
                );
            });
        });
    </script>
</body>
</html>
