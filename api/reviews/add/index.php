<?php

require("../../../core/init.php");

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	exit(0);

} elseif ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

	header("HTTP/1.1 204 No Content");
	exit(0);

} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

	$request_data = json_decode(file_get_contents("php://input"), true);

	$identifier = $request_data["identifier"];
	$comment = $request_data["comment"];
	$stars = $request_data["stars"];

	if (!empty($identifier) && !empty($comment) && !empty($stars)) {

		$user_id = $Database->get("user_id", "Identifiers", ["identifier" => $identifier], "user_id");

		$user_type = $Database->get("type", "Users", ["id" => $user_id, "type" => "client"], "type");

		if (!$Database->rowCount) {

			echo json_encode([
				"status" => "failure",
				"code" => 409,
				"message" => "Not A Client."
			]);

			exit(0);

		}

		$Database->insert("Reviews", ["user_id" => $user_id, "comment" => $comment, "stars" => $stars]);

		echo json_encode([
			"status" => "success",
			"code" => 201,
			"message" => "Review Has Been Added."
		]);

		exit(0);

	} else { // If Anything Missed

		header("HTTP/1.1 400 Bad Request");
		exit(0);

	}
	

}

?>