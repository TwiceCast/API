<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');

	$response = new Response(Response::OK);
	try {
		$user = new User();
		$authentication = null;

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$headers = array_change_key_case(getallheaders());
		if (isset($headers["authorization"])) {
			$jwt = str_replace("Bearer ", "", $headers['authorization']);
			$authentication = new Authentication();
			$authentication->verifyJWT($jwt);
		}
		if (($id = (isset($_GET['id']) ? 'id' : (isset($_GET['nickname']) ? 'nickname' : false))) !== false)
		{
			if (!($id == "id" ? $user->getFromID($_GET["id"]) : ($authentication && $_GET["nickname"] == "me" ? ($user = $authentication->getUserFromToken()) : $user->getFromName($_GET["nickname"]))))
				throw new ParametersException("This user does not exist", Response::DOESNOTEXIST);
			$response->setMessage($user);
		}
		else
		{
			$users = $user->getAllUsers();
			$response->setMessage(["users" => $users]);
		}
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
