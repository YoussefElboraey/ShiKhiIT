<?php

require("/var/www/ShiKhiIT/core/init.php");

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	header("location:/signup");

} elseif ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

	header("HTTP/1.1 204 No Content");
	exit(0);

} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

	$request_data = json_decode(file_get_contents("php://input"), true);

	if (!empty($request_data["first_name"]) && !empty($request_data["last_name"]) && !empty($request_data["email"]) && !empty($request_data["password"])) {

		require "/var/www/ShiKhiIT/core/functions/identifiers.php"; // identifiers Creator.

		$first_name = $request_data["first_name"];
		$last_name = $request_data["last_name"];
		$email = $request_data["email"];
		$passwd = md5($request_data["password"]);
		$identifier = create_identifier();

		try {

			$stmt = $Database->prepare("SELECT email FROM `Credentials` WHERE email = :email");
			$stmt->execute(["email" => $email]);

			if (count($stmt->fetchAll(PDO::FETCH_ASSOC)) > 0) {

				echo json_encode([
					"status" => "failure",
					"code" => 409,
					"message" => "User Already Exists."
				]);

				exit;
			}

			$stmt = $Database->prepare("INSERT INTO `Users` (first_name, last_name) VALUES (:first_name , :last_name)");
			$stmt->execute(["first_name" => $first_name, "last_name" => $last_name]);

			$user_id = $Database->lastInsertId();

			$stmt = $Database->prepare("INSERT INTO `Credentials` (user_id, email, password) VALUES (:user_id , :email , :passwd)");
			$stmt->execute(["user_id" => $user_id, "email" => $email, "passwd" => $passwd]);

			$stmt = $Database->prepare("INSERT INTO `Identifiers` (user_id, identifier) VALUES (:user_id , :identifier)");
			$stmt->execute(["user_id" => $user_id, "identifier" => $identifier]);

			echo json_encode([
				"status" => "success",
				"code" => 201,
				"message" => "User Created Successfully."
			]);

			require "../../../core/mailer.php";

			exit(0);

		} catch (PDOException $Error) {

			$Database->exec("DELETE FROM `Users` WHERE id = $user_id");

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Unable To Add User."
			]);

			exit(0);

		}

	}

	echo json_encode([
		"status" => "failure",
		"code" => 400,
		"message" => "All Fields Are Required."
	]);

	exit(0);

}

?>
