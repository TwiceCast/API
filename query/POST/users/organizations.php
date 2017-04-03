<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	if (isset($_POST['name']) and isset($_GET['userid']))
	{
		$newOrganization = new Organization();
		$newOrganization->setName($_POST['name']);
		if (isset($_POST['desc']))
			$newOrganization->setDescription($_POST['desc']);
		
		$state = $newOrganization->checkForCreation();
		
		if ($state == ERR::OK)
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
				echo '{"error":"Organization created successfully"}';
			}
			else
				echo '{"error":"Something wrong append"}';
		}
		else
		{
			if ($state == ERR::ORGNAMEUSED)
				echo '{"error":"Organization name already in use"}';
			else
				echo '{"error":"Something wrong append"}';
		}
	}
	else
		echo '{"error":"Missing parameters"}';
?>