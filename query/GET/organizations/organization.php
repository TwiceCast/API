<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');

	$response = new Response(Response::OK);
	try {
		$organization = new Organization();
		$authentication = null;

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$authentication = new Authentication();
		$authentication->verify(false);
		
		if (isset($_GET['id']))
		{
			if (!$organization->getFromId($_GET['id']))
				throw new ParametersException("This organization does not exist", Response::DOESNOTEXIST);
			$response->setMessage($organization);
		}
		else
		{
			if (isset($_GET['limit']))
			{
				if (isset($_GET['start']))
					$organizations = $organization->getAllOrganizations($_GET['limit'], $_GET['start']);
				else
					$organizations = $organization->getAllOrganizations($_GET['limit']);
			}
			else
				$organizations = $organization->getAllOrganizations();

			$rep = new stdClass();
			if ($organizations === false)
			{
				$rep->organization_list = null;
				$rep->organization_total = 0;
			}
			else
			{
				$rep->organization_list = $organizations;
				$rep->organization_total = count($organizations);
			}
			$response->setMessage($rep);
		}
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>