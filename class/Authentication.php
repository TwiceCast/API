<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/config.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
	use Lcobucci\JWT\Builder;
	use Lcobucci\JWT\Signer\Hmac\Sha256;
	use Lcobucci\JWT\ValidationData;
	use Lcobucci\JWT\Parser;

	class Authentication
	{
		var $mail;
		var $password;
		var $user;
		var $token;
		private $db;

		function __construct($db = true, $mail = null, $password = null, $user = null)
		{
			$this->setMail($mail);
			$this->setPassword($password);
			$this->setUser($user);
			if ($db)
				$this->db = new DB();
			else
				$this->db = null;
		}

		function setMail($mail)
		{
			$this->mail = $mail;
			return $this;
		}

		function setPassword($password)
		{
			$this->password = $password;
			return $this;
		}

		function setUser($user)
		{
			$this->user = $user;
			return $this;
		}

		function getUserFromToken()
		{
			if ($this->token)
			{
				$this->user = new User();
				if ($this->token->hasClaim('uid'))
					$this->user->getFromId($this->token->getClaim('uid'));
				else
				{
					if ($this->token->hasClaim('resetid'))
						$this->user->getFromId($this->token->getClaim('resetid'));
					else
						return false;
				}
				return ($this->user);
			}
			return false;
		}

		function isUserId($id)
		{
			if (!$this->user)
				$this->getUserFromToken();
			return $this->user->id == $id;
		}

		function isUserName($name)
		{
			if (!$this->user)
				$this->getUserFromToken();
			return $this->user->name == $name;
		}

		function userHasRights($roleIds, $targetId, $type = "organization", $db = null)
		{
			if ($type == "organization")
			{
				$link = $this->getLink($db);
				if (!$link)
					throw new DatabaseException("Unable to connect to the database", Response::UNAVAILABLE);
				$link->prepare('
					SELECT client_role.id_role AS clientRole
					FROM client_role
					WHERE client_role.categorie_target = "Organisation"
					AND client_role.id_target = :org
					AND client_role.id_client = :user');
				$link->bindParam(':org', $targetId, PDO::PARAM_INT);
				$link->bindParam(':user', $this->user->id, PDO::PARAM_INT);
				$ret = $link->fetch(true);
				if ($ret)
				{
					if (is_array($roleIds))
						return in_array($ret['clientRole'], $roleIds);
					else
					{
						if ($roleIds == $ret['clientRole'])
							return true;
					}
				}
				else
					return false;
			}
			else if ($type == "stream")
			{
				$link = $this->getLink($db);
				if (!$link)
					throw new DatabaseException("Unabled to connect to the database", Response::UNAVAILABLE);
				$link->prepare('
					SELECT client_role.id_role AS clientRole
					FROM client_role
					WHERE client_role.categorie_target = "Stream"
					AND client_role.id_target = :stream
					AND client_role.id_client = :user');
				$link->bindParam(':stream', $targetId, PDO::PARAM_INT);
				$link->bindParam(':user', $this->user->id, PDO::PARAM_INT);
				$ret = $link->fetch(true);
				if ($ret)
				{
					if (is_array($roleIds))
						return in_array($ret['clientRole'], $roleIds);
					else
					{
						if ($roleIds == $ret['clientRole'])
							return true;
					}
				}
				else
					 return false;
			}
			else if ($type == "site")
			{
				//check for site(global) right to match $right
				return true;
			}
			else
				return false;
		}

		function getUserRights($targetId, $type = "Organisation", $db = null)
		{
			if ($type == "organization")
				$type = "Organisation";
			else if ($type == "stream")
				$type = "Stream";
			$link = $this->getLink($db);
			if (!$link)
				throw new DatabaseException("Unable to connect to the database", Response::UNAVAILABLE);
			$link->prepare('
				SELECT client_role.id_role AS clientRole
				FROM client_role
				WHERE client_role.categorie_target = :type
				AND client_role.id_target = :target
				AND client_role.id_client = :user');
			$link->bindParam(':type', $type, PDO::PARAM_STR);
			$link->bindParam(':target', $targetId, PDO::PARAM_INT);
			$link->bindParam(':user', $this->user->id, PDO::PARAM_INT);
			$ret = $link->fetchAll(true);
			if ($ret)
			{
				$rights = array();
				foreach ($ret as &$entry)
				{
					$rights[] = $entry['clientRole'];
				}
				return $rights;
			}
			else
				return array();
		}

		function getUserBann($streamId, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new DatabaseException("Unabled to connect to the database", Response::UNAVAILABLE);
			$link->prepare('
				SELECT st_bann.end AS bannEnd
				FROM st_bann
				WHERE st_bann.userid = :user AND st_bann.streamid = :stream');
			$link->bindParam(':user', $this->user->id, PDO::PARAM_INT);
			$link->bindParam(':stream', $streamId, PDO::PARAM_INT);
			$data = $link->fetch(true);
			if ($data)
				return $data['bannEnd'];
			else
				return null;
		}

		function getUserMute($streamId, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new DatabaseException("Unabled to connect to the database", Response::UNAVAILABLE);
			$link->prepare('
				SELECT st_mute.end AS muteEnd
				FROM st_mute
				WHERE st_mute.userid = :user AND st_mute.streamid = :stream');
			$link->bindParam(':user', $this->user->id, PDO::PARAM_INT);
			$link->bindParam(':stream', $streamId, PDO::PARAM_INT);
			$data = $link->fetch(true);
			if ($data)
				return $data['bannEnd'];
			else
				return null;
		}
		
		function getLink($db = null)
		{
			if ($this->db)
				return $this->db;
			else if ($db)
				return $db;
			else
				return false;
		}

		function connect($db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new DatabaseException("Unable to connect to the database", Response::UNAVAILABLE);
			$link->prepare('
				SELECT client.id AS clientId,
				client.email AS clientEmail,
				client.password AS clientPassword,
				client.name AS clientName,
				client.register_date AS clientRegisterDate
				FROM client
				WHERE client.email = :mail AND client.password = :password');
			$tmpMail = DB::toDB($this->mail);
			$tmpPassword = hash('sha256', $this->password);
			$link->bindParam(':mail', $tmpMail, PDO::PARAM_STR);
			$link->bindParam(':password', $tmpPassword, PDO::PARAM_STR);
			$data = $link->fetch(true);
			if (!$data)
				throw new ParametersException("Authentication failed", Response::MISSPARAM);
			$this->user = new User();
			$this->user->setId($data['clientId']);
			$this->user->setEmail(DB::fromDB($data['clientEmail']));
			$this->user->setPassword($data['clientPassword']);
			$this->user->setName(DB::fromDB($data['clientName']));
			$this->user->setRegisterDate($data['clientRegisterDate']);
			return true;
		}

		function generateJWT()
		{
			$config = $_SESSION["config"]["application"];
			if (!$this->connect())
				return false;
			$signer = new Sha256();
			$token = (new Builder())->setIssuer('http://api.twicecast.com')
									->setAudience('http://twicecast.com')
									->setId('4f1g23a12aa')
									->setIssuedAt(time())
									->setNotBefore(time())
									->setExpiration(time() + 3600)
									->set('uid', $this->user->id)
									->sign($signer, $config["token"])
									->getToken();
			return $token;
		}

		function generateResetToken($user)
		{
			$config = $_SESSION["config"]["application"];
			$signer = new Sha256();
			$token = (new Builder())->setIssuer('http://api.twicecast.com')
									->setAudience('http://twicecast.com')
									->setId('4f1g23a12aa')
									->setIssuedAt(time())
									->setNotBefore(time())
									->setExpiration(time() + 600)
									->set('resetid', $user->id)
									->sign($signer, $config["token"])
									->getToken();
			return $token;
		}

		function generateChatToken($stream)
		{
			$config = $_SESSION["config"]["chat"];
			$rights = $this->getUserRights($stream->id, "Stream");
			$bann = $this->getUserBann($stream->id);
			$mute = $this->getUserMute($stream->id);
			$signer = new Sha256();
			$token = (new Builder())->setIssuer('http://api.twicecast.com')
									->setAudience('http://twicecast.com')
									->setId('4f1g23a12aa')
									->setIssuedAt(time())
									->setExpiration(time() + 3600)
									->set('type', 'chat')
									->set('username', $this->user->name)
									->set('room', $stream->owner->name + "/" + $stream->title)
									->set('rights', $rights)
									->set('bannedUntil', $bann)
									->set('mutedUntil', $mute)
									->sign($signer, $config["token"])
									->getToken();
			return $token;
		}

		function generateRepositoryToken($stream)
		{
			$config = $_SESSION["config"]["repository"];
			$signer = new Sha256();
			$token = (new Builder())->setIssuer('http://api.twicecast.com')
									->setAudience('http://twicecast.com')
									->setId('4f1g23a12aa')
									->setIssuedAt(time())
									->setExpiration(time() + 3600)
									->set('type', 'repository')
									->set('username', $this->user->name)
									->set('streamername', $stream->owner->name)
									->set('streamname', $stream->title)
									->sign($signer, $config["token"])
									->getToken();
			return $token;
		}

		function verify($forceAuth = true)
		{
			$headers = array_change_key_case(getallheaders());
			if ($headers === false || !isset($headers["authorization"]))
				if ($forceAuth === true)
					throw new AuthenticationException("Authorization header not found", Response::NOTAUTH);
				else
					return true;
			$jwt = str_replace("Bearer ", "", $headers['authorization']);
			$this->verifyJWT($jwt);
		}

		function verifyJWT($token)
		{
			$config = $_SESSION["config"]["application"];
			$signer = new Sha256();
			try
			{
				$token = (new Parser())->parse((string) $token);
				$this->token = $token;
				if (!$token->verify($signer, $config["token"]))
					throw new AuthenticationException("Invalid token", Response::NOTAUTH);
				$data = new ValidationData();
				$data->setIssuer('http://api.twicecast.com');
				$data->setAudience('http://twicecast.com');
				$data->setId('4f1g23a12aa');
				if ($token->validate($data) !== true)
					throw new AuthenticationException("Invalid token", Response::NOTAUTH);
				$this->getUserFromToken();
				return true;
			}
			catch (Exception $e)
			{
				throw new AuthenticationException("Invalid token", Response::NOTAUTH, $e);
			}
			if (!$token->validate($data))
				throw new AuthenticationException("Token is expired", Response::NOTAUTH);
		}
	}
?>