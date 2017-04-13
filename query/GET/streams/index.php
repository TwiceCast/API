<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');

	$out = null;
	$stream = new Stream();
	if (isset($_GET['id']))
	{
		if (!$stream->getFromID($_GET['id']))
			$out = false;
		else
			$out = $stream;
	}
	else
	{
		$streams = $stream->getAllStreams();
		$out = $streams;
	}

	if (isset($_GET['accept']))
	{
		if ($_GET['accept'] == 'json')
		{
			header('Content-Type: application/json');
			if ($out)
				echo json_encode($out);
			else
				echo '{"error":"This stream does not exist."}';
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
			echo '{"error":"This stream does not exist."}';
	}

	function toXML($obj)
	{
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
		if (is_array($obj))
		{
			$xml .= "<streams>\r\n";
			foreach ($obj as &$stream)
			{
				$xml .= "\t<stream>\r\n";
				$xml .= "\t\t<ID>".$stream->ID."</ID>\r\n";
				$xml .= "\t\t<title>".$stream->title."</title>\r\n";
				if ($stream->owner)
				{
					$xml .= "\t\t<owner>\r\n";
					$xml .= "\t\t\t<ID>".$stream->owner->ID."</ID>\r\n";
					$xml .= "\t\t\t<email>".$stream->owner->email."</email>\r\n";
					$xml .= "\t\t\t<nickname>".$stream->owner->nickname."</nickname>\r\n";
					if ($stream->owner->country)
					{
						$xml .= "\t\t\t<country>\r\n";
						$xml .= "\t\t\t\t<ID>".$stream->owner->country->ID."</ID>\r\n";
						$xml .= "\t\t\t\t<code>".$stream->owner->country->code."</code>\r\n";
						$xml .= "\t\t\t\t<name>".$stream->owner->country->name."</name>\r\n";
						$xml .= "\t\t\t</country>\r\n";
					}
					$xml .= "\t\t\t<birthdate>".$stream->owner->birthdate."</birthdate>\r\n";
					if ($stream->owner->rank)
					{
						$xml .= "\t\t\t<rank>\r\n";
						$xml .= "\t\t\t\t<ID>".$stream->owner->rank->ID."</ID>\r\n";
						$xml .= "\t\t\t\t<title>".$stream->owner->rank->title."</title>\r\n";
						$xml .= "\t\t\t</rank>\r\n";
					}
					$xml .= "\t\t\t<register_date>".$stream->owner->register_date."</register_date>\r\n";
					$xml .= "\t\t\t<last_visit_date>".$stream->owner->last_visit_date."</last_visit_date>\r\n";
					$xml .= "\t\t</owner>\r\n";
				}
				$xml .= "\t</stream>\r\n";
			}
			$xml .= "</streams>\r\n";
		}
		else
		{
			$xml .= "<stream>\r\n";
			$xml .= "\t<ID>".$obj->ID."</ID>\r\n";
			$xml .= "\t<title>".$obj->title."</title>\r\n";
			if ($obj->owner)
			{
				$xml .= "\t<owner>\r\n";
				$xml .= "\t\t<ID>".$obj->owner->ID."</ID>\r\n";
				$xml .= "\t\t<email>".$obj->owner->email."</email>\r\n";
				$xml .= "\t\t<nickname>".$obj->owner->nickname."</nickname>\r\n";
				if ($obj->owner->country)
				{
					$xml .= "\t\t<country>\r\n";
					$xml .= "\t\t\t<ID>".$obj->owner->country->ID."</ID>\r\n";
					$xml .= "\t\t\t<code>".$obj->owner->country->code."</code>\r\n";
					$xml .= "\t\t\t<name>".$obj->owner->country->name."</name>\r\n";
					$xml .= "\t\t</country>\r\n";
				}
				$xml .= "\t\t<birthdate>".$obj->owner->birthdate."</birthdate>\r\n";
				if ($obj->owner->rank)
				{
					$xml .= "\t\t<rank>\r\n";
					$xml .= "\t\t\t<ID>".$obj->owner->rank->ID."</ID>\r\n";
					$xml .= "\t\t\t<title>".$obj->owner->rank->title."</title>\r\n";
					$xml .= "\t\t</rank>\r\n";
				}
				$xml .= "\t\t<register_date>".$obj->owner->register_date."</register_date>\r\n";
				$xml .= "\t\t<last_visit_date>".$obj->owner->last_visit_date."</last_visit_date>\r\n";
				$xml .= "\t</owner>\r\n";
			}
			$xml .= "</stream>\r\n";
		}
		return $xml;
	}
?>