<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
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
		private $db;

		function __construct($db = true, $mail = null, $password = null, $user = null)
		{
			$this->setMail($mail);
			$this->setPassword($password);
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
			if ($link)
			{
				$link->prepare('
					SELECT client.id AS clientID,
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
				if ($data)
				{
					$this->user = new User();
					$this->user->setID($data['clientID']);
					$this->user->setEmail(DB::fromDB($data['clientEmail']));
					$this->user->setPassword($data['clientPassword']);
					$this->user->setName(DB::fromDB($data['clientName']));
					$this->user->setRegisterDate($data['clientRegisterDate']);
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function generateJWT()
		{
			if ($this->connect())
			{
				$signer = new Sha256();
				$token = (new Builder())->setIssuer('http://api.twicecast.com')
										->setAudience('http://twicecast.com')
										->setId('4f1g23a12aa')
										->setIssuedAt(time())
										->setNotBefore(time())
										->setExpiration(time() + 3600)
										->set('uid', $this->user->ID)
										->sign($signer, 'TwiceCastAPIKeyForJWT')
										->getToken();
				return $token;
			}
			else
				return false;
		}

		function verify()
		{
			$headers = getallheaders();
			if (isset($headers['Authorization']))
			{
				$jwt = str_replace("Bearer ", "", $headers['Authorization']);
				return $this->verifyJWT($jwt);
			}
			else
				return false;
		}

		function verifyJWT($token)
		{
			$signer = new Sha256();
			try
			{
				$token = (new Parser())->parse((string) $token);
				if ($token->verify($signer, 'TwiceCastAPIKeyForJWT'))
				{
					$data = new ValidationData();
					$data->setIssuer('http://api.twicecast.com');
					$data->setAudience('http://twicecast.com');
					$data->setId('4f1g23a12aa');
					return $token->validate($data);
				}
				else
					return false;
			}
			catch(Exception $e)
			{
				return false;
			}
		}
	}
?>