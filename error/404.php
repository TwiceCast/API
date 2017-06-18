<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');

	$response = new Response();
	try {
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		throw new NotFoundException("Ressource not found", Response::NOTFOUND);
	}
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>