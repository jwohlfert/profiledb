<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 3/13/2016
 * Time: 12:26 AM
 */
session_start();
unset($_SESSION['name']);
unset($_SESSION['user_id']);
header('Location: index.php');