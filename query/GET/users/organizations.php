<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$response = new Response(Response::OK);
	$organization = new Organization();
	$organizations = null;

	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	if (isset($_GET['userid']))
	{
		$organizations = $organization->getFromUserID($_GET['userid']);
		if ($organizations === false)
			$response->setMessage(["error" => "Something wrong happened"], Response::UNKNOWN);
		else
			$response->setMessage(["organizations" => $organizations]);
	}
	else
		$response->setMessage(["error" => "Missing parameters to proceed"], Response::MISSPARAM);

	$response->send();
?>