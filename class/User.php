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
		var $gender;
		var $birthdate;
		var $biography;
		var $github;
		var $linkdin;
		private $db;

		function __construct($db = true, $id = null, $email = null, $password = null, $name = null, $register_date = null, $language = null, $private = null, $gender = null, $birthdate = null, $biography = null, $github = null, $linkdin = null)
		{
			$this->setId($id);
			$this->setEmail($email);
			$this->setPassword($password);
			$this->setName($name);
			$this->setRegisterDate($register_date);
			$this->setLanguage($language);
			$this->setPrivate($private);
			$this->setGender($gender);
			$this->setBirthdate($birthdate);
			$this->setBiography($biography);
			$this->setGithub($github);
			$this->setLinkdin($linkdin);
			if ($db)
				$this->db = new DB();
			else
				$this->db = null;
		}
		
		public function __toString()
		{
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

		function setGender($gender)
		{
			if ($gender === null)
				$this->gender = $gender;
			else
				$this->gender = (bool) $gender;
			return $this;
		}

		function setBirthdate($birthdate)
		{
			$this->birthdate = $birthdate;
			return $this;
		}

		function setBiography($biography)
		{
			$this->biography;
			return $this;
		}

		function setGithub($github)
		{
			$this->github = $github;
			return $this;
		}

		function setLinkdin($linkdin)
		{
			$this->linkdin = $linkdin;
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
					client.language AS clientLanguage,
					client.private AS clientPrivate,
					client.gender AS clientGender,
					client.birthdate AS clientBirthdate,
					client.biography AS clientBiography,
					client.github AS clientGithub,
					client.linkdin AS clientLinkdin
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
					$this->setPrivate($data['clientPrivate']);
					$this->setGender($data['clientGender']);
					$this->setBirthdate($data['clientBirthdate']);
					$this->setBiography($data['clientBiography']);
					$this->setGithub($data['clientGithub']);
					$this->setLinkdin($data['clientLinkdin']);
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
					client.language AS clientLanguage,
					client.private AS clientPrivate,
					client.gender AS clientGender,
					client.birthdate AS clientBirthdate,
					client.biography AS clientBiography,
					client.github AS clientGithub,
					client.linkdin AS clientLinkdin
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
					$this->setPrivate($data['clientPrivate']);
					$this->setGender($data['clientGender']);
					$this->setBirthdate($data['clientBirthdate']);
					$this->setBiography($data['clientBiography']);
					$this->setGithub($data['clientGithub']);
					$this->setLinkdin($data['clientLinkdin']);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getFromEmail($email, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT client.id AS clientId,
					client.email AS clientEmail,
					client.password AS clientPassword,
					client.name AS clientName,
					client.register_date as clientRegisterDate,
					client.language AS clientLanguage,
					client.private AS clientPrivate,
					client.gender AS clientGender,
					client.birthdate AS clientBirthdate,
					client.biography AS clientBiography,
					client.github AS clientGithub,
					client.linkdin AS clientLinkdin
					FROM client
					WHERE client.email = :email');
				$link->bindParam(':email', $email, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setId($data['clientId']);
					$this->setEmail(DB::fromDB($data['clientEmail']));
					$this->setPassword($data['clientPassword']);
					$this->setName(DB::fromDB($data['clientName']));
					$this->setRegisterDate($data['clientRegisterDate']);
					$this->setLanguage(DB::fromDB($data['clientLanguage']));
					$this->setPrivate($data['clientPrivate']);
					$this->setGender($data['clientGender']);
					$this->setBirthdate($data['clientBirthdate']);
					$this->setBiography($data['clientBiography']);
					$this->setGithub($data['clientGithub']);
					$this->setLinkdin($data['clientLinkdin']);
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
					client.language AS clientLanguage,
					client.private AS clientPrivate,
					client.gender AS clientGender,
					client.birthdate AS clientBirthdate,
					client.biography AS clientBiography,
					client.github AS clientGithub,
					client.linkdin AS clientLinkdin
					FROM client
					ORDER BY client.id';
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
					$client->setPrivate($entry['clientPrivate']);
					$client->setGender($entry['clientGender']);
					$client->setBirthdate($entry['clientBirthdate']);
					$client->setBiography($entry['clientBiography']);
					$client->setGithub($entry['clientGithub']);
					$client->setLinkdin($entry['clientLinkdin']);
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
			if (is_object($newLanguage))
				$tmpLanguage = DB::toDB($newLanguage->code);
			else if (is_array($newLanguage))
				$tmpLanguage = DB::toDB($newLanguage['code']);
			else
				$tmpLanguage = DB::toDB($newLanguage);
			$link->prepare('
				UPDATE client
				SET client.language = :language
				WHERE client.id = :id');
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
					UPDATE client
					SET client.private = :private
					WHERE client.id = :id');
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

		function changeGender($newGender, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE client
					SET client.gender = :gender
					WHERE client.id = :id');
				$link->bindParam(':gender', (int) $newGender, PDO::PARAM_INT);
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->setGender($newGender);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeBirthdate($newBirthdate, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE client
					SET client.birthdate = :birthdate
					WHERE client.id = :id');
				$link->bindParam(':birthdate', $newBirthdate, PDO::PARAM_STR);
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->setBirthdate($newBirthdate);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeBiography($newBiography, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE client
					SET client.biography = :biography
					WHERE client.id = :id');
				$tmp = DB::toDB($newBiography);
				$link->bindParam(':biography', $tmp, PDO::PARAM_STR);
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->setBiography($newBiography);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeGithub($newGithub, $db = true)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE client
					SET client.github = :github
					WHERE client.id = :id');
				$tmp = DB::toDB($newGithub);
				$link->bindParam(':github', $tmp, PDO::PARAM_STR);
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->setGithub($newGithub);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changeLinkdin($newLinkdin, $db = true)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE client
					SET client.linkdin = :linkdin
					WHERE client.id = :id');
				$tmp = DB::toDB($newLinkdin);
				$link->bindParam(':linkdin', $tmp, PDO::PARAM_STR);
				$link->bindParam(':id', $this->id, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->setLinkdin($newLinkdin);
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
					UPDATE client
					SET client.email = :email,
					client.password = :password,
					client.name = :name,
					client.language = :language,
					client.gender = :gender,
					client.birthdate = :birthdate,
					client.biography = :biography,
					client.github = :github,
					client.linkdin = :linkdin
					WHERE client.id = :id');
				$email = DB::toDB($this->email);
				$password = hash('sha256', $this->password);
				$name = DB::toDB($this->name);
				$language = DB::toDB($this->language->code);
				$biography = DB::toDB($this->biography);
				$github = DB::toDB($this->github);
				$linkdin = DB::toDB($this->linkdin);
				$link->bindParam(':email', $email, PDO::PARAM_STR);
				$link->bindParam(':password', $password, PDO::PARAM_STR);
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$link->bindParam(':language', $language, PDO::PARAM_STR);
				$link->bindParam(':gender', (int) $this->gender, PDO::PARAM_INT);
				$link->bindParam(':birthdate', $this->birthdate, PDO::PARAM_INT);
				$link->bindParam(':biography', $biography, PDO::PARAM_STR);
				$link->bindParam(':github', $github, PDO::PARAM_STR);
				$link->bindParam(':linkdin', $linkdin, PDO::PARAM_STR);
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