<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Error.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');

	$out = Err::OK;
	$stream = new Stream();
	$streams = null;

	if (isset($_GET['userid']))
	{
		$streams = $stream->getFromUserID($_GET['userid']);
		if ($streams === false)
			$out = Err::UNKNOW;
	}
	else if (isset($_GET['usernickname']))
	{
		$streams = $stream->getFromUserNickname($_GET['usernickname']);
		if ($streams === false)
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
					echo json_encode($streams);
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
		else if ($_GET['accept'] == 'xml')
		{
			header('Content-Type: application/xml');
			if ($out == Err::OK)
				echo toXML($streams);
			else
			{
				echo "<?xml version=\"1.0\# encoding=\"UTF-8\"?>\r\n";
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
						echo "\tMissing parameters to proceed\r\n";
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
				echo json_encode($streams);
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
			$xml .= "<streams>\r\n";
			foreach ($obj as &$stream)
			{
				$xml .= "<stream>\r\n";
				$xml .= "\t<ID>".$stream->ID."</ID>\r\n";
				$xml .= "\t<title>".$stream->title."</title>\r\n";
				if ($stream->owner)
				{
					$xml .= "\t<owner>\r\n";
					$xml .= "\t\t<ID>".$stream->owner->ID."</ID>\r\n";
					$xml .= "\t\t<email>".$stream->owner->email."</email>\r\n";
					$xml .= "\t\t<nickname>".$stream->owner->nickname."</nickname>\r\n";
					if ($stream->owner->country)
					{
						$xml .= "\t\t<country>\r\n";
						$xml .= "\t\t\t<ID>".$stream->owner->country->ID."</ID>\r\n";
						$xml .= "\t\t\t<code>".$stream->owner->country->code."</code>\r\n";
						$xml .= "\t\t\t<name>".$stream->owner->country->name."</name>\r\n";
						$xml .= "\t\t</country>\r\n";
					}
					$xml .= "\t\t<birthdate>".$stream->owner->birthdate."</birthdate>\r\n";
					if ($stream->owner->rank)
					{
						$xml .= "\t\t<rank>\r\n";
						$xml .= "\t\t\t<ID>".$stream->owner->rank->ID."</ID>\r\n";
						$xml .= "\t\t\t<title>".$stream->owner->rank->title."</title>\r\n";
						$xml .= "\t\t</rank>\r\n";
					}
					$xml .= "\t\t<register_date>".$stream->owner->register_date."</register_date>\r\n";
					$xml .= "\t\t<last_visit_date>".$stream->owner->last_visit_date."</last_visit_date>\r\n";
					$xml .= "\t</owner>\r\n";
				}
				$xml .= "</stream>\r\n";
			}
			$xml .= "</streams>\r\n";
		}
		else
		{
			$xml .= "<stream>\r\n";
			$xml .= "\t<ID>".$obj->ID."</ID\r\n";
			$xml .= "\t<title>".$obj->title."</title>\r\n";
			$xml .= "\t<owner>\r\n";
			$xml .= "\t\t<ID>".$obj->ID."</ID>\r\n";
			$xml .= "\t\t<email>".$obj->email."</email>\r\n";
			$xml .= "\t\t<nickname>".$obj->nickname."</nickname>\r\n";
			if ($obj->country)
			{
				$xml .= "\t\t<country>\r\n";
				$xml .= "\t\t\t<ID>".$obj->country->ID."</ID>\r\n";
				$xml .= "\t\t\t<code>".$obj->country->code."</code>\r\n";
				$xml .= "\t\t\t<name>".$obj->country->name."</name>\r\n";
				$xml .= "\t\t</country>\r\n";
			}
			$xml .= "\t\t<birthdate>".$obj->birthdate."</birthdate>\r\n";
			if ($obj->rank)
			{
				$xml .= "\t\t<rank>\r\n";
				$xml .= "\t\t\t<ID>".$obj->rank->ID."</ID>\r\n";
				$xml .= "\t\t\t<title>".$obj->rank->title."</title>\r\n";
				$xml .= "\t\t</rank>\r\n";
			}
			$xml .= "\t\t<register_date>".$obj->register_date."</register_date>\r\n";
			$xml .= "\t\t<last_visit_date>".$obj->last_visit_date."</last_visit_date>\r\n";
			$xml .= "\t</owner>\r\n";
			$xml .= "</stream>\r\n";
		}
		return $xml;
	}
?>
