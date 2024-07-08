<?php

require("/var/www/ShiKhiIT/core/init.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	if (isset($_GET["from"])) {

		$default_number_of_articles = 3;

		if (is_numeric($_GET["from"])) {

			$start_index = $_GET["from"];
			$number_of_articles = (isset($_GET["N"]) && is_numeric($_GET["N"])) ? $_GET["N"] - 1 : $default_number_of_articles;

			$query = "SELECT * FROM Articles WHERE id >= $start_index ORDER BY id LIMIT $number_of_articles";
		
		} else {

			exit(0);

		}

	} elseif (isset($_GET["tag"])) {

		$query = "SELECT * FROM Articles WHERE tags LIKE '%" . $_GET["tag"] . "%'";

	} else {

		$query = "SELECT * FROM Articles LIMIT 5";

	}

	try {

		$data = $Database->query($query)->fetchAll(PDO::FETCH_ASSOC);	

	} catch (PDOException $Error) {

		echo json_encode([
			"status" => "failure",
			"code" => 500,
			"message" => "Unable To Fetch Articles."
		]);

		exit(0);

	}

	if (count($data) > 0) {

		echo json_encode([
			"status" => "success",
			"code" => 200,
			"message" => "All Data Has Been Retrieved.",
			"data" => $data,
			"metadata" => ["records" => count($data)]
		]);

	} else {

		echo json_encode([
			"status" => "success",
			"code" => 404,
			"message" => "No Data Available.",
			"data" => NUll
		]);

	}

} else {

	exit(0);

}

?>