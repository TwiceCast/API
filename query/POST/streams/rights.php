<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

	$response = new Response(Response::OK);
	try
	{
		$postdata = getPostData();
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$stream = new Stream();
		$user = new User();
		$authentication = new Authentication();

		$authentication->verify();
		if (!isset($_GET['id']) or !isset($_GET['uid']) or !isset($postdata['right']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		if ($postdata['right'] != 9 and $postdata['right'] != 10
			and $postdata['right'] != 11 and $postdata['right'] != 12)
			throw new ParametersException("You are not allowed to use this role", 400);
		if (!$stream->getFromID($_GET['id']))
			throw new NotFoundException("This stream does not exist", Response::NOTFOUND);
		if (!$user->getFromId($_GET['uid']))
			throw new NotFoundException("This user does not exist", Response::NOTFOUND);
		if ($authentication->userHasRights(array(8, 9), $stream->id, "stream") === false)
			throw new RightsException("You don't have enough rigths to modify this stream", Response::NORIGHT);
		
		$rightsAuth = new Authentication(true, null, null, $user);
		$rights = $rightsAuth->getUserRights($stream->id, "stream");
		if (!in_array($postdata['right' ], $rights))
			$stream->addRole($user->id, $postdata['right']);
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