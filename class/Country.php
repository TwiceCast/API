<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'./class/DB.php');

	class Country
	{
		var $ID;
		var $code;
		var $name;
		private $db;

		function __construct($db = true, $ID = null, $code = null, $name = null)
		{
			$this->setID($ID);
			$this->setCode($code);
			$this->setName($name);
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

		function setCode($code)
		{
			$this->code = $code;
			return $this;
		}

		function setName($name)
		{
			$this->name = $name;
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
					SELECT country.id AS countryID,
					country.code AS countryCode,
					country.name AS countryName
					FROM country
					WHERE country.id = :ID');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['countryID']);
					$this->setCode($data['countryCode']);
					$this->setName($data['countryName']);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getFromCode($code, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT country.id AS countryID,
					country.code AS countryCode,
					country.name AS countryName
					FROM country
					WHERE country.code = :code');
				$link->bindParam(':code', $code, PDO::PARAM_STR);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['countryID']);
					$this->setCode($data['countryCode']);
					$this->setName($data['countryName']);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getFromName($name, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT country.id AS countryID,
					country.code AS countryCode,
					country.name AS countryName
					FROM country
					WHERE country.name = :name');
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['countryID']);
					$this->setCode($data['countryCode']);
					$this->setName($data['countryName']);
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
					SELECT country.id AS countryID,
					country.code AS countryCode,
					country.name AS countryName
					FROM country');
				$data = $link->fetchAll(true);
				if ($data)
				{
					$countrys = array();
					foreach ($data as &$entry)
					{
						$country = new Country(false);
						$country->setID($entry['countryID']);
						$country->setCode($entry['countryCode']);
						$country->setName($entry['countryName']);
						$countrys[] = $country;
					}
					return $countrys;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeCode($newCode, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE country
					SET country.code = :code
					WHERE country.id = :ID');
				$tmp = DB::toDB($newCode);
				$link->bindParam(':code', $tmp, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->code = $newCode;
					return true;
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
					UPDATE country
					SET country.name = :name
					WHERE country.id = :ID');
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
					INSERT INTO country(code, name)
					VALUE(:code, :name)');
				$tmpCode = DB::toDB($this->code);
				$tmpName = DB::toDB($this->name);
				$link->bindParam(':code', $tmpCode, PDO::PARAM_STR);
				$link->bindParam(':name', $tmpName, PDO::PARAM_STR);
				return $link->execute(true);
			}
			else
				return false;
		}

		function delete($db = null)
		{
			$linik = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					DELETE
					FROM country
					WHERE country.id = :ID');
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}
	}
?>