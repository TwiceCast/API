<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	$auth = new Authentication();
	$out = null;
	$response = new Response(Response::OK);

	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	$verify = $auth->verify();
	if ($verify !== true)
		$response->setMessage($verify, Reponse::NOTAUTH);
	else {
		$user = new User();

		if (isset($_GET['id']))
		{
			if (!$auth->isUserID($_GET['id']))
				$response->setMessage(["error" => 'You can not delete someone else\'s account'], Response::NORIGHT);
			if ($user->getFromID($_GET['id']))
			{
				if ($user->delete())
					$response->setMessage(["message" : "User deleted successfully"], Response::SUCCESS);
				else
					$response->setMessage(["error" : "Something wrong happened"], Response::UNKNOWN);
			}
			else
				$response->setMessage(["error" : "This user does not exist"], Response::DOESNOTEXIST);
		}
		else if (isset($_GET['nickname']))
		{
			if (!$auth->isUserName($_GET['nickname']))
				die ('You can not delete someone else\'s account');
			if ($user->getFromNickname($_GET['nickname']))
			{
				if ($user->delete())
					$response->setMessage(["message" : "User deleted successfully"], Response::SUCCESS);
				else
					$response->setMessage(["error" : "Something wrong happened"], Response::UNKNOWN);
			}
			else
				$response->setMessage(["error" : "This user does not exist"], Response::DOESNOTEXIST);
		}
		else
			$response->setMessage(["error" : "Missing parameters to proceed"], Response::MISSPARAM);
	}
	$response.send();
?>
