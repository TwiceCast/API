<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Tag.php');

	class Stream
	{
		var $id;
		var $title;
		var $short_description;
		var $owner;
		var $tags;
		private $db;

		function __construct($db = true, $id = null, $title = null, $short_description = null, $owner = null, $tags = array())
		{
			$this->setID($id);
			$this->setTitle($title);
			$this->setShortDescription($short_description);
			$this->setOwner($owner);
			$this->setTags($tags);
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

		function setShortDescription($short_description)
		{
			$this->short_description = $short_description;
			return $this;
		}

		function setOwner($owner)
		{
			$this->owner = $owner;
			return $this;
		}

		function setTags($tags)
		{
			$this->tags = $tags;
			return $this;
		}

		function addTag($tag)
		{
			$this->tags[] = $tag;
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

		function getTags($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT tag.id AS tagId,
					tag.name AS tagName,
					tag.short_description AS tagShortDescription,
					tag.full_description AS tagFullDescription
					FROM st_tag
					LEFT JOIN tag ON st_tag.tagid = tag.id
					WHERE st_tag.streamid = :id');
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				$data = $link->fetchAll(true);
				if ($data !== false)
				{
					foreach ($data as &$entry)
					{
						$tag = new Tag(false);
						$tag->setId(DB::fromDB($entry['tagId']));
						$tag->setName(DB::fromDB($entry['tagName']));
						$tag->setShortDescription(DB::fromDB($entry['tagShortDescription']));
						$tag->setFullDescription(DB::fromDB($entry['tagFullDescription']));
						$this->addTag($tag);
					}
					return true;
				}
				else
					return false;
			}
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
					stream.short_description AS streamShortDescription,
					client.id AS userID,
					client.email AS userEmail,
					client.name AS userNickname,
					client.register_date AS userRegisterDate
					FROM stream
					LEFT JOIN client_role ON stream.id = client_role.id_target AND client_role.categorie_target = "stream"
					LEFT JOIN client ON client_role.id_client = client.id
					WHERE stream.id = :ID');
				$link->bindParam(':ID', $id, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['streamID']);
					$this->setTitle(DB::fromDB($data['streamTitle']));
					$this->setShortDescription(DB::fromDB($data['streamShortDescription']));
					$user = new User(false);
					$user->setId($data['userID']);
					$user->setEmail(DB::fromDB($data['userEmail']));
					$user->setName(DB::fromDB($data['userNickname']));
					$user->setRegisterDate($data['userRegisterDate']);
					$this->setOwner($user);
					$this->getTags($link);
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
				stream.short_description AS streamShortDescription,
				client.id AS userID,
				client.email AS userEmail,
				client.name AS userNickname,
				client.register_date AS userRegisterDate,
				client.language AS userLanguage,
				client.private AS userPrivate
				FROM stream
				LEFT JOIN client_role ON stream.id = client_role.id_target AND client_role.categorie_target = "stream"
				LEFT JOIN client ON client_role.id_client = client.id
				WHERE stream.name = :title AND client.id = :ID');
			$link->bindParam(':title', DB::toDB($title), PDO::PARAM_STR);
			$link->bindParam(':ID', $this->owner->id, PDO::PARAM_INT);
			$data = $link->fetch(true);
			if ($data === false)
				return false;
			$this->setID($data['streamID']);
			$this->setTitle(DB::toDB($data['streamTitle']));
			$this->setShortDescription(DB::toDB($data['streamShortDescription']));
			$user = new User(false);
			$user->setId($data['userID']);
			$user->setEmail(DB::fromDB($data['userEmail']));
			$user->setName(DB::fromDB($data['userNickname']));
			$user->setRegisterDate($data['userRegisterDate']);
			$user->setLanguage($entry['userLanguage']);
			$user->setPrivate($entry['userPrivate']);
			$this->setOwner($user);
			$this->getTags($link);
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
					stream.short_description AS streamShortDescription,
					client.id AS userID,
					client.email AS userEmail,
					client.name AS userNickname,
					client.register_date AS userRegisterDate
					FROM stream
					LEFT JOIN client_role ON stream.id = client_role.id_target AND client_role.categorie_target = "stream"
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
					$stream->setTitle(DB::toDB($entry['streamTitle']));
					$stream->setShortDescription(DB::toDB($entry['streamShortDescription']));
					$user = new User(false);
					$user->setId($entry['userID']);
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
					$stream->getTags($link);
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
					stream.short_description AS streamShortDescription,
					client.id AS userID,
					client.email AS userEmail,
					client.name AS userNickname,
					client.register_date AS userRegisterDate
					FROM stream
					LEFT JOIN client_role ON stream.id = client_role.id_target AND client_role.categorie_target = "stream"
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
						$stream->setTitle(DB::toDB($entry['streamTitle']));
						$stream->setShortDescription(DB::toDB($entry['streamShortDescription']));
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
						$stream->setTags($link);
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
				stream.short_description AS streamShortDescription,
				client.id AS userID,
				client.email AS userEmail,
				client.name AS userNickname,
				client.register_date AS userRegisterDate,
				client.language AS userLanguage,
				client.private AS userPrivate
				FROM stream
				LEFT JOIN client_role ON stream.id = client_role.id_target AND client_role.categorie_target = "stream"
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
				$stream->setTitle(DB::toDB($entry['streamTitle']));
				$stream->setShortDescription(DB::toDB($entry['streamShortDescription']));
				$user = new User(false);
				$user->setId($entry['userID']);
				$user->setEmail(DB::fromDB($entry['userEmail']));
				// $user->setPassword($entry['userPassword']);
				$user->setName(DB::fromDB($entry['userNickname']));
				// $user->setBirthdate($entry['userBirthdate']);
				$user->setRegisterDate($entry['userRegisterDate']);
				$user->setLanguage($entry['userLanguage']);
				$user->setPrivate($entry['userPrivate']);
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
				$stream->getTags($link);
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
				$link->bindParam(':ID', $this->id, PDO::PARAM_INT);
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

		function addTagToDB($tagId, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				return false;
			$link->prepare('
				INSERT INTO st_tag(streamid, tagid)
				VALUE(:streamid, :tagid)');
			$link->bindParam(':streamid', $this->id, PDO::PARAM_INT);
			$link->bindParam(':tagid', $tagId, PDO::PARAM_INT);
			if (!$link->execute(true))
				return false;
			return true;
		}

		function create($db = null)
		{
			if ($this->getFromTitle($this->title))
				throw new ParametersException("You already have a stream with this name", Response::MISSPARAMS);
			$link = $this->getLink($db);
			if (!$link)
				return false;
			$link->prepare('
				BEGIN;
				INSERT INTO stream(name, short_description)
				VALUE(:title, :short_description);
				INSERT INTO client_role(id_client, id_role, categorie_target, id_target)
				VALUE(:id_user, (SELECT id FROM role WHERE name = "Founder" AND categorie = "Stream"), "Stream", LAST_INSERT_ID());
				COMMIT;
				');
			$link->bindParam(':title', DB::toDB($this->title), PDO::PARAM_STR);
			$link->bindParam(':short_description', DB::toDB($this->short_description), PDO::PARAM_STR);
			$link->bindParam(':id_user', $this->owner->id, PDO::PARAM_INT);
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
				$link->bindParam(':ID', $this->id, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$link->prepare('
						DELETE
						FROM client_role
						WHERE id_target = :ID
						AND categorie_target = "stream"');
					$link->bindParam(':ID', $this->id, PDO::PARAM_INT);
					return $link->execute(true);
				}
				else
					return false;
			}
			else
				return false;
		}
	}
?>