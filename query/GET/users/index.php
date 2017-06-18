<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

	$response = new Response(Response::OK);
	try {
		$user = new User();
		$authentication = null;

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$_GET["nickname"] = checkTokenMe($_GET["nickname"]);
		if (($id = (isset($_GET['id']) ? 'id' : (isset($_GET['nickname']) ? 'nickname' : false))) !== false)
		{
			if (!($id == "id" ? $user->getFromID($_GET["id"]) : $user->getFromName($_GET["nickname"])))
				throw new ParametersException("This user does not exist", Response::DOESNOTEXIST);
			$response->setMessage($user);
		}
		else
		{
			$users = $user->getAllUsers();
			$rep = new stdClass();
			$rep->user_list = $users;
			$rep->user_total = count($users);
			$response->setMessage($rep);
		}
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
