<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	$postdata = json_decode(file_get_contents('php://input'));
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
		
		
		// Part to move in the Error class inside an error generating function.
		if ($state == ERR::OK)
		{
			if ($newUser->create())
				echo '{"error":"User created successfully"}';
			else
				echo '{"error":"Something wrong append"}';
		}
		else
		{
			if ($state == ERR::NICKUSED)
				echo '{"error":"Nickname already in use"}';
			else if ($state == ERR::EMAILUSED)
				echo '{"error":"Email already in use"}';
			else
				echo '{"error":"Something wrong append"}';
		}
	}
	else
		echo '{"error":"Missing parameters"}';
?>