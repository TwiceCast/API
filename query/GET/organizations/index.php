<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$out = null;
	$organization = new Organization();

	if (isset($_GET['id']))
	{
		if (!$organization->getFromID($_GET['id']))
			$out = false;
		else
			$out = $organization;
	}
	else
	{
		$organizations = $organization->getAllOrganizations();
		$out = $organizations;
	}

	if (isset($_GET['accept']))
	{
		if ($_GET['accept'] == 'json')
		{
			header('Content-Type: application/json');
			if ($out)
				echo json_encode($out);
			else
				echo '{"error":"This organization does not exist."}';
		}
		else if ($_GET['accept'] == 'xml')
		{
			header('Content-Type: application/xml');
			if ($out)
				echo toXML($out);
			else
			{
				echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
				echo "<error>\r\n";
				echo "\tThis organization does not exist.\r\n";
				echo "</error>\r\n";
			}
		}
	}
	else
	{
		header('Content-Type: appliation/json');
		if ($out)
			echo json_encode($out);
		else
			echo '{"error":"This organization does not exist."}';
	}

	function toXML($obj)
	{
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
		if (is_array($obj))
		{
			$xml .= "<organizations>\r\n";
			foreach ($obj as &$org)
			{
				$xml .= "\t<organization>\r\n";
				$xml .= "\t\t<ID>".$org->ID."</ID>\r\n";
				$xml .= "\t\t<name>".$org->name."\</name>\r\n";
				$xml .= "\t</organization>\r\n";
			}
			$xml .= "</organizations>\r\n";
		}
		else
		{
			$xml .= "<organization>\r\n";
			$xml .= "\t<ID>".$org->ID."</ID>\r\n";
			$xml .= "\t<name>".$org->ID."</name>\r\n";
			$xml .= "</organization>\r\n";
		}
		return $xml;
	}
?>