<?php

require("/var/www/ShiKhiIT/core/init.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	if (isset($_GET["from"]) && isset($_GET["N"])) {

		if (is_numeric($_GET["from"]) && is_numeric($_GET["N"])) {

			$start_index = $_GET["from"];
			$number_of_comments = $_GET["N"] - 1;

			$query = "SELECT Reviews.id, first_name, last_name, job, image_path, comment, stars, created_at FROM Reviews JOIN Users ON Reviews.user_id = Users.id WHERE type = :type AND Reviews.id BETWEEN $start_index AND $start_index + $number_of_comments ORDER BY Reviews.id";
		
		} else {

			exit(0);

		}

	} else {

		$query = "SELECT Reviews.id, first_name, last_name, job, image_path, comment, stars, created_at FROM Reviews JOIN Users ON Reviews.user_id = Users.id WHERE type = :type ORDER BY Reviews.id";

	}

	$data = $Database->customQuery($query, ["type" => "client"], True);

	if ($Database->rowCount) {

		echo json_encode([
			"status" => "success",
			"code" => 200,
			"message" => "All Data Has Been Retrieved.",
			"data" => $data,
			"metadata" => ["records" => $Database->rowCount]
		]);

	} else {

		echo json_encode([
			"status" => "success",
			"code" => 404,
			"message" => "No Data Available."
		]);

	}

} else {

	exit(0);

}


?>