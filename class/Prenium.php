<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');

    class Prenium
    {
        var $userId;
        var $preniumUntil;
        private $db;

        function __construct($db = true, $userId = null, $preniumUntil = null)
        {
            $this->setUserId($userId);
            $this->setPreniumUntil($preniumUntil);
            if ($db)
                $this->db = new DB();
            else
                $this->db = null;
        }

        public function __toString()
        {
            return "prenium";
        }

        function setUserId($userId)
        {
            $this->id = $id;
            return $this;
        }

        function setPreniumUntil($preniumUntil)
        {
            $this->preniumUntil = $preniumUntil;
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

        function getFromUserId($userId, $db = null)
        {
            $link = $this->getLink($db);
            if ($link)
            {
                $link->prepare('
                    SELECT client.id AS clientId,
                    client.preniumuntil AS clientPreniumUntil
                    FROM client
                    WHERE client.id = :id');
                $link->bindParam(':id', $userId, PDO::PARAM_INT);
                $data = $link->fetch(true);
                if ($data)
                {
                    $this->setUserId($data['clientId']);
                    $this->setPreniumUntil($data['clientPreniumUntil']);
                    return true;
                }
                else
                    return false;
            }
            else
                return false;
        }

        function isPrenium($db = null)
        {
            $link = $this->getLink($db);
            if ($link)
            {
                if ($this->preniumUntil)
                {
                    $today = $link->getNow();
                    if ($this->preniumUntil >= $today)
                        return true;
                    else
                        return false;
                }
                else
                    return false;
            }
            else
                return false;
        }

        function addPrenium($userId, $duration, $db = null)
        {
            $link = $this->getLink($db);
            if ($link)
            {
                $today = $link->getNow();
                if ($this->userId == null)
                    $this->getFromUserId($userId, $db = null);
                if ($this->preniumUntil == null || $this->preniumUntil < $today)
                    $link->prepare('
                        UPDATE client
                        SET client.preniumUntil = DATE_ADD(NOW(), INTERVAL :duration DAY)
                        WHERE client.id = :id');
                else
                    $link->prepare('
                        UPDATE client
                        SET client.preniumUntil = DATE_ADD(client.preniumUntil, INTERVAL :duration DAY)
                        WHERE client.id = :id');
                $link->bindParam(':duration', $duration, PDO::PARAM_INT);
                $link->bindParam(':id', $userId, PDO::PARAM_INT);
                if ($link->execute(true))
                    return $this->getFromUserId($userId);
                else
                    return false;
            }
            else
                return false;
        }

        function removePrenium($userId, $duration, $db = null)
        {
            $link = $this->getLink($db);
            if ($link)
            {
                $link->prepare('
                    UPDATE client
                    SET client.preniumUntil = DATE_SUB(client.preniumUntil, INTERVAL :duration DAY)
                    WHERE client.id = :id');
                $link->bindParam(':duration', $duration, PDO::PARAM_INT);
                $link->bindParam(':id', $userId, PDO::PARAM_INT);
                if ($link->execute(true))
                    return $this->getFromUserId($userId);
                else
                    return false;
            }
            return
                false;
        }
    }