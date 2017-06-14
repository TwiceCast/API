<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');

	$response = new Response(Response::OK);
	$stream = new Stream();
	$streams = null;

	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	if (isset($_GET['userid']))
	{
		$streams = $stream->getFromUserID($_GET['userid']);
		if ($streams === false)
			$response->setMessage(["error", "Something wrong happened"], Response::UNKNOWN);
		else
			$response->setMessage(["streams" => $streams]);
	}
	else if (isset($_GET['usernickname']))
	{
		$streams = $stream->getFromUserNickname($_GET['usernickname']);
		if ($streams === false)
			$response->setMessage(["error", "Something wrong happened"], Response::UNKNOWN);
		else
			$response->setMessage(["streams" => $streams]);
	}
	else
		$response->setMessage(["error" => "Missing parameters to proceed"], Response::MISSPARAM);

	$response->send();
?>
