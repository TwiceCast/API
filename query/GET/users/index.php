<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	$out = null;
	$user = new User();
	if (isset($_GET['id']))
	{
		if (!$user->getFromID($_GET['id']))
			$out = false;
		else
			$out = $user;
	}
	else if (isset($_GET['nickname']))
	{
		if (!$user->getFromNickname($_GET['nickname']))
			$out = false;
		else
			$out = $user;
	}
	else
	{
		$users = $user->getAllUsers();
		$out = $users;
	}

	if (isset($_GET['accept']))
	{
		if ($_GET['accept'] == 'json')
		{
			header('Content-Type: application/json');
			if ($out)
				echo json_encode($out);
			else
				echo '{"error":"This user does not exist."}';
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
				echo "\tThis user does not exist.\r\n";
				echo "</error>\r\n";
			}
		}
	}
	else
	{
		header('Content-Type: application/json');
		if ($out)
			echo json_encode($out);
		else
			echo '{"error":"This user does not exist."}';
	}

	function toXML($obj)
	{
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
		if (is_array($obj))
		{
			$xml .= "<users>\r\n";
			foreach ($obj as &$user)
			{
				$xml .= "\t<user>\r\n";
				$xml .= "\t\t<ID>".$user->ID."</ID>\r\n";
				$xml .= "\t\t<email>".$user->email."</email>\r\n";
				$xml .= "\t\t<nickname>".$user->nickname."</nickname>\r\n";
				if ($user->country)
				{
					$xml .= "\t\t<country>\r\n";
					$xml .= "\t\t\t<ID>".$user->country->ID."</ID>\r\n";
					$xml .= "\t\t\t<code>".$user->country->code."</code>\r\n";
					$xml .= "\t\t\t<name>".$user->country->name."</name>\r\n";
					$xml .= "\t\t</country>\r\n";
				}
				$xml .= "\t\t<birthdate>".$user->birthdate."</birthdate>\r\n";
				if ($user->rank)
				{
					$xml .= "\t\t<rank>\r\n";
					$xml .= "\t\t\t<ID>".$user->rank->ID."</ID>\r\n";
					$xml .= "\t\t\t<title>".$user->rank->title."</title>\r\n";
					$xml .= "\t\t</rank>\r\n";
				}
				$xml .= "\t\t<register_date>".$user->register_date."</register_date>\r\n";
				$xml .= "\t\t<last_visit_date>".$user->last_visit_date."</last_visit_date>\r\n";
				$xml .= "\t</user>\r\n";
			}
			$xml .= "</users>\r\n";
		}
		else
		{
			$xml .= "<user>\r\n";
			$xml .= "\t<ID>".$obj->ID."</ID>\r\n";
			$xml .= "\t<email>".$obj->email."</email>\r\n";
			$xml .= "\t<nickname>".$obj->nickname."</nickname>\r\n";
			if ($obj->country)
			{
				$xml .= "\t<country>\r\n";
				$xml .= "\t\t<ID>".$obj->country->ID."</ID>\r\n";
				$xml .= "\t\t<code>".$obj->country->code."</code>\r\n";
				$xml .= "\t\t<name>".$obj->country->name."</name>\r\n";
				$xml .= "\t</country>\r\n";
			}
			$xml .= "\t<birthdate>".$obj->birthdate."</birthdate>\r\n";
			if ($obj->rank)
			{
				$xml .= "\t<rank>\r\n";
				$xml .= "\t\t<ID>".$obj->rank->ID."</ID>\r\n";
				$xml .= "\t\t<title>".$obj->rank->title."</title>\r\n";
				$xml .= "\t</rank>\r\n";
			}
			$xml .= "\t<register_date>".$obj->register_date."</register_date>\r\n";
			$xml .= "\t<last_visit_date>".$obj->last_visit_date."</last_visit_date>\r\n";
			$xml .= "</user>\r\n";
		}
		return $xml;
	}
?>
