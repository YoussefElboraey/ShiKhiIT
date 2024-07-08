<?php

require("/var/www/ShiKhiIT/core/init.php");

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

	exit(0);

} elseif ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

	header("HTTP/1.1 204 No Content");
	exit(0);

} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

	$request_data = json_decode(file_get_contents("php://input"), true);

	$identifier = $request_data["identifier"];
	$article_id = $request_data["article_id"];
	$comment = $request_data["comment"];
	$reply_to = (!empty($request_data["reply_to"])) ? $request_data["reply_to"] : Null ;

	if (!empty($identifier) && !empty($comment) && !empty($article_id)) {

		$stmt = $Database->prepare("SELECT user_id FROM Identifiers WHERE identifier = :identifier");

		$stmt->execute(["identifier" => $identifier]);

		$stmt->bindColumn("user_id", $user_id);
		$stmt->fetch(PDO::FETCH_BOUND);

		try {

			$stmt = $Database->prepare("INSERT INTO `Comments` (user_id, article_id, comment, reply_to) VALUES (:user_id, :article_id, :comment, :reply_to)");
			$stmt->execute(["user_id" => $user_id, "article_id" => $article_id, "comment" => $comment, "reply_to" => $reply_to]);

			echo json_encode([
				"status" => "success",
				"code" => 201,
				"message" => "Comment Has Been Added."
			]);

			exit(0);

		} catch (PDOException $Error) {

			echo json_encode([
				"status" => "failure",
				"code" => 500,
				"message" => "Unable To Add Comment."
			]);

			exit(0);

		}

	} else { // If There Is Anything Missed

		header("HTTP/1.1 400 Bad Request");
		exit(0);

	}
	

}

?>