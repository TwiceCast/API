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

	// For this part, we need to check all paramters like:
	//   - email: Must be valid and unique
	//   - password: Must pass minimal requierement
	//   - nickname: Must be valid and unique
	//   - country: Must exist
	//   - rank: Must exist
	
	// If there is too much SQL Query here we can stop using cangeXXX() function and use setXXX() then update()
	if ($response->getResponseType() == Response::OK)
	{
		$out = array();
		if (isset($post['email']))
		{
			if ($user->changeEmail($post['email']))
				$out['email'] = true;
			else
				$out['email'] = false;
		}
		if (isset($post['password']))
		{
			if ($user->changePassword($post['password']))
				$out['password'] = true;
			else
				$out['password'] = false;
		}
		if (isset($post['nickname']))
		{
			if ($user->changeName($post['nickname']))
				$out['nickname'] = true;
			else
				$out['nickname'] = false;
		}
		// if (isset($post['country']))
		// {
			// if ($user->changeCountry($post['country']))
				// $out[] = array('country', Err::SUCCESS);
			// else
				// $out[] = array('country', Err::UNKNOW);
		// }
		// if (isset($post['birthdate']))
		// {
			// if ($user->changeBirthdate($post['birhdate']))
				// $out[] = array('birthdate', Err::SUCCESS);
			// else
				// $out[] = array('birthdate', Err::UNKNOW);
		// }
		// if (isset($post['rank']))
		// {
			// if ($user->changeRank($post['rank']))
				// $out[] = array('rank', Err::SUCCESS);
			// else
				// $out[] = array('rank', Err::UNKNOW);
		// }
		$response->setMessage($out);
	}

	$response->send();
?>
