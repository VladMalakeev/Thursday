<?php

class DBStrings extends DataBase
{
    public function __construct()
    {}

    public static function addString($params){
       $keys = '';
       $values = '';
        foreach ($params as $key=>$val){
            if(!ctype_alpha($key))throw new Exception('Language KEY must be string');
            $keys .= $key.', ';
            $values .= $val.', ';
        }
        try {
        $sql = "INSERT INTO thursday_strings($keys) VALUES($values)";
        $response = self::getInstance()->exec($sql);
        if(self::checkResponse($response)){
            return self::getInstance()->lastInsertId();
        }else return false;
        }catch (Exception $e){
            throw new Exception('No new strings were created.');
        }
    }

    public static function editString($params, $id){
        $values = '';
        foreach ($params as $key=>$val){
            if(!ctype_alpha($key))throw new Exception('Language KEY must be string');
            $values .= "$key=$val, ";
        }
        try {
            $sql = "UPDATE thursday_strings SET $values WHERE id=$id";
            $response = self::getInstance()->exec($sql);
            if(self::checkResponse($response)){
                return self::getInstance()->lastInsertId();
            }else return false;
        }catch (Exception $e){
            throw new Exception('Error editing data.');
        }
    }

    public static function deleteString($id){
        if(!ctype_digit($id))throw new Exception('ID must be integer');
        try{
            $sql = "DELETE FROM thursday_strings WHERE id=$id";
            self::getInstance()->exec($sql);
        }catch (Exception $e){
            throw new Exception('Deleting error');
        }
    }

    public static function addColumn($lang){
        if(!ctype_alpha($lang))throw new Exception('NAME must be string');
        try {
            $sql = "ALTER TABLE thursday_strings ADD $lang varchar(255) NOT NULL DEFAULT ''";
            $response = self::getInstance()->exec($sql);
            return self::checkResponse($response);
        }catch (Exception $e){
            throw new Exception('New table not created');
        }
    }

    public static function editColumn($old, $new){
        if(!ctype_alpha($old))throw new Exception('NAME must be string');
        if(!ctype_alpha($new))throw new Exception('NAME must be string');
        try{
        $sql = "ALTER TABLE thursday_strings CHANGE $old $new varchar(255) NOT NULL DEFAULT ''";
        $response = self::getInstance()->exec($sql);
        return self::checkResponse($response);
        }catch (Exception $e){
            throw new Exception('Editing table error');
        }
    }

    public static function deleteColumn($lang){
        if(!ctype_alpha($lang))throw new Exception('NAME must be string');
        $sql = "ALTER TABLE thursday_strings DROP COLUMN $lang";
        try {
            $response = self::getInstance()->exec($sql);
            return self::checkResponse($response);
        }catch (Exception $e){
            throw new Exception('Deleting table error');
        }
    }
}