<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');

	$response = new Response(Response::OK);
	try {
		$stream = new Stream();

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (!isset($_GET['id']))
			throw new NotFoundException("Missing stream id", Response::DOESNOTEXIST);
		if (!$stream->getFromID($_GET['id']))
			throw new ParametersException("This stream does not exist", Response::DOESNOTEXIST);
		
		throw new NotImplementedException("This feature is not implemented yet", 501);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>