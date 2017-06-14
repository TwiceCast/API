<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');

	$verify = true;
	$user = new User();
	$response = new Response(Response::OK);
	$authentication = null;

	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	if (isset(getallheaders()["authorization"])) {
		$authentication = new Authentication();
		$verify = $authentication->verifyJWT(getallheaders()["token"]);
		if (!$verify)
			$response->setMessage(["message" => "Wrong token or token expired"], Response::NOTAUTH);
	}
	if ($verify) {
		if (isset($_GET['id']))
		{
			if (!$user->getFromID($_GET['id']))
				$response->setMessage(["message" => "User not found"], Response::DOESNOTEXIST);
			else
				$response->setMessage($user);
		}
		else if (isset($_GET['nickname']))
		{
			if ((!$_GET['nickname'] == "me" || !$authentication || !($user = $authentication->getUserFromToken())) && !$user->getFromName($_GET['nickname']))
				$response->setMessage(["message" => "User not found"], Response::DOESNOTEXIST);
			else
				$response->setMessage($user);
		}
		else
		{
			$users = $user->getAllUsers();
			$response->setMessage(["users" => $users]);
		}
	}

	$response->send();
?>
