<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Prenium.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

    $response = new Response(Response::OK);
    try {
        $postdata = getPostData();

        if (isset($_GET['accept']))
            $response->setContentType($_GET['accept']);
        $auth = new Authentication();
        $auth->verify();

        if (!isset($_GET['USER']) && isset($_GET['DURATION']))
            throw new ParametersException("Missing parameters", Response::MISSPARAM);
        if ($_GET['DURATION'] <= 0)
            throw new ParametersException("Duration must be positive", Response::MISSPARAM);
        
        $user = $auth->getUserFromToken();
        if (!$user)
            throw new UnknownException("Something wrong happened");
    
        if ($_GET['USER'] != $user->id)
            throw new RightsException("Wrong user");
        
        $prenium = new Prenium();
        if (!$prenium->removePrenium($user->id, $_GET['DURATION']))
            throw new UnknownException("Something wrong happened");
        $response->setMessage("Prenium refound.");
    } catch (CustomException $e) {
        $response->setError($e);
    } finally {
        $response->send();
    }
?>