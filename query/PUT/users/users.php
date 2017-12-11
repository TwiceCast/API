<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERBER['DOCUMENT_ROOT'].'/class/functions.php');

	$response = new Response(Response::OK);
	try {
		$post = getPostData();
		
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$authentication = new Authentication();
		$authentication->verify();

		if (!isset($_GET['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		$user = $authentication->getUserFromToken();
		
		if (!$user)
			throw new UnknownException("Something wrong happened");
		
		if ($_GET['id'] != $user->id) //Add moderator/admin detection here
			throw new RightsException("You cannot modify someone else's account", Response::NORIGHT);

		if (!isset($post['name']) or !isset($post['password'])
			or !isset($post['email']) or !isset($post['language'])
			or !isset($post['gender']) or !isset($post['birthdate'])
			or !isset($post['biography']) or !isset($post['github'])
			or !isset($post['linkdin']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);

		if ($user->checkName($post['name']))
			$user->setName($post['name']);
		if ($user->checkEmail($post['email']))
			$user->setEmail($post['email']);
		$user->setPassword($post['password']);
		$user->setLanguage($post['language']['code']);
		$user->setGender($post['gender']);
		$user->setBirthdate($post['birthdate']);
		$user->setBiography($post['biography']);
		$user->setGithub($post['github']);
		$user->setLinkdin($post['linkdin']);

		if (!$user->update())
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage($user);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
