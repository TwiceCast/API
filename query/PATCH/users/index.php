<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
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

	$response = new Response(Response::OK);
	try {
		$post = getRealPOST();
		$user = new User();

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (($id = (isset($_GET['id']) ? 'id' : (isset($_GET['nickname']) ? 'nickname' : false))) === false)
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		$user = ($id == "id" ? $user->getFromID($_GET[$id]) : $user->getFromNickname($_GET[$id]));
		if (!$user)
			throw new ParametersException("This user does not exist", Response::DOESNOTEXIST);

		// For this part, we need to check all paramters like:
		//   - email: Must be valid and unique
		//   - password: Must pass minimal requierement
		//   - nickname: Must be valid and unique
		//   - country: Must exist
		//   - rank: Must exist
		
		// If there is too much SQL Query here we can stop using cangeXXX() function and use setXXX() then update()
		$out = array();
		if (isset($post['email']))
			$out['email'] = $user->changeEmail($post['email']);
		if (isset($post['password']))
			$out['password'] = $user->changePassword($post['password']);
		if (isset($post['nickname']))
			$out['nickname'] = $user->changeName($post['nickname']);
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
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
