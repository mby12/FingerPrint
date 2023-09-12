<?php

$ENV = parse_ini_file(".env");
putenv("FP_CLIENT_SERVICE_HOST=" . $ENV['FP_CLIENT_SERVICE_HOST']);
