<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// require "/var/www/ShiKhiIT/core/connect.php"; // Database Connector.
require "core/classes/cursor.php";

$Database = new Cursor("127.0.0.1", "ShiKhiIT", "ShiKhiIT", "ShiKhiIT");

?>