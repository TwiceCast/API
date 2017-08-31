<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');

	$response = new Response(Response::OK);
	try {
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$authentication = new Authentication();
		$authentication->verify();

		if (!isset($_GET['id']))
			throw new ParametersException("/Mussing parameters to proceed", Response::MISSPARAM);
		$user = $authentication->getUserFromToken();

		if (!$user)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);

		$stream = new Stream();
		if ($stream->getFromId($_GET['id']) === false)
			throw new NotFoundException("This stream does not exist", Response::NOTFOUND);

		// role 8 == Stream Founder
		if ($authentication->userHasRights(8, $stream->id, "stream") === false)
			throw new RightsException("You cannot modify someone else's stream", Response ::NORIGHT);

		if (!$stream->delete())
			throw new UnknownException("Somehitng wrong happened", Response::UNKNOWN);
		$response->setMessage("The stream heas been removed successfully", Response::SUCCESS);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>