<?php

require("/var/www/ShiKhiIT/core/init.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	try {

		if (isset($_GET["N"])) {

			if (is_numeric($_GET["N"])) {
			
				$stmt = $Database->prepare("SELECT first_name, last_name, username, image_path, position, practice_area, experience, address, phone, email, about, education, languages, linkedin FROM Employees LIMIT " . $_GET["N"]);
				$stmt->execute();
			
			} else {

				exit(0);

			}

		} elseif (isset($_GET["UN"])) {

			$stmt = $Database->prepare("SELECT first_name, last_name, username, image_path, position, practice_area, skills, experience, address, phone, email, about, education, languages, linkedin FROM Employees WHERE username = :username");
			$stmt->execute(["username" => $_GET["UN"]]);

		} else {

			$stmt = $Database->prepare("SELECT first_name, last_name, username, image_path, position, practice_area, experience, address, phone, email, about, education, languages, linkedin FROM Employees");
			$stmt->execute();

		}

	} catch (PDOException $Error) {

		echo json_encode([
			"status" => "failure",
			"code" => 500,
			"message" => "Unable To Fetch Employees."
		]);

		exit(0);

	}

	$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (count($employees) > 0) {

		echo json_encode([
			"status" => "success",
			"code" => 200,
			"message" => "All Data Has Been Retrieved.",
			"data" => $employees[0],
			"metadata" => ["records" => count($employees)]
		]);

	} else {

		echo json_encode([
			"status" => "success",
			"code" => 200,
			"message" => "No Data Available.",
			"data" => NUll
		]);

	}

}  elseif ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

	header("HTTP/1.1 204 No Content");
	exit(0);

} else {

	exit(0);

}



?>