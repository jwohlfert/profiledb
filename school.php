<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 4/7/2016
 * Time: 5:09 PM
 */
$stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
$stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));

$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $retval[] = $row['name'];
}

echo(json_encode($retval));