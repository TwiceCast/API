<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	$response = new Response(Response::OK);
	try
	{
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);

		$stream = new Stream();
		$user = new User();
		$authentication = new Authentication();
		
		$authentication->verify(false);
		if (!isset($_GET['id']) or !isset($_GET['uid']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		if (!$stream->getFromID($_GET['id']))
			throw new NotFoundException("This stream does not exist", Response::NOTFOUND);
		if (!$user->getFromId($_GET['uid']))
			throw new NotFoundException("This user does not exist", Response::NOTFOUND);
		$rightsAuth = new Authentication(true, null, null, $user);
		$response->setMessage($rightsAuth->getUserRights($stream->id, "stream"));
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