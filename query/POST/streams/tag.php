<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Tag.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

	$response = new Response(Response::OK);
	try {
		$postdata = getPostData();
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);

		$authentication = new Authentication();
		$authentication->verify();
		$user = $authentication->getUserFromToken();

		if (!$user)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);

		if (!isset($_GET['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);

		$stream = new Stream();
		if (!$stream->getFromId($_GET['id']))
			throw new NotFoundException("Stream not found", Response::NOTFOUND);

		if (!isset($postdata['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);

		$tag = new Tag();
		if (!$tag->getFromId($postdata['id']))
			throw new ParametersException("Tag not found", Response::NOTFOUND);

		if ($authentication->userHasRights(8, $stream->id, "stream") === false)
			throw new RightsException("You don't have enough rights to modify this stream", Response::NORIGHT);

		if (!$stream->addTagToDB($postdata['id']))
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage($tag);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>