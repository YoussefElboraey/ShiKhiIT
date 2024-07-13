<?php

require("../../../core/init.php");

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

	header("HTTP/1.1 204 No Content");
	exit(0);

} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

	$identifier = $_POST["identifier"];

	// print_r($_POST);
	// exit;

	$user_id = $Database->get("user_id", "Identifiers", ["identifier" => $identifier], "user_id");

	if (!$user_id) {

		echo json_encode([
			"status" => "failure",
			"code" => 400,
			"message" => "Identifier Not Valied."
		]);

		exit(0);

	}

	if (isset($_FILES["picture"])) {

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

			$Database->update("Users", ["image_path" => "profile_pictures/$image_random_name.webp"], ["id" => $user_id]);

		}

	}

	if (count($_POST) > 1) {

		$allowed_fields = ["first_name", "last_name", "username", "job", "bio", "company", "password"];

		if (isset($_POST["password"])) $_POST["password"] = md5($_POST["password"]);

		unset($_POST["identifier"]);

		$Database->update("Users", array_filter($_POST), ["id" => $user_id]);

	}

	echo json_encode([
		"status" => "success",
		"code" => 201,
		"message" => "Update Successful."
	]);
	

}

?>
