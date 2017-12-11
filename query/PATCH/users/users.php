<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

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
		
		if ($_GET['id'] != $user->id) //add moderator/admin detection here
			throw new RightsException("You cannot modify someone else's account", Response::NORIGHT);
		
		if (isset($post['email']))
			$user->changeEmail($post['email']);
		if (isset($post['password']))
			$user->changePassword($post['password']);
		if (isset($post['name']))
			$user->changeName($post['name']);
		if (isset($post['language']))
			$user->changeLanguage($post['language']['code']);
		if (isset($post['gender']))
			$user->changeGender($post['gender']);
		if (isset($post['birthdate']))
			$user->changeBirthdate($post['birhtdate']);
		if (isset($post['biography']))
			$user->changeBiography($post['biography']);
		if (isset($post['github']))
			$user->changeGithub($post['github']);
		if (isset($post['linkdin']))
			$user->changeLinkdin($post['linkdin']);
		
		$response->setMessage($user);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
