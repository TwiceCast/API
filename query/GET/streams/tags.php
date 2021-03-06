<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');

	$response = new Response(Response::OK);
	try
	{
		$stream = new Stream();

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (!isset($_GET['id']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
			
		if (!$stream->getFromID($_GET['id']))
			throw new ParametersException("This stream does not exist", Response::DOESNOTEXIST);
		$rep = new stdClass();
		$rep->tag_list = $stream->tags;
		$rep->tag_total = count($stream->tags);
		$response->setMessage($rep);
	}
	catch (CustomException $e)
	{
		$response->setError($e);
	}
	finally
	{
		$response->send();
	}