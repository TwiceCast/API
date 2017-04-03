<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Error.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');

	class User
	{
		var $ID;
		var $email;
		private $password;
		var $name;
		var $register_date;
		private $db;

		function __construct($db = true, $ID = null, $email = null, $password = null, $name = null, $register_date = null)
		{
			$this->setID($ID);
			$this->setEmail($email);
			$this->setPassword($password);
			$this->setName($name);
			$this->setRegisterDate($register_date);
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
					SELECT client.id AS clientID,
					client.email AS clientEmail,
					client.password AS clientPassword,
					client.name AS clientName,
					client.register_date AS clientRegisterDate
					FROM client
					WHERE client.id = :ID');
				$link->bindParam(':ID', $ID, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['clientID']);
					$this->setEmail(DB::fromDB($data['clientEmail']));
					$this->setPassword($data['clientPassword']);
					$this->setName(DB::fromDB($data['clientName']));
					$this->setRegisterDate($data['clientRegisterDate']);
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
					SELECT client.id AS clientID,
					client.email AS clientEmail,
					client.password AS clientPassword,
					client.name AS clientName,
					client.register_date AS clientRegisterDate
					FROM client
					WHERE client.name = :name');
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setID($data['clientID']);
					$this->setEmail(DB::fromDB($data['clientEmail']));
					$this->setPassword($data['clientPassword']);
					$this->setName(DB::fromDB($data['clientName']));
					$this->setRegisterDate($data['clientRegisterDate']);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getAllUsers($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT client.id AS clientID,
					client.email AS clientEmail,
					client.password AS clientPassword,
					client.name AS clientName,
					client.register_date AS clientRegisterDate
					FROM client
					ORDER BY client.ID');
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
			return false;
		}

		function changeEmail($newEmail, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE client
					SET client.email = :email
					WHERE client.id = :ID');
				$tmp = DB::toDB($newEmail);
				$link->bindParam(':email', $tmp, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->email = $newEmail;
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function changePassword($newPassword, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE client
					SET client.password = :password
					WHERE client.id = :ID');
				$link->bindParam(':password', $newPassword, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				if ($link->execute(true))
				{
					$this->password = $newPassword;
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
					UPDATE client
					SET client.name = :name
					WHERE client.id = :ID');
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

		function update($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE client
					SET client.email = :email,
					client.password = :password,
					client.name = :name
					WHERE client.id = :ID');
				$email = DB::toDB($this->email);
				$name = DB::toDB($this->name);
				$link->bindParam(':email', $email, PDO::PARAM_STR);
				$link->bindParam(':password', $this->password, PDO::PARAM_STR);
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				return $link->execute(true);
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
						INSERT INTO client(email, password, name)
						VALUE (:email, :password, :name)');
					$tmpEmail = DB::toDB($this->email);
					$tmpName = DB::toDB($this->name);
					$link->bindParam(':email', $tmpEmail, PDO::PARAM_STR);
					$link->bindParam(':password', $this->password, PDO::PARAM_STR);
					$link->bindParam(':name', $tmpName, PDO::PARAM_STR);
					return $link->execute(true);
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
			if ($link)
			{
				$link->prepare('
					SELECT client.id AS clientID
					FROM client
					WHERE client.name = :name');
				$link->bindParam(':name', $this->name, PDO::PARAM_STR);
				$data = $link->fetchAll(true);
				if ($data)
					return ERR::NICKUSED;
				else
				{
					$link->prepare('
						SELECT client.id AS clientID
						FROM client
						WHERE client.email = :email');
					$link->bindParam(':email', $this->email, PDO::PARAM_STR);
					$data = $link->fetchAll(true);
					if ($data)
						return ERR::EMAILUSED;
					else
						return ERR::OK;
				}
			}
			else
				return ERR::UNKNOW;
		}

		function delete($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					DELETE
					FROM client
					WHERE client.id = :ID');
				$link->bindParam(':ID', $this->ID, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}
	}
?>