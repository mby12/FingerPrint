<?php
/*
 * Author: Dahir Muhammad Dahir
 * Date: 12-April-2020 5:07 AM
 * About: I will tell you later
 */

namespace fingerprint;

require_once "configs.php";

$v = $_GET['v'] ?? "2";

require_once ($v == "2"? "./src/html/home_v2.php" :"./src/html/home.html");
