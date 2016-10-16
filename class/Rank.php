<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'./class/DB.php');

	class Rank
	{
		var $ID;
		var $title;
		private $db;

		function __construct($db = true, $ID = null, $title = null)
		{
			$this->setID($ID);
			$this->setTitle($$title);
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

		function getLink($db)
		{
			if ($this->db)
				return $this->$db;
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
					SELECT rank.id AS rankID,
					rank.title AS rankTitle
					FROM rank
					WHERE rank.id = :ID');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['rankID']);
					$this->setTitle(DB::fromDB($data['rankTitle']));
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
			if ($link)
			{
				$link->prepare('
					SELECT rank.id AS rankID,
					rank.title AS rankTitle
					FROM rank
					WHERE rank.title = :title');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['rankID']);
					$this->setTitle(DB::fromDB($data['rankTitle']));
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getAllRanks($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT rank.id as rankID,
					rank.title AS rankTitle
					FROM rank');
				$data = $link->fetchAll(true);
				if ($data)
				{
					$ranks = array();
					foreach ($data as &$entry)
					{
						$rank = new Rank(false);
						$rank->setID($entry['rankID']);
						$rank->setTitle(DB::fromDB($entry['rankTitle']));
						$ranks[] = $rank;
					}
					return $ranks;
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
					UPDATE rank
					SET rank.title = :title
					WHERE rank.id = :ID');
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
					INSERT INTO rank(title)
					VALUE(:title)');
				$tmp = DB::toDB($this->title);
				$link->bindParam(':title', $tmp, PDO::PARAM_STR);
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
					FROM rank
					WHERE rank.id = :ID');
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}
	}
?>