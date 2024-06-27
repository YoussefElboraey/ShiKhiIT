<?php

$host	  = "127.0.0.1"; // "gym.c1.is";
$userName = "ShiKhiIT"; // "gymcis12_Gym";
$password = "ShiKhiIT"; // "3mar-Elbora3ey";
$DBName   = "ShiKhiIT"; // "gymcis12_Gym";

try {

	$Database = new PDO("mysql:host=$host;dbname=$DBName" , $userName , $password);

} catch (PDOException $connetionError) {

	echo json_encode([
		"status" => "failure",
		"code" => 500,
		"message" => "This API Unable To Connect With Database."
	]);

	exit(0);

}

?>