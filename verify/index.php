<?php

$url = $_SERVER["SERVER_NAME"] . "/api/clients/verify/?identifier=" . $_GET["identifier"];

$request = curl_init();

curl_setopt($request, CURLOPT_URL, $url);

curl_exec($request);

header("location:/login");

?>
