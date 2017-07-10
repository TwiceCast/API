<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');

	$response = new Response(Response::OK);
	try {
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		throw new NotImplementedException("This feature is not implemented yet", 501);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
	// echo "PATCHORGANIZATION.PHP";
	// var_dump($_GET);
?>