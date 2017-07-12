<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');
	
	$response = new Response(Response::OK);
	try {
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		
		$authentication = new Authentication();
		$authentication->verify();
		
		if (!isset($_GET['userid']))
			throw new ParametersException("Missing parameters to proceed id", Response::MISSPARAM);
		$user = $authentication->getUserFromToken();
		
		if (!$user)
			throw new UnknownException("Something wrong happened");

		if ($_GET['userid'] != $user->id) //Add moderator/admin detection here
			throw new RightsException("You cannot modify someone else's account", Response::NORIGHT);

		// Here test max width and max length
		$filetmp = $_SERVER['DOCUMENT_ROOT']."/avatar/tmp/".$user->id;
		$file = $_SERVER['DOCUMENT_ROOT']."/avatar/".$user->id;
		
		if (!$putdata  = fopen("php://input", "rb"))
			throw new UnknownException("Unable to read body", Response::UNKNOWN);

		if (!$fp = fopen($filetmp, "wb+"))
			throw new UnknownException("Unable to create file on the server", Response::UNKNOWN);
		
		while ($data = fread($putdata, 1024))
		{
			var_dump($data);
			fwrite($fp, $data);
		}
		
		fclose($fp);
		fclose($putdata);
		
		$imgType = exif_imagetype($filetmp);
		switch ($imgType)
		{
			case 1:
				$file .= '.gif';
				break;
			case 2:
				$file .= '.jpeg';
				break;
			case 3:
				$file .= '.png';
				break;
			default:
				var_dump($imgType);
				throw new ParametersException("We only support gif, jpeg and png", Response::UNSUPPORTED);
				break;
		}

		array_map('unlink', glob($_SERVER['DOCUMENT_ROOT']."/avatar/".$user->id.".*"));
		if (!rename($filetmp, $file))
			throw new UnknownException("Something wrong happened");
		$response->setMessage("The avatar has been replaced.");
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}

	
	// echo "PUT/USERS/ID/AVATAR";
?>
