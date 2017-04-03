<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Error.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$out = Err::OK;
	$organization = new Organization();
	$organizations = null;

	if (isset($_GET['userid']))
	{
		$organizations = $organization->getFromUserID($_GET['userid']);
		if ($organizations === false)
			$out = Err::UNKNOW;
	}
	else
		$out = Err::MISSPARAM;

	if (isset($_GET['accept']))
	{
		if ($_GET['accept'] == 'json')
		{
			header('Content-Type: application/json');
			switch ($out)
			{
				case Err::OK:
					echo json_encode($organizations);
					break;
				case Err::UNKNOW:
					echo '{"error":"Somehting wrong append"}';
					break;
				case Err::MISSPARAM:
					echo '{"error":"Missing parameters to proceed"}';
					break;
				default:
					echo '{"error":"Something wrong append"}';
			}
		}
		else if ($_GET['accept'] == 'xml')
		{
			header('Content-Type: application/xml');
			if ($out == Err::OK)
				echo toXML($organizations);
			else
			{
				echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
				echo "<error>\r\n";
				switch ($out)
				{
					case Err::OK:
						echo toXML($out);
						break;
					case Err::UNKNOW:
						echo "\tSomething wrong append\r\n";
						break;
					case Err::MISSPARAM:
						echo "\tMissing parameters ot proceed\r\n";
						break;
					default:
						echo "\tSomething wrong append";
				}
				echo "</error>\r\n";
			}
		}
	}
	else
	{
		header('Content-Type: application/json');
		switch ($out)
		{
			case Err::OK:
				echo json_encode($organizations);
				break;
			case Err::UNKNOW:
				echo '{"error":"Something wrong append"}';
				break;
			case Err::MISSPARAM:
				echo '{"error":"Missing parameters to proceed"}';
				break;
			default:
				echo '{"error":"Something wrong append"}';
		}
	}

	function toXML($obj)
	{
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
		if (is_array($obj))
		{
			$xml .= "<organizations>\r\n";
			foreach ($obj as &$organization)
			{
				$xml .= "<organization>\r\n";
				$xml .= "\t<ID>".$organization->ID."</ID>\r\n";
				$xml .= "\t<name>".$organization->name."</name>\r\n";
				$xml .= "</organization>\r\n";
			}
			$xml .= "</organizations>\r\n";
		}
		else
		{
			$xml .= "<organization>\r\n";
			$xml .= "\t<ID>".$obj->ID."</ID>\r\n";
			$xml .= "\t<name>".$obj->name."</name>\r\n";
			$xml .= "</organization>\r\n";
		}
		return $xml;
	}
?>