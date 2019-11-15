<?php


class DataBase
{
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            $host = 'localhost';
            $db = 'vlad_malakeev';
            $user = 'mysql';
            $pass = 'mysql';
            $charset = 'utf8';
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            );
            try {
                $db = new PDO($dsn, $user, $pass, $options);
                $db->setAttribute(PDO::NULL_NATURAL,PDO::ATTR_ORACLE_NULLS);
                $db->query("SET NAMES utf8");
                self::$instance = $db;
            }catch (PDOException $e){
                die();
            }

        }
        return self::$instance;
    }

    protected static function checkResponse($response){
        if($response !== false || $response !==NULL){
            return true;
        }else return false;
    }

    protected  static  function getExistLanguage($lang){
        $LangVal = 'eng';
        $res = self::getInstance()->query("SELECT COLUMN_NAME 
                                                    FROM INFORMATION_SCHEMA.COLUMNS
                                                    WHERE TABLE_NAME = 'thursday_strings' ")->fetchAll(PDO::FETCH_ASSOC);
       foreach ($res as $key => $val){
           if($val['COLUMN_NAME'] == $lang){
               $LangVal = $lang;
           }
       }
       return $LangVal;
    }

    protected static function parseKeyValue($json, $updPrefix){
        $keys = '';
        $values = '';
        $updateStr = '';
        $valueArr = json_decode($json);
        foreach ($valueArr as $key=>$val){
            if(!ctype_alpha($key))throw new Exception('Language KEY must be string');
            if($updPrefix){
                $updateStr = $updateStr."$updPrefix.$key='$val', ";
            }else {
                $updateStr = $updateStr . "$key='$val', ";
            }
            $keys = $keys."$key, ";
            $values = $values."'$val', ";
        }
        $values = substr($values,0,strlen($values)-2);
        $keys = substr($keys,0,strlen($keys)-2);
        $updateStr = substr($updateStr,0,strlen($updateStr)-2);
        return array('keys' => $keys, 'values' => $values, 'update' => $updateStr);
    }
}