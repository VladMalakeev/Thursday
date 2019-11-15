<?php

class DBLanguage extends DataBase
{
    public function __construct()
    {}

    public static function addNewLanguage($name){
        if(!ctype_alpha($name))throw new Exception('NAME must be string');
        try {
            $sql = "INSERT INTO thursday_languages(name) VALUES('$name')";
            $response = self::getInstance()->exec($sql);
            return self::checkResponse($response);
        }catch (Exception $e){
            throw new Exception('Error creating data');
        }
    }

    public static function getLanguageById($id){
        if(!ctype_digit($id))throw new Exception('ID must be integer');
        try {
            $sql = "SELECT * FROM thursday_languages WHERE id=$id";
            return self::getInstance()->query($sql)->fetch(PDO::FETCH_LAZY);
        }catch (Exception $e){
            throw new Exception('Error getting data, maybe ID is not exist');
        }
    }
    public static function getLanguages(){
        try{
        $sql = "SELECT * FROM thursday_languages";
        $response = self::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $response;
        }catch (Exception $e){
            throw new Exception('Error getting list of languages');
        }
    }

    public static function editLanguage($id, $name){
        if(!ctype_digit($id))throw new Exception('ID must be integer');
        if(!ctype_alpha($name))throw new Exception('NAME must be string');
        try {
            $sql = "UPDATE thursday_languages SET name='$name' WHERE id=$id";
            $response = self::getInstance()->exec($sql);
            return $response;
        }catch (Exception $e){
            throw new Exception('Error editing data, maybe ID is not exist');
        }
    }

    public static function deleteLanguage($id){
        if(!ctype_digit($id))throw new Exception('ID must be integer');
        try {
        $sql = "DELETE FROM thursday_languages WHERE id=$id";
        $response = self::getInstance()->exec($sql);
        return $response;
        }catch (Exception $e){
            throw new Exception('Error deleting data, maybe ID is not exist');
        }
    }

}