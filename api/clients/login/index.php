<?php

require("/var/www/ShiKhiIT/core/init.php");

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	header("location:/login");

} elseif ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

	header("HTTP/1.1 204 No Content");
	exit(0);

} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

	$request_data = json_decode(file_get_contents("php://input"), true);

	if (!empty($request_data["email"]) && !empty($request_data["password"])) {

		require "/var/www/ShiKhiIT/core/functions/identifiers.php"; // identifiers Creator.

		$email = $request_data["email"];
		$passwd = md5($request_data["password"]);

		$stmt = $Database->prepare("SELECT user_id, email, password FROM `Credentials` WHERE email = :email AND password = :passwd");

		$stmt->execute(["email" => $email, "passwd" => $passwd]);

		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($data) > 0) {

			$identifier = create_identifier();

			try {

				$stmt = $Database->prepare("INSERT INTO `Identifiers` (user_id, identifier) VALUES (:user_id , :identifier)");
				$stmt->execute(["user_id" => $data[0]["user_id"], "identifier" => $identifier]);

				echo json_encode([
				"status" => "success",
				"code" => 200,
				"message" => "Login Successful.",
				"data" => ["identifier" => $identifier]
			]);

			exit(0);

			} catch (PDOException $Error) {

				echo json_encode([
					"status" => "failure",
					"code" => 500,
					"message" => "Unable To Confirm User Credentials."
				]);

				exit;
			}

		}

	}

	echo json_encode([
		"status" => "failure",
		"code" => 401,
		"message" => "Wrong email Or Password."
	]);

	exit(0);

}

?>
