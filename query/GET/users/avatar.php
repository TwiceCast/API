<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');

	$response = new Response(Response::OK);
	try {
		$user = new User();
		$authentication = null;
		
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		
		$authentication = new Authentication();
		$authentication->verify(false);
		if (!isset($_GET['userid']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		if (!$user->getFromId($_GET['userid']))
			throw new NotFoundException("This user id doesn’t exist", Response::NOTFOUND);

		header('Content-Type: image/png');
		//header('Content-Disposition: attachment; filename="'.$user->id.'.png"');
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/avatar/'.$user->id.'.png'))
			$file = $_SERVER['DOCUMENT_ROOT'].'/avatar/'.$user->id.'.png';
		else
			$file = $_SERVER['DOCUMENT_ROOT'].'/avatar/0.png';
		header('Content-Length: '.filesize($file));
		readfile($file);
		exit();
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
