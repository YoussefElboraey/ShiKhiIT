<?php

require("/var/www/ShiKhiIT/core/init.php");

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

	header("HTTP/1.1 204 No Content");
	exit(0);

} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

	$identifier = $_POST["identifier"];

	$stmt = $Database->prepare("SELECT user_id FROM Identifiers WHERE identifier = :identifier");

	$stmt->execute(["identifier" => $identifier]);

	$stmt->bindColumn("user_id", $user_id);
	$stmt->fetch(PDO::FETCH_BOUND);

	if (!$user_id) {

		echo json_encode([
			"status" => "failure",
			"code" => 400,
			"message" => "Identifier Not Valied."
		]);

		exit(0);

	}

	if ($_FILES["picture"]["size"] > 0) {

		if ($_FILES["picture"]["error"] !== 0) {

			echo json_encode([
				"status" => "failure",
				"code" => 400,
				"message" => "Please Choose Another Picture"
			]);

			exit(0);

		}

		$image_extension = strtolower(@end(explode(".", $_FILES["picture"]["name"])));
		$allowed_image_extensions = ["png", "jpg", "jpeg"];

		if (!in_array($image_extension, $allowed_image_extensions)) {

			echo json_encode([
				"status" => "failure",
				"code" => 400,
				"message" => "Image Extension Not Supported"
			]);

			exit(0);

		}

		$image_type = getimagesize($_FILES["picture"]["tmp_name"])[2];

		switch ($image_type) {

			case IMAGETYPE_JPEG:

				$image = imagecreatefromjpeg($_FILES["picture"]["tmp_name"]);

				break;

			case IMAGETYPE_PNG:

				$image = imagecreatefrompng($_FILES["picture"]["tmp_name"]);

				break;
			
			default:

				echo json_encode([
					"status" => "failure",
					"code" => 400,
					"message" => "Image Extension Not Supported"
				]);

				exit(0);

		}

		$image_random_name = bin2hex(random_bytes(8));

		imagewebp($image, "/var/www/ShiKhiIT/profile_pictures/$image_random_name.webp", 30);

		imagedestroy($image);

		try {

			$stmt = $Database->prepare("UPDATE `Users` SET image_path = 'profile_pictures/$image_random_name.webp' WHERE id = $user_id");
			$stmt->execute();

		} catch (PDOException $Error) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Unable To Update Profile Picture."
			]);

			exit(0);

		}
		

	}

	if (count($_POST) > 1) {

		$allowed_fields = ["first_name", "last_name", "username", "job", "bio", "company", "password"];

		foreach ($_POST as $column => $value) {

			if ($column === "identifier" || empty($value)) continue;

			if (!in_array($column, $allowed_fields)) exit(0);

			if ($column === "email" || $column === "password") {

				$table = "Credentials";
				$condetion_column = "user_id";

				if ($column === "password") $value = md5($value);

			} else {

				$table = "Users";
				$condetion_column = "id";

			}

			try {

				$stmt = $Database->prepare("UPDATE $table SET $column = :value WHERE $condetion_column = $user_id");
				$stmt->execute(["value" => $value]);

			} catch (PDOException $Error) {

				echo json_encode([
					"status" => "failure",
					"code" => 500,
					"message" => "Unable To Update User Data."
				]);

				exit(0);

			}

		}

	}

	echo json_encode([
		"status" => "success",
		"code" => 201,
		"message" => "Update Successful."
	]);
	

}

?>
