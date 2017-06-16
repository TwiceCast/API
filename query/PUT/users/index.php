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
		if (!($id == "id" ? $user->getFromID($_GET[$id]) : $user->getFromNickname($_GET[$id]));
			throw new ParametersException("This user does not exist", Response::DOESNOTEXIST);
		if (!isset($post['nickname']) or !isset($post['password']) or !isset($post['email']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
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
		if (!$user->update())
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage(["message" => "User overwrited successfully"], Response::SUCCESS);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
