<?php


class DBLocale extends DataBase
{
    public function __construct()
    {
    }

    public static function getLocale($lang,$admin)
    {
        $lang = self::getExistLanguage($lang);
        function getSql($lang)
        {
            return "SELECT k.id, k.col_key as 'key', s.$lang as 'value'  
                    FROM thursday_keys k INNER JOIN thursday_strings s 
                    ON s.id = k.string_id
                    WHERE s.$lang IS NOT NULL";
        }

        try {
            if(!$admin) {
                $sql = getSql($lang);
                $locale = self::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                if (!$locale) {
                    $sql = getSql('eng');
                    $locale = self::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                }
            }else{
                $sql =  "SELECT id, col_key as 'key', string_id FROM thursday_keys";
                $locale = self::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                for($i=0;$i<count($locale);$i++){
                    $strId = $locale[$i]['string_id'];
                    unset($locale[$i]['string_id']);
                    $strResult = self::getInstance()->query("SELECT * FROM thursday_strings WHERE id = $strId")->fetch(PDO::FETCH_ASSOC);
                    unset($strResult['id']);
                    $locale[$i]['value'] = $strResult;
                }

            }
            return $locale;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function addLocale($col_key, $value)
    {
        $params = self::parseKeyValue($value,false);
        $keys = $params['keys'];
        $values = $params['values'];

        try {
            self::getInstance()->exec("INSERT INTO thursday_strings($keys) VALUES($values)");
            self::getInstance()->exec("INSERT INTO thursday_keys(col_key, string_id) VALUES('$col_key', LAST_INSERT_ID())");
            $locale = self::getInstance()->query("SELECT id, col_key as 'key', string_id FROM thursday_keys WHERE id = LAST_INSERT_ID()")->fetch(PDO::FETCH_ASSOC);
            $locId = $locale['string_id'];
            unset($locale['string_id']);
            $strings = self::getInstance()->query("SELECT $keys FROM thursday_strings WHERE id = $locId")->fetch(PDO::FETCH_ASSOC);
            $locale['value'] = $strings;
            return $locale;
        } catch (Exception $e) {
            throw new Exception("Get locale error");
        }
    }

    public static function editLocale($key, $value, $id)
    {
        $params = self::parseKeyValue($value, 's');
        $keys = $params['keys'];
        $values = $params['values'];
        $update = $params['update'];
        try {
            self::getInstance()->exec("UPDATE thursday_keys k, thursday_strings s 
                                                SET k.col_key='$key', $update 
                                                WHERE k.id = $id AND s.id = k.string_id");
            $locale = self::getInstance()->query("SELECT id, col_key as 'key', string_id FROM thursday_keys WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
            $locId = $locale['string_id'];
            unset($locale['string_id']);
            $strings = self::getInstance()->query("SELECT $keys FROM thursday_strings WHERE id = $locId")->fetch(PDO::FETCH_ASSOC);
            $locale['value'] = $strings;
            return $locale;
        } catch (Exception $e) {
            throw new Exception('Editing locale error');
        }
    }

    public static function deleteLocale($id)
    {
        try {
            $locale = self::getInstance()->query("SELECT string_id FROM thursday_keys WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
            $locId = $locale['string_id'];
           $resLoc = self::getInstance()->exec("DELETE FROM thursday_keys WHERE id=$id");
           if (!$resLoc) new Exception();
           $resStr = self::getInstance()->exec("DELETE FROM thursday_strings WHERE id=$locId");
            if (!$resStr)new Exception();
                return true;
        } catch (Exception $e) {
            throw new Exception('Deleting request error');
        }

    }
}