<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	$response = new Response(Response::OK);
	try {
		$post = json_decode(file_get_contents('php://input'));
		
		$user = new User();

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$authentication = new Authentication();
		$authentication->verify();

		if (!isset($_GET['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		$user = $authentication->getUserFromToken();
		
		if (!$user)
			throw new UnknownException("Something wrong happened");
		
		if ($_GET['id'] != $user->ID) //Add moderator/admin detection here
			throw new ParametersException("You cannot modify someone else's account", Response::NORIGHT);

		if (!isset($post->name) or !isset($post->password)
			or !isset($post->email) or !isset($post->language))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);

		$user->setName($post->name);
		$user->setEmail($post->email);
		$user->setPassword($post->password);
		$user->setLanguage($post->language->code);

		if (!$user->update())
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage(["message" => "User overwrited successfully"], Response::SUCCESS);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
