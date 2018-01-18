<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Prenium.php');

    $response = new Response(Response::OK);
    try {
        if (isset($_GET['accept']))
            $response->setContentType($_GET['accept']);

        $auth = new Authentication();
        $auth->verify(false);
        $user = $auth->getUserFromToken();

        if (!isset($_GET['id']))
            throw new ParametersException("Missing parameters", Response::MISSPARAM);
        
        $prenium = new Prenium();
        $prenium->getFromUserId($_GET['id']);
        $isPrenium = $prenium->isPrenium();
        $rep = new stdClass();

        if ($user && $_GET['id'] == $user->id)
        {
            $rep->prenium = $isPrenium;
            if ($isPrenium)
                $rep->prenium_until = $prenium->preniumUntil;
        }
        else
        {
            $rep->prenium = $isPrenium;
        }
        $response->setMessage($rep); 
    } catch (CustomException $e) {
        $response->setError($e);
    } finally {
        $response->send();
    }
?>