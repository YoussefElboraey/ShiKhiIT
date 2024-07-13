<?php

require("/var/www/ShiKhiIT/core/init.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	if (isset($_GET["N"])) {

		if (is_numeric($_GET["N"])) {
		
			$employees = $Database->customQuery("first_name, last_name, username, image_path, position, practice_area, experience, address, phone, email, about, education, languages, linkedin FROM Employees LIMIT " . $_GET["N"]);
		
		} else {

			exit(0);

		}

	} elseif (isset($_GET["UN"])) {

		$employees = $Database->get("first_name, last_name, username, image_path, position, practice_area, skills, experience, address, phone, email, about, education, languages, linkedin", "Employees", ["username" => $_GET["UN"]]);

	} else {

		$employees = $Database->get("first_name, last_name, username, image_path, position, practice_area, experience, address, phone, email, about, education, languages, linkedin", "Employees");

	}

	if (isset($_GET["UN"]) && $Database->rowCount === 1) $employees = $employees[0];

	if ($Database->rowCount) {

		echo json_encode([
			"status" => "success",
			"code" => 200,
			"message" => "All Data Has Been Retrieved.",
			"data" => $employees,
			"metadata" => ["records" => $Database->rowCount]
		]);

	} else {

		echo json_encode([
			"status" => "success",
			"code" => 404,
			"message" => "No Data Available."
		]);

	}

}  elseif ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

	header("HTTP/1.1 204 No Content");
	exit(0);

} else {

	exit(0);

}



?>
