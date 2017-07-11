<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$response = new Response(Response::OK);
	try {
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);

		if (!isset($_GET['id']))
			throw ParametersException("Missing parameters to proceed", Response::MISSPARAM);

		$organization = new Organization();
		if ($organization->getFromId($_GET['id']) === false)
			throw new NotFoundException("This organization does not exist", Response::NOTFOUND);
	
		if ($organization->private)
		{
			$authentication = new Authentication();
			$authentication->verify();
			// 4 - Organization Founder
			// 5 - Organization Moderator
			// 6 - Organization Streamer
			// 7 - Organization Guest
			// 4, 5, 6, 7 == Someone in the Organization
			if ($authentication->userHasRights(array(4, 5, 6, 7), $organization->id) === false)
				throw new RightsException("You cannot access private organization", Response::NORIGHT);
		}

		$members = $organization->getMembers();
		
		$rep = new stdClass();
		if ($members === false)
		{
			$rep->member_list = null;
			$rep->member_total = 0;
		}
		else
		{
			$rep->member_list = $members;
			$rep->member_total = count($members);
		}
		$response->setMessage($rep);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
	// echo "GETORGANIZATIONMEMBERS.PHP";
	// var_dump($_GET);
?>