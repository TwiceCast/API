<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/config.php');

	$response = new Response(Response::OK);
	try {
		$stream = new Stream();
		$auth = new Authentication();
		$config = $_SESSION["config"]["repository"];

		$auth->verify();
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (!isset($_GET['id']))
			throw new NotFoundException("Missing stream id", Response::DOESNOTEXIST);
		if (!$stream->getFromID($_GET['id']))
			throw new ParametersException("This stream does not exist", Response::DOESNOTEXIST);
		$url = $config["protocol"].($config["ssl"] ? "s" : "")."://".$config["host"];
		if ($config["port"] != "")
			$url .= ":".$config["port"];
		$token = $auth->generateRepositoryToken($stream);
		$response->setMessage(["url" => $url, "token" => "$token"]);
		// throw new NotImplementedException("This feature is not implemented yet", 501);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>