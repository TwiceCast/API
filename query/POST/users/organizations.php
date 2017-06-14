<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$response = new Response(Response::OK);
	
	if (isset($_GET['accept']))
		$response->setContentType($_GET['accept']);
	if (isset($_POST['name']) and isset($_GET['userid']))
	{
		$newOrganization = new Organization();
		$newOrganization->setName($_POST['name']);
		if (isset($_POST['desc']))
			$newOrganization->setDescription($_POST['desc']);
		
		$state = $newOrganization->checkForCreation();
		
		$response->setResponseType($state);
		if ($state == Response::OK)
		{
			if ($newOrganization->create())
			{
				$founderID = $newOrganization->createRole("Founder");
				$newOrganization->addPrivForRole($founderID, 1);
				$newOrganization->addPrivForRole($founderID, 2);
				$newOrganization->addPrivForRole($founderID, 3);
				$newOrganization->addPrivForRole($founderID, 4);
				$newOrganization->addPrivForRole($founderID, 5);
				$newOrganization->addPrivForRole($founderID, 6);
				$newOrganization->addPrivForRole($founderID, 7);
				$memberID = $newOrganization->createRole("Member");
				$newOrganization->addRoleToUser($founderID, $_GET['userid']);
				$response->setMessage(["message" => "Organization created successfully"]);
			}
			else
				$response->setMessage(["error" => "Something wrong happened"]);
		}
		else if ($state == ERR::ORGNAMEUSED)
			$response->setMessage(["error" => "Organization name already in use"]);
		else
			$response->setMessage(["error" => "Something wrong happened"]);
	}
	else
		$response->setMessage(["error" => "Missing parameters"]);
	
	$response->send();
?>