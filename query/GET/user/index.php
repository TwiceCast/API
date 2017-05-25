<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	
	$auth = new Authentication();
	
	if (!$auth->verify())
		die ('');

	$user = $auth->getUserFromToken();

	header('Content-Type: application/json');
	if ($user)
		echo json_encode($user);
	else
	{
		http_response_code(404);
		echo
			'{
				"url": "/user/",
				"method": "GET",
				"code": 404,
				"description": "An unknown error occurred"
			}';
	}
?>
