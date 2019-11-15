<?php

class DBCategories extends DataBase
{
    public function __construct()
    {}

    public static function getCategories($lang='eng',$admin){
        $lang = self::getExistLanguage($lang);
        function getSqlString($lang='eng'){
            return "SELECT cat.id, cat.image, str.$lang as name
                 FROM thursday_categories cat 
                 INNER JOIN thursday_strings str
                 ON cat.name = str.id AND str.$lang IS NOT NULL   
                ";
        }

        if(!ctype_alpha($lang))throw new Exception('Language KEY must be string');
        try {
            if(!$admin) {
                $sql = getSqlString($lang);
                $categories = self::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                if (count($categories) == 0) {
                    $sql = getSqlString();
                    $categories = self::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                }
            }else{
                $sql = "SELECT id, image, name FROM thursday_categories";
                $categories = self::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                if(count($categories)>0){
                    for($i=0;$i<count($categories);$i++){
                        $catId = $categories[$i]['name'];
                        $langArr = self::getInstance()->query("SELECT * FROM thursday_strings WHERE id=$catId")->fetch(PDO::FETCH_ASSOC);
                       unset($langArr['id']);
                        $categories[$i]['name'] = $langArr;
                        if( $categories[$i]['image']=='null')$categories[$i]['image']='';
                    }
                }
            }
            return json_encode($categories,true);
        }catch (Exception $e){
            throw new Exception('Category request error.');
        }
    }

    public static function getCategoryById($id, $lang='eng',$admin){
        $lang = self::getExistLanguage($lang);
        function getSqlString($lang='eng', $id){
            return "SELECT cat.id, cat.image, str.$lang  as name
                 FROM thursday_categories cat
                 INNER JOIN thursday_strings str
                 ON cat.name = str.id AND str.$lang IS NOT NULL 
                 WHERE cat.id=$id ";
        }

        if(!ctype_digit($id))throw new Exception('ID must be integer');
        if(!ctype_alpha($lang))throw new Exception('Language KEY must be string');
        try {
            if(!$admin) {
                $sql = getSqlString($lang, $id);
                $categories = self::getInstance()->query($sql)->fetch(PDO::FETCH_ASSOC);
                if (!$categories) {
                    $sql = getSqlString('eng', $id);
                    $categories = self::getInstance()->query($sql)->fetch(PDO::FETCH_ASSOC);
                }
            }else{
                $sql = "SELECT id, image, name FROM thursday_categories  WHERE id=$id ";
                $categories = self::getInstance()->query($sql)->fetch(PDO::FETCH_ASSOC);
                if($categories){
                        $catId = $categories['name'];
                        $langArr = self::getInstance()->query("SELECT * FROM thursday_strings WHERE id=$catId")->fetch(PDO::FETCH_ASSOC);
                        unset($langArr['id']);
                        $categories['name'] = $langArr;
                    if( $categories['image']=='null')$categories['image']='';
                }
            }
            if (!$categories) throw new Exception('Category ID not exist.');

            return json_encode($categories,true);
        }catch (Exception $e){
            throw new Exception('Category request error.');
        }
    }

    public static function addCategory($name){
        $params = self::parseKeyValue($name,false);
        $keys = $params['keys'];
        $values = $params['values'];
        $src = '';
        if(count($_FILES)>0) {
            $src = uploadImage('./images/categories/');
        }
        try {
            self::getInstance()->beginTransaction();
            self::getInstance()->exec("INSERT INTO thursday_strings($keys) VALUES($values)");
            self::getInstance()->exec("INSERT INTO thursday_categories(name,image) VALUES(LAST_INSERT_ID(),'$src')");
            $id = self::getInstance()->lastInsertId();
            self::getInstance()->commit();

            return ['id'=>$id,'name'=>json_decode($name),'image'=>$src];
        }catch (Exception $e){
            self::getInstance()->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public static function editCategory($id, $name){
        $params = self::parseKeyValue($name,false);
        $keys = $params['keys'];
        $values = $params['values'];
        $updateStr = $params['update'];
        $src = '';
        try{
            self::getInstance()->beginTransaction();
            if(count($_FILES)>0) {
                $src = uploadImage('./images/categories/');
                $cat = self::getInstance()->query("SELECT image FROM thursday_categories WHERE id=$id")->fetch(PDO::FETCH_LAZY);
                if(!$cat)throw new Exception();
                  if($cat['image']!=='null') $res = unlink('images/categories/' . $cat['image']);

                self::getInstance()->exec("UPDATE thursday_categories SET image='$src' WHERE id=$id");
            }
//            else{
//                self::getInstance()->exec("UPDATE thursday_categories SET image='null' WHERE id=$id");
//            }
            $nameId = self::getInstance()->query("SELECT name FROM thursday_categories WHERE id=$id")->fetch(PDO::FETCH_LAZY)['name'];
            if(!$nameId) throw new Exception();
            self::getInstance()->exec("UPDATE thursday_strings SET $updateStr WHERE id = $nameId ");
            self::getInstance()->commit();
            return json_encode([
                'id'=>$id,
                'name' => json_decode($name),
                'image' => $src
            ],true);
        }catch (Exception $e){
            self::getInstance()->rollBack();
            throw new Exception('Category editing error');
        }
    }

    public static function deleteCategory($id){
        if(!ctype_digit($id))throw new Exception('ID must be integer');
        try {
            self::getInstance()->beginTransaction();
           $cat = self::getInstance()->query("SELECT name FROM thursday_categories WHERE id=$id")->fetch(PDO::FETCH_LAZY);
           $catId = $cat['name'];
            self::getInstance()->exec("DELETE FROM thursday_strings WHERE id=$catId");
            self::getInstance()->exec("DELETE FROM thursday_categories WHERE id=$id");
            self::getInstance()->commit();
            return json_encode(true,true);
        }catch (Exception $e){
            self::getInstance()->rollBack();
            throw new Exception('Error deleting data, maybe ID is not exist');
        }
    }

}