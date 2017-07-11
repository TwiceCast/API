<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');
	
	$response = new Response(Response::OK);
	try {
		$postdata = getPostData();
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		
		$authentication = new Authentication();
		$authentication->verify();
		$user = $authentication->getUserFromToken();
		
		if (!$user)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		
		if (!isset($_GET['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		
		$organization = new Organization;
		if (!$organization->getFromId($_GET['id']))
			throw new NotFoundException("Organization not found", Response::NOTFOUND);
		
		if (!isset($postdata['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		
		$user = new User;
		if (!$user->getFromId($postdata['id']))
			throw new ParametersException("User not found", Response::NOTFOUND);
		
		// role 4 == Organization Founder
		// role 5 == Organization Moderator
		// 4,5 ==> Members with some decisional power
		if ($authentication->userHasRights(array(4, 5), $organization->id) === false)
			throw new RightsException("You don't have enough rights to modify this organization", Response::NORIGHT);
		
		// role 7 == Organization Guest
		if (!$organization->addUserRole(7, $postdata['id']))
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage($user);
	} catch (CustomException $e){
		$response->setError($e);
	} finally {
		$response->send();
	}
?>