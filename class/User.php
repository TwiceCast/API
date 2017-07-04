<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');

	class User
	{
		var $id;
		var $email;
		private $password;
		var $name;
		var $register_date;
		var $language;
		var $private;
		private $db;

		function __construct($db = true, $id = null, $email = null, $password = null, $name = null, $register_date = null, $language = null)
		{
			$this->setId($id);
			$this->setEmail($email);
			$this->setPassword($password);
			$this->setName($name);
			$this->setRegisterDate($register_date);
			$this->setLanguage($language);
			if ($db)
				$this->db = new DB();
			else
				$this->db = null;
		}
		
		public function __toString() {
			return "user";
		}

		function setId($id)
		{
			$this->id = $id;
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

		function setName($name)
		{
			$this->name = $name;
			return $this;
		}

		function setRegisterDate($register_date)
		{
			$this->register_date = $register_date;
			return $this;
		}

		function setLanguage($language)
		{
			if (is_object($language))
				$code = $language->code;
			else
				$code = $language;
			$this->language = new stdClass();
			$this->language->code = $code;
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
					SELECT client.id AS clientId,
					client.email AS clientEmail,
					client.password AS clientPassword,
					client.name AS clientName,
					client.register_date AS clientRegisterDate,
					client.language AS clientLanguage
					FROM client
					WHERE client.id = :id');
				$link->bindParam(':id', $id, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setId($data['clientId']);
					$this->setEmail(DB::fromDB($data['clientEmail']));
					$this->setPassword($data['clientPassword']);
					$this->setName(DB::fromDB($data['clientName']));
					$this->setRegisterDate($data['clientRegisterDate']);
					$this->setLanguage(DB::fromDB($data['clientLanguage']));
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
					SELECT client.id AS clientId,
					client.email AS clientEmail,
					client.password AS clientPassword,
					client.name AS clientName,
					client.register_date AS clientRegisterDate,
					client.language AS clientLanguage
					FROM client
					WHERE client.name = :name');
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setId($data['clientId']);
					$this->setEmail(DB::fromDB($data['clientEmail']));
					$this->setPassword($data['clientPassword']);
					$this->setName(DB::fromDB($data['clientName']));
					$this->setRegisterDate($data['clientRegisterDate']);
					$this->setLanguage(DB::fromDB($data['clientLanguage']));
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getAllUsers($limit = null, $offset = null, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$query = '
					SELECT client.id AS clientId,
					client.email AS clientEmail,
					client.password AS clientPassword,
					client.name AS clientName,
					client.register_date AS clientRegisterDate,
					client.language AS clientLanguage
					FROM client
					ORDER BY client.Id';
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
				$clients = array();
				foreach ($data as &$entry)
				{
					$client = new User(false);
					$client->setId($entry['clientId']);
					$client->setEmail(DB::fromDB($entry['clientEmail']));
					$client->setPassword($entry['clientPassword']);
					$client->setName(DB::fromDB($entry['clientName']));
					$client->setRegisterDate($entry['clientRegisterDate']);
					$client->setLanguage(DB::fromDB($entry['clientLanguage']));
					$clients[] = $client;
				}
				return $clients;
			}
			return false;
		}

		function changeEmail($newEmail, $db = null)
		{
			$this->checkEmail($newEmail);
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				UPDATE client
				SET client.email = :email
				WHERE client.id = :id');
			$tmp = DB::toDB($newEmail);
			$link->bindParam(':email', $tmp, PDO::PARAM_STR);
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			$link->execute(true);
			$this->email = $newEmail;
		}

		function changePassword($newPassword, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				UPDATE client
				SET client.password = :password
				WHERE client.id = :id');
			$password = hash('sha256', $newPassword);
			$link->bindParam(':password', $password, PDO::PARAM_STR);
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			$link->execute(true);
			$this->password = $newPassword;
		}

		function changeName($newName, $db = null)
		{
			$this->checkName($newName);
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				UPDATE client
				SET client.name = :name
				WHERE client.id = :id');
			$tmp = DB::toDB($newName);
			$link->bindParam(':name', $tmp, PDO::PARAM_STR);
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			$link->execute(true);
			$this->name = $newName;
		}

		function changeLanguage($newLanguage, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				UPDATE client
				SET client.language = :language
				WHERE client.id = :id');
			$tmp = DB::toDB($newLanguage);
			$link->bindParam(':language', $tmp, PDO::PARAM_STR);
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			$link->execute(true);
			$this->setLanguage($newLanguage);
		}

		function update($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE client
					SET client.email = :email,
					client.password = :password,
					client.name = :name,
					client.language = :language
					WHERE client.id = :id');
				$email = DB::toDB($this->email);
				$password = hash('sha256', $this->password);
				$name = DB::toDB($this->name);
				$language = DB::toDB($this->language->code);
				$link->bindParam(':email', $email, PDO::PARAM_STR);
				$link->bindParam(':password', $password, PDO::PARAM_STR);
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$link->bindParam(':language', $language, PDO::PARAM_STR);
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}

		function create($db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$this->checkForCreation($link);
			$link->prepare('
					INSERT INTO client(email, password, name, language)
					VALUE (:email, :password, :name, :language)');
			$tmpEmail = DB::toDB($this->email);
			$tmpName = DB::toDB($this->name);
			$tmpPassword = hash('sha256', $this->password);
			$tmpLanguage = DB::toDB($this->language->code);
			$link->bindParam(':email', $tmpEmail, PDO::PARAM_STR);
			$link->bindParam(':password', $tmpPassword, PDO::PARAM_STR);
			$link->bindParam(':name', $tmpName, PDO::PARAM_STR);
			$link->bindParam(':language', $tmpLanguage, PDO::PARAM_STR);
			if (!$link->execute(true))
				return false;
			$this->getFromName($this->name);
			return true;
		}

		function checkForCreation($db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				SELECT client.id AS clientId
				FROM client
				WHERE client.name = :name');
			$link->bindParam(':name', $this->name, PDO::PARAM_STR);
			$data = $link->fetchAll(true);
			if ($data)
				throw new ParametersException("Nickname already in use", Response::NICKUSED);
			$link->prepare('
				SELECT client.id AS clientId
				FROM client
				WHERE client.email = :email');
			$link->bindParam(':email', $this->email, PDO::PARAM_STR);
			$data = $link->fetchAll(true);
			if ($data)
				throw new ParametersException("Email already in use", Response::EMAILUSED);
			return true;
		}

		function checkName($nameToCheck, $db = null)
		{
			if ($nameToCheck === $this->name)
				return true;
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				SELECT client.id AS clientId
				FROM client
				WHERE client.name = :name');
			$link->bindParam(':name', $nameToCheck, PDO::PARAM_STR);
			$data = $link->fetchAll(true);
			if ($data)
				throw new ParametersException("Nickname already in use", Response::NICKUSED);
			return true;
		}
		
		function checkEmail($emailToCheck, $db = null)
		{
			if ($emailToCheck === $this->email)
				return true;
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				SELECT client.id AS clientId
				FROM client
				WHERE client.email = :email');
			$link->bindParam(':email', $emailToCheck, PDO::PARAM_STR);
			$data = $link->fetchAll(true);
			if ($data)
				throw new ParametersException("Email already in use", Response::EMAILUSED);
			return true;
		}

		function delete($db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				DELETE
				FROM client
				WHERE client.id = :id');
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			return $link->execute(true);
		}
	}
?>