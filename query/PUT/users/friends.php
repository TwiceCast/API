<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Friend.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');

    $response = new Response(Response::OK);
    try {
        if (isset($_GET['accept']))
            $response->setContentType($_GET['accept']);
        $authentication = new Authentication();
        $authentication->verify();
        if (!isset($_GET['id']) || !isset($_GET['friend']))
            throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
        $user = $authentication->getUserFromToken();

        if (!$user)
            throw new UnknownException("Something wrong happened", Response::UNKNOWN);
        if ($user->id != $_GET['id'])
            throw new RightsException("You cannot modify someone else's friends", Response::NORIGHT);
        $friend = new User();
        if (!$friend->getFromId($_GET['friend']))
            throw new NotFoundException("The 'friend' user doesn't exist", Response::NOTFOUND);

        $f = new Friend();
        if (!$f->addFriendById($user->id, $friend->id))
            throw new UnknownException("Something wrong happened", Response::UNKNOWN);
        
        $response->setMessage($user, Response::CREATED);
    } catch (CustomException $e) {
        $response->setError($e);
    } finally {
        $response->send();
    }
?>