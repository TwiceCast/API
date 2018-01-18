<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Block.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');

    $response = new Response(Response::OK);
    try {
        if (isset($_GET['accept']))
            $response->setContentType($_GET['accept']);
        $authentication = new Authentication();
        $authentication->verify();
        if (!isset($_GET['id']))
            throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
        $user = $authentication->getUserFromToken();

        if (!$user)
            throw new UnknownException("Something wrong happened", Response::UNKNOWN);
        if ($user->id != $_GET['id'])
            throw new RightsException("You cannot access someone else's blocks", Response::NORIGHT);
        $b = new Block();
        $blocks = $b->getFromId($user->id);

        $rep = new stdClass();
        if ($blocks === false)
        {
            $rep->user_list = null;
            $rep->user_total = 0;
        }
        else
        {
            $rep->user_list = $blocks;
            $rep->user_total = count($blocks);
        }
        $response->setMessage($rep);
    } catch (CustomException $e) {
        $response->setError($e);
    } finally {
        $response->send();
    }
?>