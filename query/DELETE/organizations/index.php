<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$response = new Response();
	try {
		$out = null;
		$organization = new Organization();

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (!isset($_GET['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		if ($organization->getFromID($_GET['id']))
		{
			if ($organization->delete())
				$response->setMessage(["message" => "Organization deleted successfully"], Response::SUCCESS);
			else
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		}
		else
			throw new ParametersException("This organization does not exist", Response::DOESNOTEXIST);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>