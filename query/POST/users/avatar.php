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
			throw new ParametersException("You cannot modify someone else's account", Response::NORIGHT);
		
		if (!isset($_FILES) or !isset($_FILES['avatar']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		
		switch ($_FILES['avatar']['error'])
		{
			case 0:
				break;
			case 1:
				throw new UploadException("File is too large", Response::TOOLARGE);
				break;
			case 2:
				throw new UploadException("File is too large", Response::TOOLARGE);
				break;
			case 3:
				throw new UploadException("File partially uploaded", Response::BADREQUEST);
				break;
			case 4:
				throw new UploadException("No file uploaded", Response::BADREQUEST);
				break;
			default:
				break;
		}
		if ($_FILES['avatar']['type'] != "image/png" and $_FILES['avatar']['type'] != "image/x-png")
			throw new ParametersException("We only support png", Response::UNSUPPORTED);
		
		// Here test max width and max length
		
		$filetmp = $_SERVER['DOCUMENT_ROOT']."/avatar/tmp/".$user->id.".png";
		$file = $_SERVER['DOCUMENT_ROOT']."/avatar/".$user->id.".png";
		move_uploaded_file($_FILES['avatar']['tmp_name'], $filetmp);
		
		if (exif_imagetype($filetmp) == 3)
			if (!rename($filetmp, $file))
				throw new UnknownException("Something wrong happened");
		$response->setMessage("The avatar has been replaced.");
		// Authentication
		// userid exist
		// auth = userid
		// file = png
		// file magic = pngmagic    ‰PNG
		// move file
		// avatar/userid.png
	
	
		//throw new NotImplementedException("This feature is not implemented yet", 501);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}

	
	// echo "PUT/USERS/ID/AVATAR";
?>
