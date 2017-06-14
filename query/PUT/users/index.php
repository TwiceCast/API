<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	function getRealPOST()
	{
		$pairs = explode("&", file_get_contents("php://input"));
		$vars = array();
		foreach ($pairs as $pair)
		{
			$nv = explode("=", $pair);
			$name = urldecode($nv[0]);
			$value = urldecode($nv[1]);
			$vars[$name] = $value;
		}
		return $vars;
	}
	$post = getRealPOST();
	$response = new Response(Response::OK);
	$user = new User();

	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	if (isset($_GET['id']))
	{
		if (!$user->getFromID($_GET['id']))
			$response->setMessage(["error" => "This user does not exist"], Response::DOESNOTEXIST);
	}
	else if (isset($_GET['nickname']))
	{
		if (!$user->getFromNickname($_GET['nickname']))
			$response->setMessage(["error" => "This user does not exist"], Response::DOESNOTEXIST);
	}
	else
		$response->setMessage(["error" => "Missing parameters to proceed"], Response::MISSPARAM);

	if ($response->getResponseType == Response::OK)
	{
		if (isset($post['nickname']) and isset($post['password']) and isset($post['email']))
		{
			$user->setName($post['nickname']);
			$user->setEmail($post['email']);
			$user->setPassword($post['password']);
			// if (isset($post['country']))
				// $user->setCountry((int)$post['country']);
			// else
				// $user->setCountry(null);
			// if (isset($post['birthdate']))
				// $user->setBirthdate($post['birthdate']);
			// else
				// $user->setBirthdate(null);
			// if (isset($post['rank']))
				// $user->setRank((int)$post['rank']);
			// else
				// $user->setRank(null);
			if ($user->update())
				$response->setMessage(["message" => "User overwrited successfully"], Response::SUCCESS);
			else
				$response->setMessage(["error" => "Something wrong happened"], Response::UNKNOWN);
		}
		else
			$response->setMessage(["error" => "Missing parameters to proceed"], Response::MISSPARAM);
	}

	$response->send();
?>
