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

		$user_id = $Database->get("id", "Users", ["email" => $email, "password" => $passwd], "id");

		if ($user_id) {

			$identifier = create_identifier();

			$Database->insert("Identifiers", ["user_id" => $user_id, "identifier" => $identifier]);

			echo json_encode([
				"status" => "success",
				"code" => 200,
				"message" => "Login Successful.",
				"data" => ["identifier" => $identifier]
			]);

			exit(0);

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
