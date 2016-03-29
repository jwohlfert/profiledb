<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=si664', 'john2', 'php123');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);