<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	$response = new Response(Response::OK);
	try {
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
			throw new ParametersException("You cannot delete someone else's account", Response::NORIGHT);
		
		if (!$user->delete())
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage("The user has been removed successfully.", Response::SUCCESS);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
