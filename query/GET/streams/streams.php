<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');

	$response = new Response(Response::OK);
	try {
		$stream = new Stream();

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (isset($_GET['id']))
		{
			if (!$stream->getFromID($_GET['id']))
				throw new ParametersException("This stream does not exist", Response::DOESNOTEXIST);
			$response->setMessage($stream);
		}
		else
		{
			$streams = $stream->getAllStreams($_GET['userid']);
			$response->setMessage(["stream_list" => $streams, "stream_total" => count($streams)]);
		}
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>