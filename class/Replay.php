<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Language.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Rank.php');

	class Replay
	{
		var $ID;
		var $name;
		var $description;
		var $length;
		var $spokenLanguage;
		var $visibility;
		private $db;

		function __construct($db = true, $ID = null, $name = null, $description = null, $length = null, $spokenLanguage = null, $visibility = null)
		{
			$this->setID($ID);
			$this->setName($name);
			$this->setDescription($description);
			$this->setLength($length);
			$this->setSpokenLanguage($spokenLanguage);
			$this->setVisibility($visibility);
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

		function setName($name)
		{
			$this->name = $name;
			return $this;
		}

		function setDescription($description)
		{
			$this->description = $description;
			return $this;
		}

		function setLength($length)
		{
			$this->length = $length;
			return $this;
		}

		function setSpokenLanguage($spokenLanguage)
		{
			$this->spokenLanguage = $spokenLanguage;
			return $this;
		}

		function setVisibility($visibility)
		{
			$this->visibility = $visibility;
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
					SELECT replay.id AS replayID,
					replay.name AS replayName,
					replay.description AS replayDescription,
					replay.length AS replayLength,
					language.id AS languageID,
					language.name AS languageName,
					rank.id AS rankID,
					rank.title AS rankTitle
					FROM replay
					LEFT JOIN language ON replay.fk_spoken_language = language.id
					LEFT JOIN rank ON replay.fk_visibility = rank.id
					WHERE replay.id = :ID');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['replayID']);
					$this->setName($data['replayName']);
					$this->setDescription($data['replayDescription']);
					$this->setLength($data['replayLength']);
					$this->setSpokenLanguage(new Language($data['langageID'], $data['languageName']));
					$this->setVisibility(new Rank(false, $data['rankID'], $data['rankTitle']));
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getAllReplay($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT replay.id AS replayID,
					replay.name AS replayName,
					replay.description AS replayDescription,
					replay.length AS replayLength,
					language.id AS languageID,
					langauge.name AS languageName,
					rank.id AS rankID,
					rank.title AS rankTitle
					FROM replay
					LEFT JOIN language ON replay.fk_spoken_language = language.id
					LEFT JOIN rank ON replay.fk_visivilitu = rank.id');
				$data = $link->fetchAll(true);
				if ($data)
				{
					$replays = array();
					foreach ($data as &$entry)
					{
						$replay = new Replay(false);
						$replay->setID($entry['replayID']);
						$replay->setName($entry['replayName']);
						$replay->setDescription($entry['replayDescription']);
						$replay->setLength($entry['replayLength']);
						$replay->setSpokenLanguage(new Language($entry['languageID'], $entry['languageName']));
						$replay->setVisibility(new Rank(false, $entry['rankID'], $entry['rankTitle']));
						$replays[] = $replay;
					}
					return $replays;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeName($newName, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE replay
					SET replay.name = :name
					WHERE replay.id = :ID');
				$tmp = DB::toDB($newName);
				$link->bindParam(':name', $tmp, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->name = $newName;
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
					INSERT INTO replay(fk_channel, name, description, length, fk_spoken_language, fk_visibility)
					VALUE(:fk_channel, :name, :description, :length, :fk_spoken_language, :fk_visibility)');
				$tmpName = DB::toDB($this->title);
				$tmpDescription = DB::toDB($this->description);
				$link->bindParam(':fk_channel', 1, PDO::PARAM_INT);
				$link->bindParam(':name', $tmpName, PDO::PARAM_STR);
				$link->bindParam(':description', $tmpDescription, PDO::PARAM_STR);
				$link->bindParam(':length', $this->length, PDO::PARAM_STR);
				if ($this->spokenLanguage)
					$link->bindParam(':fk_spoken_language', $this->spokenLanguage->ID, PDO::PARAM_INT);
				else
					$link->bindParam(':fk_spoken_language', 1, PDO::PARAM_INT);
				if ($this->visibility)
					$link->bindParam(':fk_visibility', $this->rank->ID, PDO::PARAM_INT);
				else
					$link->bindParam(':fk_visibility', 1, PDO::PARAM_INT);
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
					FROM replay
					WHERE replay.id = :ID');
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}
	}
?>