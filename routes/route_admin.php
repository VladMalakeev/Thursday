<?php
$full_uri = $_SERVER['REQUEST_URI'];
$uri = preg_split('/\?/',$full_uri);
$route_arr = preg_split('/\//',$uri[0]);
$path = $route_arr[2];
$responseCode = new Responses();
if($path == 'initial'){
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $dbTables = new DBTables();
        $dbTables->initialTables();
    }else  $responseCode->response_405();
}
else{
    $responseCode->response_404();
}