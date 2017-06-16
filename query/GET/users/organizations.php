<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$response = new Response(Response::OK);
	try {
		$organization = new Organization();
		$organizations = null;

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (!isset($_GET['userid']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		$organizations = $organization->getFromUserID($_GET['userid']);
		if ($organizations === false)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage(["organizations" => $organizations]);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response.send();
	}
?>