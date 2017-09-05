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

		$avatars = glob($_SERVER['DOCUMENT_ROOT']."/avatar/".$user->id.".*");
		if ($avatars === false)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		else if (empty($avatars))
		{
			$avatars = glob($_SERVER['DOCUMENT_ROOT']."/avatar/0.*");
			if ($avatars === false)
				throw new UnknownException("Something wrong happenedb", Response::UNKNOWN);
			else if (empty($avatars))
				throw new NotFoundException("Avatar not found", Response::NOTFOUND);
		}
		$path_parts = pathinfo($avatars[0]);
		if ($path_parts['extension'] == 'gif')
			header('Content-Type: image/gif');
		else if ($path_parts['extension'] == 'jpeg' or $path_parts['extension'] == 'jpg')
			header('Content-Type: image/jpeg');
		else if ($path_parts['extension'] == 'png')
			header('Content-Type: image/png');
		else
			throw new UnknownException("Something wrong happeneda", Response::UNKNOWN);
		header('Content-Length: '.filesize($avatars[0]));
		readfile($avatars[0]);
		exit();
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>
