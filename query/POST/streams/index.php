<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

	$response = new Response(Response::OK);
	try {
		throw new NotImplementedException("This feature is not implemented yet", 501);
		$postdata = getPostData();
		$authentication = new Authentication();
		$authentication->verify();
		if (!isset($postdata["title"]))
			throw new ParametersExceptions("Parameters missing to proceed", Response::MISSPARAM);
		$stream = new Stream();
		$stream->setTitle($postdata["title"]);
		$stream->setOwner($authentication->getUserFromToken());
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
	// echo "POST/STREAMS/INDEX.PHP";
	// var_dump($_GET);
	// var_dump($_SERVER);
?>