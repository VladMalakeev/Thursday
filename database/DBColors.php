<?php

class DBColors extends DataBase
{
    public function __construct()
    {}

    public static function getColors(){
        try{
           $colors = self::getInstance()->query("SELECT * FROM thursday_colors")->fetchAll(PDO::FETCH_ASSOC);
            return $colors;
        }catch (Exception $e){
            throw new Exception('Color request error');
        }
    }

    public static function addColor($key, $value){
        try{
            self::getInstance()->exec("INSERT INTO thursday_colors(col_key, value) VALUES('$key','$value')");
            $color = self::getInstance()->query("SELECT * FROM thursday_colors WHERE id=LAST_INSERT_ID()")->fetch(PDO::FETCH_ASSOC);
            return $color;
        }catch (Exception $e){
            throw new Exception('Color additing error');
        }
    }

    public static function editColor($key, $value, $id){
        try{
            self::getInstance()->exec("UPDATE thursday_colors SET col_key='$key', value='$value' WHERE id=$id");
            $color = self::getInstance()->query("SELECT * FROM thursday_colors WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
            return $color;
        }catch (Exception $e){
            throw new Exception('Color additing error');
        }
    }

    public static function deleteColor($id){
        try{
           $delete = self::getInstance()->exec("DELETE FROM thursday_colors WHERE id=$id");
            if(!$delete) new Exception();
            return true;
        }catch (Exception $e){
            throw new Exception('Color deleting error');
        }
    }
}