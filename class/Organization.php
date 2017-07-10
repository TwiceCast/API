<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	class Organization
	{
		var $id;
		var $name;
		var $language;
		var $private;
		private $db;

		function __construct($db = true, $id = null, $name = null, $language = null, $private = null)
		{
			$this->setId($id);
			$this->setName($name);
			$this->setLanguage($language);
			$this->setPrivate($private);
			if ($db)
				$this->db = new DB();
			else
				$this->db = null;
		}
		
		public function __toString()
		{
			return "organization";
		}

		function setId($id)
		{
			$this->id = $id;
			return $this;
		}

		function setName($name)
		{
			$this->name = $name;
			return $this;
		}

		function setLanguage($language)
		{
			if (is_object($language))
				$code = $language->code;
			else if (is_array($language))
				$code = $language['code'];
			else
				$code = $language;
			$this->language = new stdClass();
			$this->language->code = $code;
			return $this;
		}
		
		function setPrivate($private)
		{
			$this->private = (bool) $private;
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

		function getFromId($id, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT organization.id AS organizationId,
					organization.name AS organizationName,
					organization.language AS organizationLanguage,
					organization.private AS organizationPrivate
					FROM organization
					WHERE organization.id = :id');
				$link->bindParam(':id', $id, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setId($data['organizationId']);
					$this->setName(DB::fromDB($data['organizationName']));
					$this->setLanguage(DB::fromDB($data['organizationLanguage']));
					$this->setPrivate($data['organizationPrivate']);
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
					SELECT organization.id AS organizationId,
					organization.name AS organizationName,
					organization.language AS organizationLanguage,
					organization.private AS organizationPrivate
					FROM organization
					WHERE organization.name = :name');
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setId($data['organizationId']);
					$this->setName(DB::fromDB($data['organizationName']));
					$this->setLanguage(DB::fromDB($data['organizationLanguage']));
					$this->setPrivate($data['organizationPrivate']);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getAllOrganizations($limit = null, $offset = null, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$query = '
					SELECT organization.id AS organizationId,
					organization.name AS organizationName,
					organization.language AS organizationLanguage,
					organization.private AS organizationPrivate
					FROM organization
					ORDER BY organization.id';
				if ($limit)
				{
					$limit = (int) $limit;
					if ($offset)
					{
						$offset = (int) $offset;
						$query .= " LIMIT ".$limit." OFFSET ".$offset;
					}
					else
						$query .= " LIMIT ".$limit;
				}
				$link->prepare($query);
				$data = $link->fetchAll(true);
				if ($data === false)
					return false;
				$organizations = array();
				foreach ($data as &$entry)
				{
					$organization = new Organization(false);
					$organization->setId($entry['organizationId']);
					$organization->setName(DB::fromDB($entry['organizationName']));
					$organization->setLanguage(DB::fromDB($entry['organizationLanguage']));
					$organization->setPrivate($entry['organizationPrivate']);
					$organizations[] = $organization;
				}
				return $organizations;
			}
			else
				return false;
		}

		function getFromUserId($id, $limit = null, $offset = null, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$query = '
					SELECT organization.id AS organizationId,
					organization.name AS organizationName,
					organization.language AS organizationLanguage,
					organization.private AS organizationPrivate
					FROM client_role
					LEFT JOIN organization ON client_role.id_target = organization.id
					WHERE client_role.id_client = :id AND categorie_target = "Organisation"';
				if ($limit)
				{
					$limit = (int) $limit;
					if ($offset)
					{
						$offset = (int) $offset;
						$query .= " LIMIT ".$limit." OFFSET ".$offset;
					}
					else
						$query .= " LIMIT ".$limit;
				}
				$link->prepare($query);
				$link->bindParam(':id', $id, PDO::PARAM_INT);
				$data = $link->fetchAll(true);
				if ($data === false)
					return false;
				$organizations = array();
				foreach ($data as &$entry)
				{
					$organization = new Organization(false);
					$organization->setId($entry['organizationId']);
					$organization->setName(DB::fromDB($entry['organizationName']));
					$organization->setLanguage(DB::fromDB($entry['organizationLanguage']));
					$organization->setPrivate($entry['organizationPrivate']);
					$organizations[] = $organization;
				}
				return $organizations;
			}
			else
				return false;
		}

		function getMembers($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT client.id AS clientId,
					client.email AS clientEmail,
					client.password AS clientPassword,
					client.name AS clientName,
					client.register_data AS clientRegisterDate,
					client.language AS clientLanguage,
					client.private AS clientPrivate
					FROM or_user_role
					LEFT JOIN client ON client_role.id_client = client.id
					WHERE client_role.id_organization = :id AND categorie_target = "Organisation"');
					// Possibilité d'ajouter
					// LEFT JOIN or_role ON or_user_role.id_role = or_role.id
					// pour avoir le rôle de chaque user en même temps.
					// Possibilité d'ajouter un "UNIQUE" sur le client.id
					// pour ne pas avoir plusieurs fois le même member dans
					// le cas de multiple rôle
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				$data = $link->fetchAll(true);
				if ($data)
				{
					$clients = array();
					foreach ($data as &$entry)
					{
						$client = new User(false);
						$client->setId($entry['clientId']);
						$client->setEmail(DB::fromDB($entry['clientEmail']));
						$client->setPassword($entry['clientPassword']);
						$client->setName(DB::fromDB($entry['clientName']));
						$client->setRegisterDate($entry['clientRegisterDate']);
						$client->setLanguage(DB::fromDB($data['clientLanguage']));
						$client->setPrivate($data['clientPrivate']);
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

		function changeName($newName, $db = null)
		{
			$this->checkName($newName);
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE organization
					SET organization.name = :name
					WHERE organization.id = :id');
				$tmp = DB::toDB($newName);
				$link->bindParam(':name', $tmp, PDO::PARAM_STR);
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
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

		function changeLanguage($newLanguage, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			if (is_object($newLanguage))
				$tmpLanguage = DB::toDB($newLanguage->code);
			else if (is_array($newLanguage))
				$tmpLanguage = DB::toDB($newLanguage['code']);
			else
				$tmpLanguage = DB::toDB($newLanguage);
			$link->prepare('
				UPDATE organization
				SET organization.language = :language
				WHERE organization.id = :id');
			$link->bindParam(':language', $tmpLanguage, PDO::PARAM_STR);
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			$link->execute(true);
			$this->setLanguage($newLanguage);
		}

		function changePrivate($newPrivate, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE organization
					SET organization.private = :private
					WHERE organization.id = :id');
				$link->bindParam(':private', (int) $newPrivate, PDO::PARAM_INT);
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->setPrivate($newPrivate);
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
					UPDATE organization
					SET organization.name = :name,
					organization.language = :language,
					organization.private = :private
					WHERE organization.id = :id');
				$name = DB::toDB($this->name);
				$language = DB::toDB($this->language->code);
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$link->bindParam(':language', $language, PDO::PARAM_STR);
				$link->bindParam(':private', $this->private, PDO::PARAM_INT);
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}
		
		function create($founderId = null, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$this->checkForCreation($link);
			$link->prepare('
				INSERT INTO organization(name, language)
				VALUE(:name, :language)');
			$tmpName = DB::toDB($this->name);
			$tmpLanguage = DB::toDB($this->language->code);
			$link->bindParam(':name', $tmpName, PDO::PARAM_STR);
			$link->bindParam(':language', $tmpLanguage, PDO::PARAM_STR);
			if (!$link->execute(true))
				return false;
			$this->getFromId($link->link->lastInsertId());
			if ($founderId)
				$this->addUserRole(4, $founderId, $link);			
			return true;
		}

		function checkForCreation($db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				SELECT organization.id AS organizationId
				FROM organization
				WHERE organization.name = :name');
			$link->bindParam(':name', $this->name, PDO::PARAM_STR);
			$data = $link->fetchAll(true);
			if ($data)
				throw new ParametersException("Organization name already in use", Response::ORGNAMEUSED);
		}

		function checkName($nameToCheck, $db = null)
		{
			if ($nameToCheck === $this->name)
				return true;
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				SELECT organization.id AS organizationId
				FROM organization
				WHERE organization.name = :name');
			$link->bindParam(':name', $nameToCheck, PDO::PARAM_STR);
			$data = $link->fetchAll(true);
			if ($data)
				throw new ParametersException("Name already in use", Response::NAMEUSED);
			return true;
		}

		/*
		** Functions for advanced permision system.
		** Could be usefull for future feature
		*/
		
		/*function createRole($name, $desc = "", $db = null)
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
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				if ($link->execute(true))
					return $link->link->lastInsertId();
				else
					return false;
			}
			else
				return false;
		}

		function addPrivForRole($roleId, $privId, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					INSERT INTO or_role_privilege(id_role, id_privilege) VALUE (:role, :priv)');
				$link->bindParam(':role', $roleId, PDO::PARAM_INT);
				$link->bindParam(':priv', $privId, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}

		function removePrivForRole($roleId, $privId, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					DELETE FROM or_role_privilege
					WHERE or_role_privilege.id_role = :role AND or_role_privilege.id_privilege = :priv');
				$link->bindParam(':role', $roleId, PDO::PARAM_INT);
				$link->bindParam(':priv', $privId, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}

		function togglePrivForRole($roleId, $privId, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT 1
					FROM or_role_privilege
					WHERE or_role_privilege.id_role = :role ADN or_role_privilege.id_privilege = :priv');
				$link->bindParam(':role', $roleId, PDO::PARAM_INT);
				$link->bindParam(':priv', $privId, PDO::PARAM_INT);
				$ret = $link->fetch(true);
				if ($ret)
					return removePrivForRole($roleId, $privId, $link);
				else
					return addPrivForRole($roleId, $privId, $link);
			}
			else
				return false;
		}

		function addRoleToUser($roleId, $userId, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					INSERT INTO or_user_role(id_user, id_role, id_organization)
					VALUES(:user, :role, :org)');
				$link->bindParam(':user', $userId, PDO::PARAM_INT);
				$link->bindParam(':role', $roleId, PDO::PARAM_INT);
				$link->bindParam(':org', $this->id, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}*/

		function addUserRole($roleId, $userId, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				INSERT INTO client_role(id_client, id_role, categorie_target, id_target)
				VALUE(:user, :role, "Organisation", :org)');
			$link->bindParam(':user', $userId, PDO::PARAM_INT);
			$link->bindParam(':role', $roleId, PDO::PARAM_INT);
			$link->bindParam(':org', $this->id, PDO::PARAM_INT);
			return $link->execute(true);
		}

		function removeUserRole($roleId, $userId, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				DELETE FROM client_role
				WHERE client_role.categorie_target = "Organisation"
				AND client_role.id_target = :org
				AND client_role.id_client = :user
				AND client_role.id_role = :role');
			$link->bindParam(':org', $this->id, PDO::PARAM_INT);
			$link->bindParam(':user', $userId, PDO::PARAM_INT);
			$link->bindParam(':role', $roleId, PDO::PARAM_INT);
			return $link->execute(true);
		}

		function toggleUserRole($roleId, $userId, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				SELECT 1
				FROM client_role
				WHERE client_role.categorie_target = "Organisation"
				AND client_role.id_target = :org
				AND client_role.id_client = :user
				AND client_role.id_role = :role');
			$link->bindParam(':org', $this->id, PDO::PARAM_INT);
			$link->bindParam(':user', $userId, PDO::PARAM_INT);
			$link->bindParam(':role', $roleId, PDO::PARAM_INT);
			$ret = $link->fetch(true);
			if ($ret)
				return removeUserRole($roleId, $userId, $link);
			else
				return addUserRole($roleId, $userId, $link);
		}

		function delete($db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				DELETE
				FROM organization
				WHERE organization.id = :id');
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			$link->execute(true);
			$link->prepare('
				DELETE
				FROM client_role
				WHERE client_role.categorie_target = "Organisation" AND client_role.id_target = :id');
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			$link->execute(true);
		}
	}
?>