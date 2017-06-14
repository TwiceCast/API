<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$out = null;
	$response = new Response();
	$organization = new Organization();

	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	if (isset($_GET['id']))
	{
		if ($organization->getFromID($_GET['id']))
		{
			if ($organization->delete())
				$response->setMessage(["message" => "Organization deleted successfully"], Response::SUCCESS);
			else
				$response->setMessage(["error" => "Something wrong happened"], Response::UNKNOWN);
		}
		else
			$response->setMessage(["error" => "This organization does not exist"], Response::DOESNOTEXIST);
	}
	else
		$response->setMessage(["error" => "Missing parameters to proceed"], Response::MISSPARAM);

	$response->send();
?>