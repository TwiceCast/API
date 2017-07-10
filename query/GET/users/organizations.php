<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');

	$response = new Response(Response::OK);
	try {
		$organization = new Organization();
		$authentication = null;
		
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$authentication = new Authentication();
		$authentication->verify(false);
		
		$organizations = null;

		if (!isset($_GET['userid']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		// Possibility to check if userid exist.
		// Using an unexisting userid will show an empty set as response.
		if (isset($_GET['limit']))
		{
			if (isset($_GET['start']))
				$organizations = $organization->getFromUserId($_GET['userid'], $_GET['limit'], $_GET['start']);
			else
				$organizations = $organization->getFromUserId($_GET['userid'], $_GET['limit']);
		}
		else
			$organizations = $organization->getFromUserId($_GET['userid']);
		
		if ($organizations === false)
		{
			$rep->organization_list = null;
			$rep->user_total = 0;
		}
		else
		{
			$rep->organization_list = $organizations;
			$rep->organization_total = count($organizations);
		}
		$response->setMessage($rep);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>