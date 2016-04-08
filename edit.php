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

    if( !isset($_POST['first_name']) || (strlen($_POST['first_name'])<1 || !isset($_POST['last_name']) || strlen($_POST['last_name'])<1 || !isset($_POST['email']) || strlen($_POST['email'])<1 || !isset($_POST['headline']) || strlen($_POST['headline'])<1 || !isset($_POST['summary']) || strlen($_POST['summary'])<1 ) ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=".$id);
        return;
    }
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['posyear'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        if ( ! isset($_POST['eduyear'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        if ($_POST['posyear'.$i]==""){
            $_SESSION['error'] = "All values are required";
            header("Location: edit.php?profile_id=".$id);
            exit();
        }
        if ($_POST['desc'.$i]==""){
            $_SESSION['error'] = "All values are required";
            header("Location: edit.php?profile_id=".$id);
            exit();
        }
        if (!is_numeric($_POST['posyear'.$i])){
            $_SESSION['error'] = "Years must be numeric";
            header("Location: edit.php?profile_id=".$id);
            exit();
        }
        $_POST['posyear'.$i] = htmlentities($_POST['posyear'.$i]);
        $_POST['desc'.$i] = htmlentities($_POST['desc'.$i]);
        $_POST['position_id'.$i] = htmlentities($_POST['position_id'.$i]);
    }
    if( strpos($_POST['email'], '@') == false){
        $_SESSION['error'] = "Email address must contain @";
        header("Location: edit.php?profile_id=".$id);
        return;
    }
    else{
        require_once "pdo.php";

        $stmt = $pdo->prepare('UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :hl, summary = :sum WHERE profile_id = :pid ');
        $stmt->execute(array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':hl' => $_POST['headline'],
            ':sum' => $_POST['summary'],
            ':pid' => $id
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
            if ( ! isset($_POST['posyear'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
            if (isset($_POST['position_id'.$i]) ){
                $position = htmlentities($_POST['position_id'.$i]);
                $posyear = $_POST['posyear'.$i];
                $desc = $_POST['desc'.$i];
                $rank = $i;
                $stmt = $pdo->prepare('UPDATE position SET profile_id = :pid, rank = :rank, year = :year, description = :desc WHERE position_id=:posid');
                $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $posyear,
                    ':desc' => $desc,
                    ':posid' => $position
                ));
            }
            else{
                $posyear = $_POST['posyear'.$i];
                $desc = $_POST['desc'.$i];
                $rank = $i;
                $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES(:pid, :rank, :year, :desc)');
                $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $posyear,
                    ':desc' => $desc
                ));
            }
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
            <input type="text" name="first_name" id="mk" value="' . $row['first_name'] . '"><br/>');
            echo('<label for="ml">Last Name</label>
            <input type="text" name="last_name" id="ml" value="' . $row['last_name'] . '"><br/>');
            echo('<label for="yr">Email</label>
            <input type="text" name="email" id="yr" value="' . $row['email'] . '"><br/>');
            echo('<label for="miles">Headline</label>
            <input type="text" name="headline" id="miles" value="' . $row['headline'] . '"><br/>');
            echo('<label for="miles">Summary</label>
            <input type="text" name="summary" id="miles" value="' . $row['summary'] . '"><br/>');
        }

        $count = $pdo->prepare("SELECT COUNT(*) FROM position WHERE profile_id = :id");
        $count->execute(array(
            ':id' => htmlentities($_GET['profile_id'])
        ));
        if ($count->fetchColumn() > 0) {
            $posct = 0;
            $stmt = $pdo->prepare('SELECT * FROM position WHERE profile_id = :id');
            $stmt->execute(array(
                ':id' => htmlentities($_GET['profile_id'])
            ));
            echo('<p>Position: <input type="submit" id="addPos" value="+">');
            echo('<div id="position_fields">');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $ct++;
                echo('<div id="position'. $ct .'">'."\n");
                echo('<input type="hidden" name="position_id'.$ct.'" value="'.$row['position_id'].'">');
                echo('<p>Year: <input type="text" name="posyear' . $ct . '"');
                echo(' value="' . htmlentities($row['year']) . '" />' . "\n");
                echo('<input type="button" value="-" ');
                echo('onclick="$(\'#position' . $ct . '\').remove();return false;">' . "\n");
                echo("</p>\n");
                echo('<textarea name="desc' . $ct . '" rows="8" cols="80">' . "\n");
                echo(htmlentities($row['description']) . "\n");
                echo("\n</textarea>\n</div>\n");
            }
            echo('</div>');
            echo('</p>');
        }

    }
    else{
        echo ('<p> You do not have permission to edit this profile</p><br/>');
    }

    ?>

    <input type="submit" value="Save">
    <input type="submit" name="cancel" value="cancel">
</form>
<script src="jquery-1.10.2.js"></script>
<script src="jquery-ui-1.11.4.js"></script>
<script>
    countPos = <?=$posct?>;
    countEdu = <?=$educt?>;

    // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
    $(document).ready(function(){
        window.console && console.log('Document ready called');
        $("#addPos").click(function(event){
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
                    <p>Year: <input type="text" name="posyear'+countPos+'" value="" /> \
                    <input type="button" value="-" \
                    onclick="$(\'#position'+countPos+'\').remove();countPos--;return false;"></p> \
                    <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                    </div>'
            );
        });
    });


    $(document).ready(function(){
        window.console && console.log('Document ready called');
        $("#addEdu").click(function(event){
            // http://api.jquery.com/event.preventdefault/
            event.preventDefault();
            if ( countEdu >= 9 ) {
                alert("Maximum of nine position entries exceeded");
                return;
            }
            countPos++;
            window.console && console.log("Adding position "+countEdu);
            $('#position_fields').append(
                '<div id="school'+countEdu+'"> \
                    <p>Year: <input type="text" name="eduyear'+countEdu+'" value="" /> \
                    <input type="button" value="-" \
                    onclick="$(\'#school'+countEdu+'\').remove();countEdu--;return false;"></p> \
                    <label>School:</label><input type="text" size="80" name="edu_school'+countEdu+'1" class="school" value="" />\
                    </div>'
            );
        });
        $('.school').autocomplete({
            source: "school.php"
        });
    });
</script>
</body>
</html>
