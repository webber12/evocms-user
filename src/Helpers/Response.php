<?php
namespace EvolutionCMS\EvoUser\Helpers;

class Response
{
    public function __construct()
    {

    }

    protected static function isAjax()
    {
        //return true;
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public static function send($response = [])
    {
        if (self::isAjax()) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        } else {
            return $response;
        }
    }
}
