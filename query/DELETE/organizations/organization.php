<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$response = new Response();
	try {
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$authentication  = new Authentication();
		$authentication->verify();
		
		if (!isset($_GET['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);

		$user = $authentication->getUserFromToken();
		if (!$user)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		
		$organization = new Organization();
		if ($organization->getFromId($_GET['id']) === false)
			throw new NotFoundException("This organization does not exist", Response::NOTFOUND);
		
		// role 4 == Organization Founder
		if ($authentication->userHasRights(4, $organization->id) === false)
			throw new RightsException("You cannot modify someone else's organization", Response::NORIGHT);
		
		if (!$organization->delete())
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage("The organization has been removed successfully", Response::SUCCESS);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>