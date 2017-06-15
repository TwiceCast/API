<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');

	$stream = new Stream();
	$response = new Response(Response::OK);
	
	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	if (isset($_GET['id']))
	{
		if (!$stream->getFromID($_GET['id']))
			$response->setMessage(["error" => "This stream does not exist."], Response::DOESNOTEXIST);
		else
			$response->setMessage($stream);
	}
	else
	{
		$streams = $stream->getAllStreams();
		$response->setMessage(["streams" => $streams]);
	}

	$response->send();
?>