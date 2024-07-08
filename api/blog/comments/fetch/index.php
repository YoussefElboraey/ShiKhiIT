<?php

require("/var/www/ShiKhiIT/core/init.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	if (isset($_GET["article_id"])) {

		if (is_numeric($_GET["article_id"])) $article_id = $_GET["article_id"]; else exit(0);

	} else {

		exit(0);

	}

	$default_number_of_comments = 3;

	if (isset($_GET["from"])) {

		if (is_numeric($_GET["from"])) {

			$start_index = $_GET["from"];
			$number_of_comments = (isset($_GET["N"]) && is_numeric($_GET["N"])) ? $_GET["N"] - 1 : $default_number_of_comments;

			$query = "SELECT first_name, last_name, image_path, Comments.id AS comment_id, Comments.comment AS comment, Comments.wrote_at AS wrote_at FROM Users JOIN Comments ON Users.id = Comments.user_id WHERE article_id = $article_id AND reply_to IS NULL AND Comments.id >= $start_index ORDER BY Comments.id LIMIT $number_of_comments";
		
		} else {

			exit(0);

		}

	} else {

		$query = "SELECT first_name, last_name, image_path, Comments.id AS comment_id, Comments.comment AS comment, Comments.wrote_at AS wrote_at FROM Users JOIN Comments ON Users.id = Comments.user_id WHERE article_id = $article_id AND reply_to IS NULL LIMIT $default_number_of_comments";

	}

	try {

	$data = $Database->query($query)->fetchAll(PDO::FETCH_ASSOC);	

	} catch (PDOException $Error) {

		echo json_encode([
			"status" => "failure",
			"code" => 500,
			"message" => "Unable To Fetch Comments."
		]);

		exit(0);

	}

	if (count($data) > 0) {

		$stmt = $Database->prepare("SELECT first_name, last_name, image_path, Comments.id AS comment_id, Comments.comment AS comment, Comments.wrote_at AS wrote_at FROM Users JOIN Comments ON Users.id = Comments.user_id WHERE reply_to = :comment_id");

		for ($comment_index=0; $comment_index < count($data); $comment_index++) { 

			$stmt->execute(["comment_id" => $data[$comment_index]["comment_id"]]);

			$replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$data[$comment_index]["replies"] = (count($replies) > 0) ? $replies : Null ;

		}

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