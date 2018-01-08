<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

    $response = new Response(Response::OK);
    try {
        $postdata = getPostData();

        if (isset($_GET['accept']))
            $response->setContentType($_GET['accept']);
        if (!isset($_GET['reset']) and isset($postdata['password']))
            throw new ParametersException("Missing parameters", Response::MISSPARAM);
        $auth = new Authentication();
        $auth->verifyJWT($_GET['reset']);
        $user = $auth->getUserFromToken();

        if (!$user)
            throw new UnknownException("Something wrong happened");

        $user->changePassword($postdata['password']);
        $response->setMessage("The password has been changed");
    } catch (CustomException $e) {
        $response->setError($e);
    } finally {
        $response->send();
    }
?>