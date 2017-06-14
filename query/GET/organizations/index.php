<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$out = null;
	$response = new Response(Response::OK);
	$organization = new Organization();

	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	if (isset($_GET['id']))
	{
		if (!$organization->getFromID($_GET['id']))
			$response->setMessage(["error" => "This organization does not exist."], Response::DOESNOTEXIST);
		else
			$response->setMessage($organization);
	}
	else
	{
		$organizations = $organization->getAllOrganizations();
		$response->setMessage(["organizations" => $organizations]);
	}
	
	$response->send();
?>