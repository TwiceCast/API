<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Error.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Organization.php');

	$out = null;
	$organization = new Organization();

	if (isset($_GET['id']))
	{
		if ($organization->getFromID($_GET['id']))
		{
			if ($organization->delete())
				$out = Err::SUCCESS;
			else
				$out = Err::UNKNOW;
		}
		else
			$out = Err::DOESNOTEXIST;
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
				case Err::SUCCESS:
					echo '{"error":"Organization deleted successfully"}';
					break;
				case Err::UNKNOW:
					echo '{"error":"Something wrong append"}';
					break;
				case Err::DOESNOTEXIST:
					echo '{"error":"This organization does not exist"}';
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
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
			echo "<error>\r\n";
			switch ($out)
			{
				case Err::SUCCESS:
					echo "\tOrganization deleted successfully\r\n";
					break;
				case Err::UNKNOW:
					echo "\tSomething wrong append\r\n";
					break;
				case Err::DOESNOTEXIST:
					echo "\tThis organization does not exist\t\n";
					break;
				case Err::MISSPARAM:
					echo "\tMissing parameters to proceed\r\n";
					break;
				default:
					echo "\tSomething wrong append\r\n";
			}
			echo "</error>\r\n";
		}
	}
	else
	{
		header('Content-Type: application/json');
		switch ($out)
		{	
			case Err::SUCCESS:
				echo '{"error":"Organization deleted successfully"}';
				break;
			case Err::UNKNOW:
				echo '{"error":"Something wrong append"}';
				break;
			case Err::DOESNOTEXIST:
				echo '{"error":"This organization does not exist"}';
				break;
			case Err::MISSPARAM:
				echo '{"error":"Missing parameters to proceed"}';
				break;
			default:
				echo '{"error":"Something wrong append"}';
		}
	}
?>
?>