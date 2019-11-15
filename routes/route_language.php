<?php
$full_uri = $_SERVER['REQUEST_URI'];
$uri = preg_split('/\?/', $full_uri);
$route_arr = preg_split('/\//', $uri[0]);
$path = $route_arr[2];

$responseCode = new Responses();

if (!$path) {
    $db = DataBase::getInstance();
    $dbLang = new DBLanguage();
    $dbStr = new DBStrings();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (count($_GET) > 0) {
                $responseCode->response_400();
            } else {
                try {
                    $list = $dbLang->getLanguages();
                    echo json_encode($list);
                } catch (Exception $e) {
                    $responseCode->response_400($e->getMessage());
                }
            }
            break;
        case 'POST':
            if ($_POST['name']) {
                $lang = $_POST['name'];
                try {
                    $db->beginTransaction();
                    $dbLang->addNewLanguage($lang);
                    $newData = $dbLang->getLanguageById($db->lastInsertId());
                    $dbStr->addColumn($lang);
                    $db->commit();
                    echo json_encode(['id' => $newData['id'], 'name' => $newData['name']]);
                } catch (Exception $e) {
                    $db->rollBack();
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
                    try {
                        $id = $_PUT['id'];
                        $newName = $_PUT['name'];
                        $db->beginTransaction();
                        $oldData = $dbLang->getLanguageById($id);
                        $dbLang->editLanguage($id, $newName);
                        $dbStr->editColumn($oldData['name'], $newName);
                        $response = $dbLang->getLanguageById($id);
                        echo json_encode(['name'=>$response['name'],'id'=>$response['id']]);
                        $db->commit();
                    } catch (Exception $e) {
                        $db->rollBack();
                        $responseCode->response_400($e->getMessage());
                    }
                } else {
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
                        $db->beginTransaction();
                        $oldData = $dbLang->getLanguageById($id);
                        $dbLang->deleteLanguage($id);
                        $dbStr->deleteColumn($oldData['name']);
                        $db->commit();
                        echo json_encode(true);
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
    $db->NULL;
} else {
    $responseCode->response_404();
}