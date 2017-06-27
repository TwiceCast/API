<?php

function getPostData() {
	$raw = file_get_contents('php://input');
	$type = getallheaders()["Content-Type"];
	if (strpos($type, 'application/x-www-form-urlencoded') === 0) {
		parse_str($raw, $output);
		return ($output);
	}
	else if (strpos($type, "application/json") === 0)
		return (json_decode($raw, true));
	return (json_decode($raw, true));
}

function checkTokenMe($nickname) {
	$headers = array_change_key_case(getallheaders());
	if (isset($headers["authorization"])) {
		$jwt = str_replace("Bearer ", "", $headers['authorization']);
		$authentication = new Authentication();
		$authentication->verifyJWT($jwt);
		if ($nickname == "me")
			return ($authentication->getUserFromToken()->name);
	}
	return ($nickname);
}

?>