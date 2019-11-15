<?php

$full_uri = $_SERVER['REQUEST_URI'];
$uri = preg_split('/\?/', $full_uri);
$route_arr = preg_split('/\//', $uri[0]);
$path = $route_arr[2];

$dbCategory = new DBCategories();
$responseCode = new Responses();

if (ctype_digit($path)) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            try {
                $lang = 'eng';
                $admin = false;
                if ($_GET['lang']) $lang = $_GET['lang'];
                if ($_GET['admin']) $admin = $_GET['admin']==='true'?true:false;
                $result = $dbCategory->getCategoryById($path, $lang, $admin);
                echo $result;
            } catch (Exception $e) {
                $responseCode->response_400($e->getMessage());
            }
            break;
        default:
            $responseCode->response_405();
    }
} else if (!$path) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $lang = 'eng';
            $admin = false;
            if ($_GET['lang']) $lang = $_GET['lang'];
            if ($_GET['admin']) $admin = $_GET['admin']==='true'?true:false;
            try {
                $result = $dbCategory->getCategories($lang, $admin);
                echo $result;
            } catch (Exception $e) {
                $responseCode->response_400($e->getMessage());
            }
            break;
        case 'POST':
            if ($_POST['name']) {
                $name = $_POST['name'];
                try {
                    $result = $dbCategory->addCategory($name);
                    echo json_encode($result, true);
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
                if ($_PUT['id'] && $_PUT['name']) {
                    $id = $_PUT['id'];
                    $name = $_PUT['name'];
                    try{
                       echo $dbCategory->editCategory($id,$name);
                    }catch (Exception $e){
                        $responseCode->response_400($e->getMessage());
                    }
                } else {
                    $responseCode->response_400();
                }
            } else $responseCode->response_400('Incorrect content-type');
            break;
        case 'DELETE':
            $_DELETE = getPutDeleteData();
            if ($_DELETE !== false) {
                if($_DELETE['id']){
                    try {
                        $id = $_DELETE['id'];
                        $res = $dbCategory->deleteCategory($id);
                        echo $res;
                    } catch (Exception $e) {
                        $responseCode->response_400($e->getMessage());
                    }
                }else{
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