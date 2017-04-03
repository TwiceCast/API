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

	// For this part, we need to check all paramters like:
	//   - email: Must be valid and unique
	//   - password: Must pass minimal requierement
	//   - nickname: Must be valid and unique
	//   - country: Must exist
	//   - rank: Must exist
	
	// If there is too much SQL Query here we can stop using cangeXXX() function and use setXXX() then update()
	if ($out == Err::OK)
	{
		$out = array();
		if (isset($post['email']))
		{
			if ($user->changeEmail($post['email']))
				$out[] = array('email', Err::SUCCESS);
			else
				$out[] = array('email', Err::UNKNOW);
		}
		if (isset($post['password']))
		{
			if ($user->changePassword($post['password']))
				$out[] = array('password', Err::SUCCESS);
			else
				$out[] = array('password', Err::UNKNOW);
		}
		if (isset($post['nickname']))
		{
			if ($user->changeName($post['nickname']))
				$out[] = array('nickname', Err::SUCCESS);
			else
				$out[] = array('nickname', Err::UNKNOW);
		}
		// if (isset($post['country']))
		// {
			// if ($user->changeCountry($post['country']))
				// $out[] = array('country', Err::SUCCESS);
			// else
				// $out[] = array('country', Err::UNKNOW);
		// }
		// if (isset($post['birthdate']))
		// {
			// if ($user->changeBirthdate($post['birhdate']))
				// $out[] = array('birthdate', Err::SUCCESS);
			// else
				// $out[] = array('birthdate', Err::UNKNOW);
		// }
		// if (isset($post['rank']))
		// {
			// if ($user->changeRank($post['rank']))
				// $out[] = array('rank', Err::SUCCESS);
			// else
				// $out[] = array('rank', Err::UNKNOW);
		// }
	}
	else
	{
		switch ($out)
		{
			case Err::DOESNOTEXIST;
				echo '{"error":"This user does not exist"}';
				break;
			case Err::MISSPARAM;
				echo '{"error":"Missing parameters to proceed"}';
				break;
			default:
				echo '{"error":"Something wrong append"}';
		}
		$out = null;
	}

	if ($out)
	{
		if (isset($_GET['accept']))
		{
			if ($_GET['accept'] == 'json')
			{
				header('Content-Type: application/json');
				$first = true;
				echo '[';
				foreach ($out as &$change)
				{
					if (!$first)
						echo ',';
					echo '{"item":"'.$change[0].'", "error":';
					switch ($change[1])
					{
						case Err::SUCCESS:
							echo '"Item patched successfully"}';
							break;
						default:
							echo '"Something wrong append"}';
					}
					$first = false;
				}
				echo ']';
			}
			else if ($_GET['accept'] == 'xml')
			{
				header('Content-Type: application/xml');
				echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
				echo "<items>\r\n";
				foreach ($out as &$change)
				{
					echo "\t<item>\r\n";
					echo "\t\t<name>\r\n";
					echo "\t\t\t".$change[0]."\r\n";
					echo "\t\t</name>\r\n";
					echo "\t\t<error>\r\n";
					switch ($change[1])
					{
						case Err::SUCCESS:
							echo "\t\t\tItem patched successfully\r\n";
							break;
						default:
							echo "\t\t\tSomething wrong append\r\n";
					}
					echo "\t\t</error>\r\n";
					echo "\t</item>\r\n";
				}
				echo "</items>\r\n";
			}
		}
		else
		{
			header('Content-Type: application/json');
			$first = true;
			echo '[';
			foreach ($out as &$change)
			{
				if (!$first)
					echo ',';
				echo '{"item":"'.$change[0].'", "error":';
				switch ($change[1])
				{
					case Err::SUCCESS:
						echo '"Item patched successfully"}';
						break;
					default:
						echo '"Something wrong append"}';
				}
				$first = false;
			}
			echo ']';
		}
	}
?>
