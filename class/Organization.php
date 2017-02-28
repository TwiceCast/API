<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/BD.php');

	class Organization
	{
		var $ID;
		var $title;
		var $lang;
		var $private;

		function __construct($db = true, $ID = null, $title = null, $lang = null, $private = null)
		{
			$this->setID($ID);
			$this->setTitle($title);
			$this->setLang($lang);
			$this->setPrivate($private);
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

		function setLang($lang)
		{
			$this->lang = $lang;
			return $this;
		}

		function setPrivate($private)
		{
			$this->lang = $private;
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
					SELECT organization.id AS organizationID,
					organization.title AS organizationTitle,
					organization.lang AS organizationLang,
					organization.private AS organizationPrivate
					FROM organization
					WHERE organization.id = :ID');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['organizationID']);
					$this->setTitle($data['organizationTitle']);
					$this->setLang($data['organizationLang']);
					$this->setPrivate($data['organizationPrivate']);
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
					SELECT organization.id AS organizationID,
					organization.title AS organizationTitle,
					organization.lang AS organizationLang,
					organization.private AS organizationPrivate
					FROM organization
					WHERE organization.title = :title');
				$link->bindParam(':title', $title, PDO::PARAM_STR);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['organizationID']);
					$this->setTitle($data['organizationTitle']);
					$this->setLang($data['organizationLang']);
					$this->setPrivate($data['organizationPrivate']);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getAllOrganizations($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT organization.id AS organizationID,
					organization.title AS organizationTitle,
					organization.lang AS organizationLang,
					organization.private AS organizationPrivate
					FROM organization');
				$data = $link->fetchAll(true);
				if ($data)
				{
					$organizations = array();
					foreach ($data as &$entry)
					{
						$organization = new Organization(false);
						$organization->setID($entry['countryID']);
						$organization->setTitle($entry['countryTitle']);
						$organization->setLang($entry['countryLang']);
						$organization->setPrivate($entry['countryPrivate']);
						$organizations[] = $organization;
					}
					return $organizations;
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
					UPDATE organization
					SET organization.title = :title
					WHERE organization.id = :ID');
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

		function changeLang($newLang, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE organization
					SET organization.lang = :lang
					WHERE organization.id = :ID');
				$tmp = DB::toDB($newLang);
				$link->bindParam(':lang', $tmp, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->lang = $newLang;
					return true;
				}
				else
					return false
			}
			else
				return false;
		}

		function changePrivate($newPrivate, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE organization
					SET organization.private = :private
					WHERE organization.id = :ID');
				$link->bindParam(':private', $newPrivate, PDO::PARAM_INT);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->private = $newPrivate;
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function togglePrivate($db = null)
		{
			$this->changePrivate(!$this->private, $db);
		}

		function create($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					INSERT INTO organization(title, lang)
					VALUE(:title, :lang)');
				$tmpTitle = DB::toDB($this->title);
				$tmpLang = DB::toDB($this->lang);
				$link->bindParam(':title', $tmpTitle, PDO::PARAM_STR);
				$link->bindParam(':lang', $tmpLang, PDO::PARAM_STR);
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
					FROM organization
					WHERE organization.id = :ID');
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}
	}
?>