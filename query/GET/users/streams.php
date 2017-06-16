<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');

	$response = new Response(Response::OK);
	try {
		$stream = new Stream();
		$streams = null;

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (($id = (isset($_GET['userid']) ? 'userid' : (isset($_GET['usernickname']) ? 'usernickname' : false))) === false)
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		$streams = ($id == "userid" ? $streams->getFromUserID($_GET[$id]) : $streams->getFromUserNickname($_GET[$id]));
		if (!$streams)
			throw new ParametersException("This user does not exist", Response::DOESNOTEXIST);
		$response->setMessage(["streams" => $streams]);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response.send();
	}
?>
