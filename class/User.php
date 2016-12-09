<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Error.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Country.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Rank.php');

	class User
	{
		var $ID;
		var $email;
		private $password;
		var $nickname;
		var $country;
		var $birthdate;
		var $rank;
		var $register_date;
		var $last_visit_date;
		var $avatar;
		private $db;

		function __construct($db = true, $ID = null, $email = null, $password = null, $nickname = null, $country = null, $birthdate = null, $rank = null, $register_date = null, $last_visit_date = null)
		{
			$this->setID($ID);
			$this->setEmail($email);
			$this->setPassword($password);
			$this->setNickname($nickname);
			$this->setCountry($country);
			$this->setBirthdate($birthdate);
			$this->setRank($rank);
			$this->setRegisterDate($register_date);
			$this->setLastvisitDate($last_visit_date);
			if ($db)
				$this->db = new DB();
			else
				$this->db = null;
		}

		function setID($ID)
		{
			$this->ID = $ID;
			$tmp = $_SERVER['DOCUMENT_ROOT'].'/avatar/'.$this->ID.'.png';
			if (file_exists($tmp))
				$this->avatar = $tmp;
			else
				$this->avatar = 'api/avatar/0.png';
			return $this;
		}

		function setEmail($email)
		{
			$this->email = $email;
			return $this;
		}

		function setPassword($password)
		{
			$this->password = $password;
			return $this;
		}

		function setNickname($nickname)
		{
			$this->nickname = $nickname;
			return $this;
		}

		function setCountry($country)
		{
			$this->country = $country;
			return $this;
		}

		function setBirthdate($birthdate)
		{
			$this->birthdate = $birthdate;
			return $this;
		}

		function setRank($rank)
		{
			$this->rank = $rank;
			return $this;
		}

		function setRegisterDate($register_date)
		{
			$this->register_date = $register_date;
			return $this;
		}

		function setLastVisitDate($last_visit_date)
		{
			$this->last_visit_date = $last_visit_date;
			return $this;
		}

		function getLink($db)
		{
			if ($this->db)
				return $this->db;
			else if ($db)
				return $db;
			else
				return false;
		}

		function getFromID($ID, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT user.id AS userID,
					user.email AS userEmail,
					user.password AS userPassword,
					user.nickname AS userNickname,
					user.birthdate AS userBirthdate,
					user.register_date AS userRegisterDate,
					user.last_visit_date AS userLastVisitDate,
					country.id AS countryID,
					country.code AS countryCode,
					country.name AS countryName,
					rank.id AS rankID,
					rank.title AS rankTitle
					FROM user
					LEFT JOIN country ON user.fk_country = country.id
					LEFT JOIN rank ON user.fk_rank = rank.id
					WHERE user.id = :ID');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['userID']);
					$this->setEmail(DB::fromDB($data['userEmail']));
					$this->setPassword($data['userPassword']);
					$this->setNickname(DB::fromDB($data['userNickname']));
					$this->setBirthdate($data['userBirthdate']);
					$this->setRegisterDate($data['userRegisterDate']);
					$this->setLastVisitDate($data['userLastVisitDate']);
					$country = new Country(false);
					$country->setID($data['countryID']);
					$country->setCode(DB::fromDB($data['countryCode']));
					$country->setName(DB::fromDB($data['countryName']));
					$this->setCountry($country);
					$rank = new Rank(false);
					$rank->setID($data['rankID']);
					$rank->setTitle(DB::fromDB($data['rankTitle']));
					$this->setRank($rank);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getFromNickname($nickname, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT user.id AS userID,
					user.email AS userEmail,
					user.password AS userPassword,
					user.nickname AS userNickname,
					user.birthdate AS userBirthdate,
					user.register_date AS userRegisterDate,
					user.last_visit_date AS userLastVisitDate,
					country.id AS countryID,
					country.code AS countryCode,
					country.name AS countryName,
					rank.id AS rankID,
					rank.title AS rankTitle
					FROM user
					LEFT JOIN country ON user.fk_country = country.id
					LEFT JOIN rank ON user.fk_rank = rank.id
					WHERE user.nickname = :nickname');
				$link->bindParam(':nickname', $nickname, PDO::PARAM_STR);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['userID']);
					$this->setEmail(DB::fromDB($data['userEmail']));
					$this->setPassword($data['userPassword']);
					$this->setNickname(DB::fromDB($data['userNickname']));
					$this->setBirthdate($data['userBirthdate']);
					$this->setRegisterDate($data['userRegisterDate']);
					$this->setLastVisitDate($data['userLastVisitDate']);
					$country = new Country(false);
					$country->setID($data['countryID']);
					$country->setCode(DB::fromDB($data['countryCode']));
					$country->setName(DB::fromDB($data['countryName']));
					$this->setCountry($country);
					$rank = new Rank(false);
					$rank->setID($data['rankID']);
					$rank->setTitle(DB::fromDB($data['rankTitle']));
					$this->setRank($rank);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getAllUsers($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT user.id AS userID,
					user.email AS userEmail,
					user.password AS userPassword,
					user.nickname AS userNickname,
					user.birthdate AS userBirthdate,
					user.register_date AS userRegisterDate,
					user.last_visit_date AS userLastVisitDate,
					country.id AS countryID,
					country.code AS countryCode,
					country.name AS countryName,
					rank.id AS rankID,
					rank.title AS rankTitle
					FROM user
					LEFT JOIN country ON user.fk_country = country.id
					LEFT JOIN rank ON user.fk_rank = rank.id');
				$data = $link->fetchAll(true);
				if ($data)
				{
					$users = array();
					foreach ($data as &$entry)
					{
						$user = new User(false);
						$user->setID($entry['userID']);
						$user->setEmail(DB::fromDB($entry['userEmail']));
						$user->setPassword($entry['userPassword']);
						$user->setNickname(DB::fromDB($entry['userNickname']));
						$user->setBirthdate($entry['userBirthdate']);
						$user->setRegisterDate($entry['userRegisterDate']);
						$user->setLastVisitDate($entry['userLastVisitDate']);
						$country = new Country(false);
						$country->setID($entry['countryID']);
						$country->setCode(DB::fromDB($entry['countryCode']));
						$country->setName(DB::fromDB($entry['countryName']));
						$user->setCountry($country);
						$rank = new Rank(false);
						$rank->setID($entry['rankID']);
						$rank->setTitle(DB::fromDB($entry['rankTitle']));
						$user->setRank($rank);
						$users[] = $user;
					}
					return $users;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeEmail($newEmail, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE user
					SET user.email = :email
					WHERE user.id = :ID');
				$tmp = DB::toDB($newEmail);
				$link->bindParam(':email', $tmp, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->email = $newEmail;
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changePassword($newPassword, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE user
					SET user.password = :password
					WHERE user.id = :ID');
				$link->bindParam(':password', $newPassword, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->password = $newPassword;
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeNickname($newNickname, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE user
					SET user.nickname = :nickname
					WHERE user.id = :ID');
				$tmp = DB::toDB($newNickname);
				$link->bindParam(':nickname', $tmp, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->nickname = $newNickname;
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeCountry($newCountryID, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE user
					SET user.fk_country = :country
					WHERE user.id = :ID');
				$link->bindParam(':country', $newCountryID, PDO::PARAM_INT);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					if (!$this->country)
						$this->country = new Country(false);
					$this->country->getFromID($newCountryID, $link);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeBirthdate($newBirthdate, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE user
					SET user.birthdate = :birthdate
					WHERE user.id = :ID');
				$link->bindParam(':birthdate', $newBrithdate, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->birthdate = $newBirthdate;
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeRank($newRankID, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE user
					SET user.fk_rank = :rank
					WHERE user.id = :ID');
				$link->bindParam(':rank', $newRankID, PDO::PARAM_INT);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					if (!$this->rank)
						$this->rank = new Rank(false);
					$this->rank->getFromID($newRankID, $link);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeLastVisitDate($newLastVisitDate, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->preapre('
					UPDATE user
					SET user.last_visit_date = :lastVisitDate
					WHERE user.id = :ID');
				$link->bindParam(':lastVisitDate', $newLastVisitDate, PDO::PARAM_INT);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->last_visit_date = $newLastVisitDate;
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function update($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE user
					SET user.email = :email,
					user.password = :password,
					user.nickname = :nickname,
					user.fk_country = :country,
					user.birthdate = :birthdate,
					user.fk_rank = :rank,
					user.last_visit_date = :lastVisitDate
					WHERE user.ID = :ID');
				$email = DB::toDB($this->email);
				$nickname = DB::toDB($this->nickname);
				$link->bindParam(':email', $email, PDO::PARAM_STR);
				$link->bindParam(':password', $this->password, PDO::PARAM_STR);
				$link->bindParam(':nickname', $nickname, PDO::PARAM_STR);
				if ($this->country)
				{
					if (is_int($this->country))
						$link->bindParam(':country', $this->country, PDO::PARAM_INT);
					else
						$link->bindParam(':country', $this->country->ID, PDO::PARAM_INT);
				}
				else
					$link->bindParam(':country', 0, PDO::PARAM_INT);
				if ($this->birthdate)
					$link->bindParam(':birthdate', $this->birthdate, PDO::PARAM_STR);
				else
					$link->bindParam(':birthdate', null, PDO::PARAM_NULL);
				if ($this->rank)
				{
					if (is_int($this->rank))
						$link->bindParam(':rank', $this->rank, PDO::PARAM_INT);
					else
						$link->bindParam(':rank', $this->rank->ID, PDO::PARAM_INT);
				}
				else
					$link->bindParam(':rank', 0, PDO::PARAM_INT);
				$link->bindParam(':lastVisitDate', $this->last_visit_date, PDO::PARAM_INT);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}

		function create($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				if ($this->checkForCreation($link) == ERR::OK)
				{
					$link->prepare('
						INSERT INTO user(email, password, nickname, fk_country, birthdate, fk_rank)
						VALUE(:email, :password, :nickname, :fk_country, :birthdate, :fk_rank)');
					$tmpEmail = DB::toDB($this->email);
					$tmpNickname = DB::toDB($this->nickname);
					$link->bindParam(':email', $tmpEmail, PDO::PARAM_STR);
					$link->bindParam(':password', $this->password, PDO::PARAM_STR);
					$link->bindParam(':nickname', $tmpNickname, PDO::PARAM_STR);
					if ($this->country)
						$link->bindParam(':fk_country', $this->country->ID, PDO::PARAM_INT);
					else
						$link->bindParam(':fk_country', 0, PDO::PARAM_INT);
					if ($this->birthdate)
						$link->bindParam(':birthdate', $this->birthdate, PDO::PARAM_STR);
					else
						$link->bindParam(':birthdate', null, PDO::PARAM_NULL);
					if ($this->rank)
						$link->bindParam(':fk_rank', $this->rank->ID, PDO::PARAM_INT);
					else
						$link->bindParam(':fk_rank', 0, PDO::PARAM_INT);
					return $link->execute(true);
				}
				else
					return false;
			}
			else
				return false;
		}

		function checkForCreation($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT user.id AS userID
					FROM user
					WHERE user.nickname = :nickname');
				$link->bindParam(':nickname', $this->nickname, PDO::PARAM_STR);
				$data = $link->fetchAll(true);
				if ($data)
					return ERR::NICKUSED;
				else
				{
					$link->prepare('
						SELECT user.id AS userID
						FROM user
						WHERE user.email = :email');
					$link->bindParam(':email', $this->email, PDO::PARAM_STR);
					$data = $link->fetchAll(true);
					if ($data)
						return ERR::EMAILUSED;
					else
						return ERR::OK;
				}
			}
			else
				return ERR::UNKNOW;
		}

		function delete($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					DELETE
					FROM user
					WHERE user.id = :ID');
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}

		function makeHMAC($string, $algo = 'sha1')
		{
			return hash_hmac($algo, $string, $this->password);
		}
	}
?>