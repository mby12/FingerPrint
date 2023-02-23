<?php
$servername = "172.16.1.2";
$username = "poslapas";
$password = "poslapas.1@#";
$port = '3306';

try {
  $conn = new PDO("mysql:host=$servername;port=$port", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
