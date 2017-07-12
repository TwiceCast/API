<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	class Stream
	{
		var $id;
		var $title;
		var $owner;
		private $db;

		function __construct($db = true, $id = null, $title = null, $owner = null)
		{
			$this->setID($id);
			$this->setTitle($title);
			$this->setOwner($owner);
			if ($db)
				$this->db = new DB();
			else
				$this->db = null;
		}
		
		public function __toString()
		{
			return "stream";
		}

		function setID($id)
		{
			$this->id = $id;
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

		function getFromID($id, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT stream.id AS streamID,
					stream.name AS streamTitle,
					client.id AS userID,
					client.email AS userEmail,
					client.name AS userNickname,
					client.register_date AS userRegisterDate
					FROM stream
					LEFT JOIN client_role ON stream.id = client_role.id_target
					LEFT JOIN client ON client_role.id_client = client.id
					WHERE stream.id = :ID');
				$link->bindParam(':ID', $id, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['streamID']);
					$this->setTitle($data['streamTitle']);
					$user = new User(false);
					$user->setID($data['userID']);
					$user->setEmail(DB::fromDB($data['userEmail']));
					$user->setName(DB::fromDB($data['userNickname']));
					$user->setRegisterDate($data['userRegisterDate']);
					$this->setOwner($user);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getFromTitle($title, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				return false;
			$link->prepare('
				SELECT stream.id AS streamID,
				stream.name AS streamTitle,
				client.id AS userID,
				client.email AS userEmail,
				client.name AS userNickname,
				client.register_date AS userRegisterDate
				FROM stream
				LEFT JOIN client_role ON stream.id = client_role.id_target
				LEFT JOIN client ON client_role.id_client = client.id
				WHERE stream.name = :title AND client.id = :ID');
			$link->bindParam(':title', $title, PDO::PARAM_STR);
			$link->bindParam(':ID', $this->owner->ID, PDO::PARAM_STR);
			$data = $link->fetch(true);
			if (!$data)
				return false;
			$this->setID($data['streamID']);
			$this->setTitle($data['streamTitle']);
			$user = new User(false);
			$user->setID($data['userID']);
			$user->setEmail(DB::fromDB($data['userEmail']));
			$user->setName(DB::fromDB($data['userNickname']));
			$user->setRegisterDate($data['userRegisterDate']);
			$this->setOwner($user);
			return true;
		}

		function getFromUserID($id, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT stream.id AS streamID,
					stream.name AS streamTitle,
					client.id AS userID,
					client.email AS userEmail,
					client.name AS userNickname,
					client.register_date AS userRegisterDate
					FROM stream
					LEFT JOIN client_role ON stream.id = client_role.id_target
					LEFT JOIN client ON client_role.id_client = client.id
					WHERE client.id = :ID');
				$link->bindParam(':ID', $id, PDO::PARAM_INT);
				$data = $link->fetchAll(true);
				if ($data === false)
					return false;
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
					stream.name AS streamTitle,
					client.id AS userID,
					client.email AS userEmail,
					client.name AS userNickname,
					client.register_date AS userRegisterDate
					FROM stream
					LEFT JOIN client_role ON stream.id = client_role.id_target
					LEFT JOIN client ON client_role.id_client = client.id
					WHERE client.name = :nickname');
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
			if (!$link)
				return false;
			$link->prepare('
				SELECT stream.id AS streamID,
				stream.name AS streamTitle,
				client.id AS userID,
				client.email AS userEmail,
				client.name AS userNickname,
				client.register_date AS userRegisterDate
				FROM stream
				LEFT JOIN client_role ON stream.id = client_role.id_target
				LEFT JOIN client ON client_role.id_client = client.id
				ORDER BY stream.id
				');
			$data = $link->fetchAll(true);
			if ($data === false)
				return false;
			$streams = array();
			foreach ($data as &$entry)
			{
				$stream = new Stream(false);
				$stream->setID($entry['streamID']);
				$stream->setTitle($entry['streamTitle']);
				$user = new User(false);
				$user->setID($entry['userID']);
				$user->setEmail(DB::fromDB($entry['userEmail']));
				// $user->setPassword($entry['userPassword']);
				$user->setName(DB::fromDB($entry['userNickname']));
				// $user->setBirthdate($entry['userBirthdate']);
				$user->setRegisterDate($entry['userRegisterDate']);
				// $user->setLastVisitDate($entry['userLastVisitDate']);
				// $country = new Country(false);
				// $country->setID($entry['countryID']);
				// $country->setCode(DB::fromDB($entry['countryCode']));
				// $country->setName(DB::fromDB($entry['countryName']));
				// $user->setCountry($country);
				// $rank = new Rank(false);
				// $rank->setId($entry['rankID']);
				// $rank->setTitle(DB::fromDB($entry['rankTitle']));
				// $user->setRank($rank);
				$stream->setOwner($user);
				$streams[] = $stream;
			}
			return $streams;
		}

		function changeTitle($newTitle, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE stream
					SET stream.name = :title
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
			if (!$link)
				return false;
			$link->prepare('
				BEGIN;
				INSERT INTO stream(name)
				VALUE(:title);
				INSERT INTO client_role(id_client, id_role, categorie_target, id_target)
				VALUE(:id_user, (SELECT id FROM role WHERE name = "Guest" AND categorie = "Stream"), "Stream", LAST_INSERT_ID());
				COMMIT
				');
			$link->bindParam(':title', DB::toDB($this->title), PDO::PARAM_STR);
			$link->bindParam(':id_user', $this->owner->ID, PDO::PARAM_INT);
			if (!$link->execute(true))
				return false;
			$this->getFromTitle($this->title);
			return true;
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