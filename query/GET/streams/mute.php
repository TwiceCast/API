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
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);

		$authentication = new Authentication();
		$authentication->verify();
		$user = $authentication->getUserFromToken();

		if (!$user)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);

		if (!isset($_GET['id']))
			throw new NotFoundException("Missing parameters to proceed", Response::MISSPARAM);

		$stream = new Stream();
		if (!$stream->getFromId($_GET['id']))
			throw new NotFoundException("Stream not found", Response::NOTFOUND);

		if (!isset($_GET['userid']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);

		$user = new User();
		if (!$user->getFromId($_GET['userid']))
			throw new ParametersException("User not found", Response::NOTFOUND);
		
		$ret = $stream->getMute($_GET['userid']);
		if (!$ret)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage($ret);
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