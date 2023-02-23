<?php
$servername = "172.16.1.9\Production";
$username = "sa";
$password = "web@ccess.1";
$database = "ERP_Production";
$pdo = new PDO(
    "sqlsrv:server=$servername;Database=$database",
    $username,
    $password,
    array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    )
);
