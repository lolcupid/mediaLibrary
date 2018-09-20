<?php

$host = 'localhost';
$dbname = 'mediaLibrary';
$user = 'root';
$password = '123456';
$dsn = "mysql:host=" . $host . ";dbname=" . $dbname;

try {
  $db = new PDO($dsn, $user, $password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo $e->getMessage();
}

?>