<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

	$response = new Response(Response::OK);
	try
	{
		$postdata = getPostData();
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);

		$authentication = new Authentication();
		$authentication->verify();
		$user = $authentication->getUserFromToken();

		if (!$user)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);

		if (!isset($_GET['id']))
			throw new NotFuondException("Missing parameters to proceed", Reponse::MISSPARAM);

		$stream = new Stream();
		if (!$stream->getFromId($_GET['id']))
			throw new NotFoundException("Stream not found", Response::NOTFOUND);

		if (!isset($postdata['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);

		$user = new User();
		if (!$user->getFromId($postdata['id']))
			throw new ParametersException("User not found", Response::NOTFOUND);

		if ($authentication->userHasRights(array(8, 9, 10), $stream->id, "stream") === false)
			throw new RightsException("You don't have enough rights to modify this stream", Response::NORIGHT);

		if (isset($postdata['duration']))
			$ret = $stream->bannUser($postdata['id'], $postdata['duration']);
		else
			$ret = $stream->bannUser($postdata['id']);
		if (!$ret)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage("", 204);
	}
	catch (CustomException $e)
	{
		$response->setError($e);
	}
	finally
	{
		$response->send();
	}
?>