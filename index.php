<?php
/*
 * Author: Dahir Muhammad Dahir
 * Date: 12-April-2020 5:07 AM
 * About: I will tell you later
 */

namespace fingerprint;

$v = $_GET['v'] ?? 0;

require($v == "2"? "./src/html/home_v2.html" :"./src/html/home.html");
