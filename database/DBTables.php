<?php


class DBTables extends DataBase
{
    public function __construct()
    {
    }

    public static function initialTables(){
        echo self::createStringsTable()."\n";
        echo self::createColorsTable()."\n";
        echo self::createLanguagesTable()."\n";
        echo self::createCategoriesTable()."\n";
        echo self::createSubCategoriesTable()."\n";
        echo self::createInterfaceKeysTable()."\n";
    }

    private static function tableResponse($result, $name){
        if($result === false || $result === NULL){
            return "table $name not created";
        }else{
            return "table $name created successfully";
        }
    }

    public static function createCategoriesTable(){
        $categories = "CREATE TABLE IF NOT EXISTS 
        thursday_categories(
            id int(11) AUTO_INCREMENT primary key,
            name int(11),
            image varchar (255) NOT NULL DEFAULT ''
        )ENGINE=InnoDB";
        $response = self::getInstance()->exec($categories);
        return  self::tableResponse($response,'thursday_categories');
    }

    public static function createSubCategoriesTable(){
        $subcategories = "CREATE TABLE IF NOT EXISTS 
        thursday_subcategories(
            id int(11) AUTO_INCREMENT primary key,
            name int(11),
            image varchar (255) NOT NULL DEFAULT '',
            parent int(11),
            FOREIGN KEY (parent) REFERENCES thursday_categories(id) ON DELETE CASCADE ON UPDATE CASCADE
        )ENGINE=InnoDB";
        $response = self::getInstance()->exec($subcategories);
        return  self::tableResponse($response,'thursday_subcategories');
    }

    public static function createLanguagesTable(){
        $languages = "CREATE TABLE IF NOT EXISTS 
        thursday_languages(
            id int(11) AUTO_INCREMENT primary key,
            name varchar (255) unique key NOT NULL DEFAULT ''
        ) ENGINE=InnoDB";
        $response = self::getInstance()->exec($languages);
        return  self::tableResponse($response,'thursday_languages');
    }

    public static function createStringsTable(){
        $strings = "CREATE TABLE IF NOT EXISTS 
        thursday_strings(
            id int(11) AUTO_INCREMENT primary key
        ) ENGINE=InnoDB";
        $response = self::getInstance()->exec($strings);
        return  self::tableResponse($response,'thursday_strings');
    }

    public static function createColorsTable(){
        $colors = "CREATE TABLE IF NOT EXISTS 
        thursday_colors(
            id int(11) AUTO_INCREMENT primary key,
            col_key varchar (255) NOT NULL DEFAULT '',
            value varchar (255) NOT NULL DEFAULT ''
        )ENGINE=InnoDB";
        $response = self::getInstance()->exec($colors);
        return self::tableResponse($response,'thursday_colors');
    }

    public static function createInterfaceKeysTable(){
        $keys = "CREATE TABLE IF NOT EXISTS 
        thursday_keys(
            id int(11) AUTO_INCREMENT primary key,
            col_key varchar (255) unique key NOT NULL DEFAULT '',
            string_id int (11),
            FOREIGN KEY (string_id) REFERENCES thursday_strings(id) ON DELETE CASCADE ON UPDATE CASCADE
        )ENGINE=InnoDB";
        $response = self::getInstance()->exec($keys);
        return self::tableResponse($response,'thursday_keys');
    }

}