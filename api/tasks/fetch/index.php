<?php

require("../../../core/init.php");

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

	$tasks = $Database->customQuery($query, [], True);

	echo json_encode([
		"status" => "success",
		"code" => 200,
		"message" => "All Data Has Been Retrieved.",
		"data" => $tasks,
		"metadata" => ["records" => $Database->rowCount]
	]);

}

?>