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
    $id = $_SESSION['autos_id'];
    unset($_SESSION['autos_id']);

    if ( isset($_POST['cancel'] ) ) {
        // Redirect the browser to index.php
        $_SESSION['success'] = "Edit canceled";
        header("Location: index.php");
        return;
    }

    if( !isset($_POST['make']) || (strlen($_POST['make'])<1) ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php".$id);
        return;
    }
    if( !isset($_POST['model']) || strlen($_POST['model'])<1 ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php".$id);
        return;
    }
    if( !isset($_POST['year']) || strlen($_POST['year'])<1 ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php".$id);
        return;
    }
    if( !isset($_POST['mileage']) || strlen($_POST['mileage'])<1 ){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php".$id);
        return;
    }

    $_POST['make'] = htmlentities($_POST['make']);
    $_POST['model'] = htmlentities($_POST['model']);
    $_POST['year'] = htmlentities($_POST['year']);
    $_POST['mileage'] = htmlentities($_POST['mileage']);

    if(!(is_numeric($_POST['mileage']))){
        $_SESSION['error'] = "Mileage must be numeric";
        header("Location: edit.php".$id);
        return;
    }
    if (!(is_numeric($_POST['year']))){
        $_SESSION['error'] = "Year must be numeric";
        header("Location: edit.php".$id);
        return;
    }

    else{
        require_once "pdo.php";

        $stmt = $pdo->prepare('INSERT INTO autos(make, model, year, mileage) VALUES ( :mk, :ml :yr, :mi)');
        $stmt->execute(array(
            ':mk' => $_POST['make'],
            ':ml' => $_POST['model'],
            ':yr' => $_POST['year'],
            ':mi' => $_POST['mileage']));

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
    <title>John Wohlfert's Auto's Database Edit</title>
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

    <?php
    $_SESSION['autos_id'] =  htmlentities($_GET['autos_id']);
    $stmt = $pdo->prepare("SELECT * FROM autos WHERE autos_id = :id");
    $stmt->execute(array(
        ':id' => htmlentities($_GET['autos_id'])
    ));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo('<label for="mk">Make</label>
        <input type="text" name="make" id="mk" value="'.$row['make'].'"><br/>
        <label for="ml">Model</label>
        <input type="text" name="model" id="ml" value="'.$row['model'].'"><br/>
        <label for="yr">Year</label>
        <input type="text" name="year" id="yr" value="'.$row['year'].'"><br/>
        <label for="miles">Mileage</label>
        <input type="text" name="mileage" id="miles" value="'.$row['mileage'].'"><br/>');
    }

    ?>

    <input type="submit" value="Edit">
    <input type="submit" name="cancel" value="cancel">
</form>
</body>
</html>
