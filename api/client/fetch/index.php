<?php

require("../../../core/init.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	if (isset($_GET["identifier"])) {

		$identifier = $_GET["identifier"];

		$user_id = $Database->get("user_id", "Identifiers", ["identifier" => $identifier], "user_id");

		$user_data = $Database->get("first_name, last_name, username, email, job, bio, company, image_path, joined_at, verified", "Users", ["id" => $user_id]);

		if (count($user_data) > 0) {

			echo json_encode([
				"status" => "success",
				"code" => 200,
				"message" => "All Data Has Been Retrieved.",
				"data" => $user_data[0]
			]);

			exit(0);

		}

	}

	// header("HTTP/1.1 404 Not Found");

	echo json_encode([
		"status" => "failure",
		"code" => 404,
		"message" => "No Data Available.",
		"data" => NUll
		]);

} elseif ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

	header("HTTP/1.1 204 No Content");
	exit(0);

}

?>