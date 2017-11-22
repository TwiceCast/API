<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');

    $response = new Response(Response::OK);
    try
    {
        $stream = new Stream();
    
        if (isset($_GET['accept']))
            $response->setContentType($_GET['accept']);

        $authentication = new Authentication();
        $authentication->verify(false);
        if (!isset($_GET['id']))
            throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
        if (!$stream->getFromID($_GET['id']))
            throw new NotFoundException("This stream id doesn't exist", Response::NOTFOUND);

        $covers = glob($_SERVER['DOCUMENT_ROOT']."/cover/".$stream->id.".*");
        if ($covers === false)
            throw new UnknownException("Something  wrong happened", Response::UNKNOWN);
        else if (empty($covers))
        {
            $covers = glob($_SERVER['DOCUMENT_ROOT']."/cover/0.*");
            if ($covers === false)
                throw new UnknownException("Something wrong happened", Response::UNKNOW);
            else if (empty($covers))
                throw new NotFoundException("Cover not found", Response::NOTFOUND); 
        }
        $path_parts = pathinfo($covers[0]);
        if ($path_parts['extension'] == 'gif')
            header('Content-Type: image/gif');
        else if ($path_parts['extension'] == 'jpeg' or $path_parts['extension'] == 'jpg')
            header('Content-Type: image/jpeg');
        else if ($part_parts['extension'] == 'png')
            header('Content-Type: image/png');
        else
            throw new UnknownException("Something wrong happened", Response::UNKNOWN);
        header('Content-Length: '.filesize($covers[0]));
        readfile($covers[0]);
        exit();
    }
    catch (CustomException $e)
    {
        $response->setError($e);
    }
    finally
    {
        $response->send();
    }
?>