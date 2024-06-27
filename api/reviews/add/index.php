<?php

require("/var/www/ShiKhiIT/core/init.php");

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

		$stmt = $Database->prepare("SELECT type FROM `Users` WHERE id = (SELECT user_id FROM Identifiers WHERE identifier = :identifier) AND type = 'client'");
		$stmt->execute(["identifier" => $identifier]);

		if (count($stmt->fetchAll(PDO::FETCH_ASSOC)) !== 1) {

			echo json_encode([
				"status" => "failure",
				"code" => 409,
				"message" => "Not A Client."
			]);

			exit(0);

		}

		try {

			$stmt = $Database->prepare("INSERT INTO `Reviews` (user_id, comment, stars) VALUES ((SELECT user_id FROM Identifiers WHERE identifier = :identifier), :comment, :stars)");
			$stmt->execute(["identifier" => $identifier, "comment" => $comment, "stars" => $stars]);

			echo json_encode([
				"status" => "success",
				"code" => 201,
				"message" => "Review Has Been Added."
			]);

			exit(0);

		} catch (PDOException $Error) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Unable To Add Review."
			]);

			exit(0);

		}

	} else { // If There Is Anything Missed

		header("HTTP/1.1 400 Bad Request");
		exit(0);

	}
	

}


?>