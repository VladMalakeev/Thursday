<?php
$full_uri = $_SERVER['REQUEST_URI'];
$uri = preg_split('/\?/', $full_uri);
$route_arr = preg_split('/\//', $uri[0]);
$path = $route_arr[2];

$responseCode = new Responses();

if (!$path) {
    $db = DataBase::getInstance();
    $dbColors = new DBColors();
    $dbStr = new DBStrings();
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            try {
                $result = $dbColors->getColors();
                echo json_encode($result);
            } catch (Exception $e) {
                $responseCode->response_400($e->getMessage());
            }
            break;
        case 'POST':
            if ($_POST['value'] && $_POST['key']) {
                try {
                    $key = $_POST['key'];
                    $value = $_POST['value'];
                    $result = $dbColors->addColor($key, $value);
                    echo json_encode($result);
                } catch (Exception $e) {
                    $responseCode->response_400($e->getMessage());
                }
            } else {
                $responseCode->response_400();
            }
            break;
        case 'PUT':
            $_PUT = getPutDeleteData();
            if ($_PUT !== false) {
                if($_PUT['value'] && $_PUT['key'] && $_PUT['id']){
                    try {
                        $key = $_PUT['key'];
                        $value = $_PUT['value'];
                        $id = $_PUT['id'];
                        $result = $dbColors->editColor($key, $value, $id);
                        echo json_encode($result);
                    } catch (Exception $e) {
                        $responseCode->response_400($e->getMessage());
                    }
                }else {
                    $responseCode->response_400();
                }
            }else $responseCode->response_400('Incorrect content-type');
            break;
        case 'DELETE':
            $_DELETE = getPutDeleteData();
            if ($_DELETE !== false) {
                if($_DELETE['id']){
                    try {
                        $id = $_DELETE['id'];
                        $result = $dbColors->deleteColor($id);
                        echo json_encode($result);
                    } catch (Exception $e) {
                        $responseCode->response_400($e->getMessage());
                    }
                }else {
                    $responseCode->response_400();
                }
            }else $responseCode->response_400('Incorrect content-type');
            break;
        default:
            $responseCode->response_405();
    }
} else {
    $responseCode->response_404();
}