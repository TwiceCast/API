<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$response = new Response(Response::OK);
	try {
		
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		if (!isset($_POST['name']) or !isset($_GET['userid']))
			throw new ParametersException("Missing parameters", Response::MISSPARAM);
		$newOrganization = new Organization();
		$newOrganization->setName($_POST['name']);
		if (isset($_POST['desc']))
			$newOrganization->setDescription($_POST['desc']);
		
		$newOrganization->checkForCreation();
		
		if (!$newOrganization->create())
			throw new UnknownException("Something wrong append", Response::UNKNOWN);
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
		$response->setMessage(["message" => "Organization created successfully"], Response::SUCCESS);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>