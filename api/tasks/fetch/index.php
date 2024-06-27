<?php

require("/var/www/ShiKhiIT/core/init.php");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

	header("HTTP/1.1 204 No Content");
	exit(0);

} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {

	if (isset($_GET["N"])) {

		if (is_numeric($_GET["N"])) {
		
			$query = "SELECT id, client, category, date(start_date) AS start_date, date(end_date) AS end_date FROM Tasks LIMIT " . $_GET["N"];
		
		} else {

			exit(0);

		}

	} elseif (isset($_GET["id"])) {

		if (is_numeric($_GET["id"])) {
		
			$query = "SELECT id, client, category, date(start_date) AS start_date, date(end_date) AS end_date FROM Tasks WHERE id = " . $_GET["id"];
		
		} else {

			exit(0);

		}

	} else {

		$query = "SELECT id, client, category, date(start_date) AS start_date, date(end_date) AS end_date FROM Tasks";

	}

	try {

		$stmt = $Database->prepare($query);
		$stmt->execute();

		$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

		echo json_encode([
			"status" => "success",
			"code" => 200,
			"message" => "All Data Has Been Retrieved.",
			"data" => $tasks,
			"metadata" => ["records" => count($tasks)]
		]);

	} catch (PDOException $Error) {

		echo json_encode([
			"status" => "failure",
			"code" => 500,
			"message" => "Unable To Fetch Tasks."
		]);

		exit(0);

	}

}

?>