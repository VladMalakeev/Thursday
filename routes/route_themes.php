<?php
$full_uri = $_SERVER['REQUEST_URI'];
$uri = preg_split('/\?/', $full_uri);
$route_arr = preg_split('/\//', $uri[0]);
$path = $route_arr[2];

$responseCode = new Responses();

if (!$path) {
    $db = DataBase::getInstance();
    $dbLang = new DBThemes();
    $dbStr = new DBStrings();
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            break;
        case 'POST':
            break;
        case 'PUT':
            break;
        case 'DELETE':
            break;
        default:
            $responseCode->response_405();
    }
} else {
    $responseCode->response_404();
}