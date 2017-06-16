<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Error.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	class Organization
	{
		var $ID;
		var $name;
		private $db;

		function __construct($db = true, $ID = null, $name = null)
		{
			$this->setID($ID);
			$this->setName($name);
			if ($db)
				$this->db = new DB();
			else
				$this->db = null;
		}
		
		public function __toString()
		{
			return "organization";
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
					organization.name AS organizationName
					FROM organization
					WHERE organization.id = :ID');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['organizationID']);
					$this->setname($data['organizationName']);
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
					SELECT organization.id AS organizationID,
					organization.name AS organizationName
					FROM organization
					WHERE organization.name = :name');
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['organizationID']);
					$this->setname($data['organizationName']);
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
					organization.name AS organizationName
					FROM organization');
				$data = $link->fetchAll(true);
				if ($data)
				{
					$organizations = array();
					foreach ($data as &$entry)
					{
						$organization = new Organization(false);
						$organization->setID($entry['organizationID']);
						$organization->setname($entry['organizationName']);
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

		function getFromUserID($ID, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT organization.id AS organizationID,
					organization.name AS organizationName
					FROM or_user_role
					LEFT JOIN organization ON or_user_role.id_organization = organization.id
					WHERE or_user_role.id_user = :ID');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetchAll(true);
				if ($data)
				{
					$organizations = array();
					foreach ($data as &$entry)
					{
						$organization = new Organization(false);
						$organization->setID($entry['organizationID']);
						$organization->setName(DB::fromDB($entry['organizationName']));
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

		function getOrganizationMembers($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT client.id AS clientID,
					client.email AS clientEmail,
					client.password AS clientPassword,
					client.name AS clientName,
					client.register_data AS clientRegisterDate
					FROM or_user_role
					LEFT JOIN client ON or_user_role.id_user = client.id
					WHERE or_user_role.id_organization = :ID');
					// Possibilité d'ajouter
					// LEFT JOIN or_role ON or_user_role.id_role = or_role.id
					// pour avoir le rôle de chaque user en même temps.
					// Possibilité d'ajouter un "UNIQUE" sur le client.ID
					// pour ne pas avoir plusieurs fois le même member dans
					// le cas de multiple rôle
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				$data = $link->fetchAll(true);
				if ($data)
				{
					$clients = array();
					foreach ($data as &$entry)
					{
						$client = new User(false);
						$client->setID($entry['clientID']);
						$client->setEmail(DB::fromDB($entry['clientEmail']));
						$client->setPassword($entry['clientPassword']);
						$client->setName(DB::fromDB($entry['clientName']));
						$client->setRegisterDate($entry['clientRegisterDate']);
						$clients[] = $client;
					}
					return $clients;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeName($newname, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE organization
					SET organization.name = :name
					WHERE organization.id = :ID');
				$tmp = DB::toDB($newname);
				$link->bindParam(':name', $tmp, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->name = $newname;
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
				if ($this->checkForCreation($link) == ERR::OK)
				{
					$link->prepare('
						INSERT INTO organization(name)
						VALUE(:name)');
					$tmpname = DB::toDB($this->name);
					$link->bindParam(':name', $tmpname, PDO::PARAM_STR);
					if ($link->execute(true))
						return $this->getFromID($link->link->lastInsertId());
					else
						return false;
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
			if (!$link)
				throw new UnknownException('Something wrong append', Response::UNKNOWN);
			$link->prepare('
				SELECT organization.id AS organizationID
				FROM organization
				WHERE organization.name = :name');
			$link->bindParam(':name', $this->name, PDO::PARAM_STR);
			$data = $link->fetchAll(true);
			if ($data)
				throw new ParametersException("Organization name already in use", Response::ORGNAMEUSED);
		}

		function createRole($name, $desc = "", $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					INSERT INTO or_role(name, description, id_organization)
					VALUE (:name, :desc, :id)');
				$name = DB::toDB($name);
				$desc = DB::toDB($desc);
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$link->bindParam(':desc', $desc, PDO::PARAM_STR);
				$link->bindParam(':id', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
					return $link->link->lastInsertId();
				else
					return false;
			}
			else
				return false;
		}

		function addPrivForRole($roleID, $privID, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					INSERT INTO or_role_privilege(id_role, id_privilege) VALUE (:role, :priv)');
				$link->bindParam(':role', $roleID, PDO::PARAM_INT);
				$link->bindParam(':priv', $privID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}

		function removePrivForRole($roleID, $privID, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					DELETE FROM or_role_privilege
					WHERE or_role_privilege.id_role = :role AND or_role_privilege.id_privilege = :priv');
				$link->bindParam(':role', $roleID, PDO::PARAM_INT);
				$link->bindParam(':priv', $privID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}

		function togglePrivForRole($roleID, $privID, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT 1
					FROM or_role_privilege
					WHERE or_role_privilege.id_role = :role ADN or_role_privilege.id_privilege = :priv');
				$link->bindParam(':role', $roleID, PDO::PARAM_INT);
				$link->bindParam(':priv', $privID, PDO::PARAM_INT);
				$ret = $link->fetch(true);
				if ($ret)
					return addPrivForRole($roleID, $privID, $link);
				else
					return removePrivForRole($roleID, $privID, $link);
			}
			else
				return false;
		}

		function addRoleToUser($roleID, $userID, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					INSERT INTO or_user_role(id_user, id_role, id_organization)
					VALUES(:user, :role, :org)');
				$link->bindParam(':user', $userID, PDO::PARAM_INT);
				$link->bindParam(':role', $roleID, PDO::PARAM_INT);
				$link->bindParam(':org', $this->ID, PDO::PARAM_INT);
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
				//Ajouter la suppression des rôles pour l'organization en question
				return $link->execute(true);
			}
			else
				return false;
		}
	}
?>