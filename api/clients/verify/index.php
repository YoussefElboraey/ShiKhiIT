<?php

require "../../../core/init.php";

$identifier = $_GET["identifier"];

$user_id = $Database->get("id", "Users", ["identifier" => $identifier], "user_id");

$Database->update("Users", ["verified" => "1"], ["id" => $user_id]);

$Database->delete("Identifiers", ["identifier" => $identifier]);

header("location:/membership");
exit(0);

?>