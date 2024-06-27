<?php

require("/var/www/ShiKhiIT/core/init.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	if (isset($_GET["identifier"])) {

		$identifier = $_GET["identifier"];

		try {

			$stmt = $Database->prepare("SELECT first_name, last_name, username, email, job, bio, company, image_path, joined_at, verified FROM Identifiers JOIN Users ON Users.id = Identifiers.user_id JOIN Credentials ON Credentials.user_id = Users.id WHERE identifier = :identifier");

			$stmt->execute(["identifier" => $identifier]);

		} catch (PDOException $Error) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Unable To Fetch Users."
			]);

			exit(0);

		}

		$user_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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