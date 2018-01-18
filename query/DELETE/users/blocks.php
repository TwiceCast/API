<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Block.php');
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
        if (!isset($_GET['id']) || !isset($_GET['block']))
            throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
        $user = $authentication->getUserFromToken();
        
        if (!$user)
            throw new UnknownException("Something wrong happened", Response::UNKNOWN);
        if ($user->id != $_GET['id'])
            throw new RightsException("You cannot modify someone else's blocks", Response::NORIGHT);
        $block = new User();
        if (!$block->getFromId($_GET['block']))
            throw new NotFoundException("the 'block' user doesn't exist", Response::NOTFOUND);

        $b = new Block();
        if (!$b->removeBlockById($user->id, $block->id))
            throw new UnknownException("Something wrong happened", Response::UNKNOWN);

        //$response->setMessage(null, Response::NOCONTENT);
        $response->setResponseCode(Response::NOCONTENT);
    } catch (CustomException $e) {
        $response->setError($e);
    } finally {
        $response->send();
    }
?>