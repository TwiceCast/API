<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	$postdata = file_get_contents('php://input');
	$response = new Response(Response::OK);

	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	if (isset($postdata->name) and isset($postdata->password) and isset($postdata->email))
	{
		$newUser = new User();
		$newUser->setName($postdata->name);
		$newUser->setEmail($postdata->email);
		$newUser->setPassword($postdata->password);
		// if (isset($_POST['country']))
			// $newUser->setCountry($_POST['country']);
		// if (isset($_POST['birthdate']))
			// $newUser->setBirthdate($_POST['birthdate']);
		// if (isset($_POST['rank']))
			// $newUser->setRank($_POST['rank']);
		$state = $newUser->checkForCreation();
		
		if ($state == Response::OK)
		{
			if ($newUser->create())
				$response->setMessage(["message" => "User created successfully"]);
			else
				$response->setMessage(["error" => "Something wrong append"]);
		}
		else	if ($state == Response::NICKUSED)
			$response->setMessage(["error":"Nickname already in use"]);
		else if ($state == Response::EMAILUSED)
			$response->setMessage(["error":"Email already in use"]);
		else
			$response->setMessage(["error":"Something wrong append"]);
		}
		$response->setResponseType($state);
	}
	else
		$response->setMessage(["error":"Missing parameters"], Response::MISSPARAM);
	$response->send();
?>