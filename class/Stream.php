<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'./class/DB.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'./class/User.php');

	class Stream
	{
		var $ID;
		var $title;
		var $owner;
		private $db;

		function __construct($db = true, $ID = null, $title = null, $owner = null)
		{
			$this->setID($ID);
			$this->setTitle($title);
			$this->setOwner($owner);
			if ($db)
				$this->db = new DB();
			else
				$this->db = null;
		}

		function setID($ID)
		{
			$this->ID = $ID;
			return $this;
		}

		function setTitle($title)
		{
			$this->title = $title;
			return $this;
		}

		function setOwner($owner)
		{
			$this->owner = $owner;
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
					SELECT stream.id AS streamID,
					stream.title AS streamTitle,
					user.id AS userID,
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
					FROM stream
					LEFT JOIN user ON stream.fk_user = user.id
					LEFT JOIN country ON user.fk_country = country.id
					LEFT JOIN rank ON user.fk_rank = rank.id
					WHERE stream.id = :ID');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['streamID']);
					$this->setTitle($data['streamTitle']);
					$user = new User(false);
					$user->setID($data['userID']);
					$user->setEmail(DB::fromDB($data['userEmail']));
					$user->setPassword($data['userPassword']);
					$user->setNickname(DB::fromDB($data['userNickname']));
					$user->setBirthdate($data['userBirthdate']);
					$user->setRegisterDate($data['userRegisterDate']);
					$user->setLastVisitDate($data['userLastVisitDate']);
					$country = new Country(false);
					$country->setID($data['countryID']);
					$country->setCode(DB::fromDB($data['countryCode']));
					$country->setName(DB::fromDB($data['countryName']));
					$user->setCountry($country);
					$rank = new Rank(false);
					$rank->setId($data['rankID']);
					$rank->setTitle(DB::fromDB($data['rankTitle']));
					$user->setRank($rank);
					$this->setOwner($user);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getFromUserID($ID, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT stream.id AS streamID,
					stream.title AS streamTitle,
					user.id AS userID,
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
					FROM stream
					LEFT JOIN user ON stream.fk_user = user.id
					LEFT JOIN country ON user.fk_country = country.id
					LEFT JOIN rank ON user.fk_rank = rank.id
					WHERE stream.fk_user = :ID');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetchAll(true);
				$streams = array();
				foreach ($data as &$entry)
				{
					$stream = new Stream(false);
					$stream->setID($entry['streamID']);
					$stream->setTitle($entry['streamTitle']);
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
					$rank->setId($entry['rankID']);
					$rank->setTitle(DB::fromDB($entry['rankTitle']));
					$user->setRank($rank);
					$stream->setOwner($user);
					$streams[] = $stream;
				}
				return $streams;
			}
			else
				return false;
		}

	function getFromUserNickname($nickname, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT stream.id AS streamID,
					stream.title AS streamTitle,
					user.id AS userID,
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
					FROM stream
					LEFT JOIN user ON stream.fk_user = user.id
					LEFT JOIN country ON user.fk_country = country.id
					LEFT JOIN rank ON user.fk_rank = rank.id
					WHERE user.nickname = :nickname');
				$link->bindParam(':nickname', $nickname, PDO::PARAM_INT);
				$data = $link->fetchAll(true);
				if ($data)
				{
					$streams = array();
					foreach ($data as &$entry)
					{
						$stream = new Stream(false);
						$stream->setID($entry['streamID']);
						$stream->setTitle($entry['streamTitle']);
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
						$rank->setId($entry['rankID']);
						$rank->setTitle(DB::fromDB($entry['rankTitle']));
						$user->setRank($rank);
						$stream->setOwner($user);
						$streams[] = $stream;
					}
					return $streams;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getAllStreams($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT stream.id AS streamID,
					stream.title AS streamTitle,
					user.id AS userID,
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
					FROM stream
					LEFT JOIN user ON stream.fk_user = user.id
					LEFT JOIN country ON user.fk_country = country.id
					LEFT JOIN rank ON user.fk_rank = rank.id');
				$data = $link->fetchAll(true);
				if ($data)
				{
					$streams = array();
					foreach ($data as &$entry)
					{
						$stream = new Stream(false);
						$stream->setID($entry['streamID']);
						$stream->setTitle($entry['streamTitle']);
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
						$rank->setId($entry['rankID']);
						$rank->setTitle(DB::fromDB($entry['rankTitle']));
						$user->setRank($rank);
						$this->setOwner($user);
						$stream[] = $stream;
					}
					return $streams;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeTitle($newTitle, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE stream
					SET stream.title = :title
					WHERE stream.id = :ID');
				$tmp = DB::toDB($newTitle);
				$link->bindParam(':title', $tmp, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->title = $newTitle;
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function create($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					INSERT INTO stream(title, fk_user)
					VALUE(:title, :fk_user)');
				$tmpTitle = DB::toDB($this->title);
				$link->bindParam(':title', $tmpTitel, PDO::PARAM_STR);
				$link->bindParam(':fk_user', $this->owner->ID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}

		function delete($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					DELETE
					FROM stream
					WHERE stream.id = :ID');
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}
	}
?>