<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

    class Friend
    {
        var $userId;
        var $friends;
        private $db;

        function __construct($db = true, $userId = null, $friends = null)
        {
            $this->setUserId($userId);
            $this->setFriends($friends);
            if ($db)
                $this->db = new DB();
            else
                $this->db = null;
        }

        public function __toString()
        {
            return "friend";
        }

        function setUserId($userId)
        {
            $this->userId = $userId;
            return $this;
        }

        function setFriends($friends)
        {
            if ($friends == null)
                $this->friends = array();
            else if (is_array($friends))
                $this->friends = $friends;
            else if (is_object($friends))
                $this->addFriend($friends);
            return $this;
        }

        function addFriend($friend)
        {
            if (is_object($friend))
                $this->friends[] = $friend;
            else if ((int)$friend > 0)
            {
                $f = new User(false);
                if ($f->getFromId($friend, $this->db))
                    $this->friends[] = $f;
            }
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

        function getFromId($userId, $db = null)
        {
            $link = $this->getLink($db);
            if ($link)
            {
                $link->prepare('
                    SELECT DISTINCT friends.id_friend AS friendId
                    FROM friends
                    WHERE friends.id_client = :id');
                $link->bindParam(':id', $userId, PDO::PARAM_INT);
                $data = $link->fetchAll(true);
                if ($data === false)
                    return false;
                foreach ($data as $entry)
                {
                    $this->addFriend($entry['friendId']);
                }
                return $this->friends;
            }
            else
                return false;
        }

        function addFriendById($userId, $friendId, $db = null)
        {
            $link = $this->getLink($db);
            if ($link)
            {
                $link->prepare('
                    INSERT INTO friends(id_client, id_friend)
                    VALUES (:userId,:friendId)');
                $link->bindParam(':userId', $userId, PDO::PARAM_INT);
                $link->bindParam(':friendId', $friendId, PDO::PARAM_INT);
                if (!$link->execute(true))
                    return false;
                $this->getFromId($userId);
                return true;
            }
        }

        function removeFriendById($userId, $friendId, $db = null)
        {
            $link = $this->getLink($db);
            if ($link)
            {
                $link->prepare('
                    DELETE FROM friends
                    WHERE friends.id_client = :userId
                    AND friends.id_friend = :friendId');
                $link->bindParam(':userId', $userId, PDO::PARAM_INT);
                $link->bindParam(':friendId', $friendId, PDO::PARAM_INT);
                if (!$link->execute(true))
                    return false;
                $this->getFromId($userId);
                return true;
            }
        }
    }
?>