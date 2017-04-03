<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	if (isset($_POST['nickname']) and isset($_POST['password']) and isset($_POST['email']))
	{
		$newUser = new User();
		$newUser->setName($_POST['nickname']);
		$newUser->setEmail($_POST['email']);
		$newUser->setPassword($_POST['password']);
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
?>