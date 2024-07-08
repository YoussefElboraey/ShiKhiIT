<?php

function create_identifier() {

	return bin2hex(random_bytes(32));

}

?>