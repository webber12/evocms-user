<?php
namespace EvolutionCMS\EvoUser\Helpers;

class Response
{
    public function __construct()
    {
        //
    }

    protected static function isAjax()
    {
        //return true;
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public static function send($response = [])
    {
        return response()->json($response);
        /*if (self::isAjax()) {
            return response()->json($response);
        } else {
            return $response;
        }*/
    }
}
