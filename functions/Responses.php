<?php


class Responses
{
    public function __construct()
    {}

    public static function response_400($message = "Bad Request"){
        http_response_code(400);
        echo json_encode(['message'=>$message]);
    }

    public static function response_404($message = "Not Found"){
        http_response_code(404);
        echo json_encode(['message'=>$message]);
    }

    public static function response_405($message = "Method Not Allowed"){
        http_response_code(405);
        echo json_encode(['message'=>$message]);
    }
}