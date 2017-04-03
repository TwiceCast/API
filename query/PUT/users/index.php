<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Error.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	function getRealPOST()
	{
		$pairs = explode("&", file_get_contents("php://input"));
		$vars = array();
		foreach ($pairs as $pair)
		{
			$nv = explode("=", $pair);
			$name = urldecode($nv[0]);
			$value = urldecode($nv[1]);
			$vars[$name] = $value;
		}
		return $vars;
	}
	$post = getRealPOST();
	$out = Err::OK;
	$user = new User();

	if (isset($_GET['id']))
	{
		if (!$user->getFromID($_GET['id']))
			$out = Err::DOESNOTEXIST;
	}
	else if (isset($_GET['nickname']))
	{
		if (!$user->getFromNickname($_GET['nickname']))
			$out = Err::DOESNOTEXIST;
	}
	else
		$out = Err::MISSPARAM;

	if ($out == Err::OK)
	{
		if (isset($post['nickname']) and isset($post['password']) and isset($post['email']))
		{
			$user->setName($post['nickname']);
			$user->setEmail($post['email']);
			$user->setPassword($post['password']);
			// if (isset($post['country']))
				// $user->setCountry((int)$post['country']);
			// else
				// $user->setCountry(null);
			// if (isset($post['birthdate']))
				// $user->setBirthdate($post['birthdate']);
			// else
				// $user->setBirthdate(null);
			// if (isset($post['rank']))
				// $user->setRank((int)$post['rank']);
			// else
				// $user->setRank(null);
			if ($user->update())
				$out = Err::SUCCESS;
			else
				$out = Err::UNKNOW;
		}
		else
			$out = Err::MISSPARAM;
	}

	if (isset($_GET['accept']))
	{
		if ($_GET['accept'] == 'json')
		{
			header('Content-Type: application/json');
			switch ($out)
			{
				case Err::SUCCESS:
					echo '{"error":"User overwrited successfully"}';
					break;
				case Err::UNKNOW:
					echo '{"error":"Something wrong append"}';
					break;
				case Err::DOESNOTEXIST:
					echo '{"error":"This user does not exist"}';
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
					echo "\tUser overwrited successfully\r\n";
					break;
				case Err::UNKNOW:
					echo "\tSomething wrong append\r\n";
					break;
				case Err::DOESNOTEXIST:
					echo "\tThis user does not exist\r\n";
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
				echo '{"error":"User overwrited successfully"}';
				break;
			case Err::UNKNOW:
				echo '{"error":"Something wrong append"}';
				break;
			case Err::DOESNOTEXIST:
				echo '{"error":"This user does not exist"}';
				break;
			case Err::MISSPARAM:
				echo '{"error":"Missing parameters to proceed"}';
				break;
			default:
				echo '{"error":"Something wrong append"}';
		}
	}
?>
