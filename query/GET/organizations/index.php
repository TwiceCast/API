<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$response = new Response(Response::OK);
	try {
		$out = null;
		$organization = new Organization();

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (isset($_GET['id']))
		{
			if (!$organization->getFromID($_GET['id']))
				throw new ParametersException("This organization does not exist", Response::DOESNOTEXIST);
			$response->setMessage($organization);
		}
		else
		{
			$organizations = $organization->getAllOrganizations();
			$response->setMessage(["organizations" => $organizations]);
		}
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>