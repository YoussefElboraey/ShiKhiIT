<?php

require("/var/www/ShiKhiIT/core/init.php");

$identifier = $_GET["identifier"];

$stmt = $Database->prepare("UPDATE Users SET verified = true WHERE id = (SELECT user_id FROM Identifiers WHERE identifier = :identifier)");
$stmt->execute(["identifier" => $identifier]);

$stmt = $Database->prepare("DELETE FROM Identifiers WHERE identifier = :identifier");
$stmt->execute(["identifier" => $identifier]);

header("location:/membership");
exit(0);

?>