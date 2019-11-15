<?php

class DBSubcategories extends DataBase
{
    public function __construct()
    {}

    public static function getSubCategories($lang='eng', $catId, $admin){
        $lang = self::getExistLanguage($lang);
        function getSqlString($lang, $catId){
            return "SELECT sub.id, sub.image, str.$lang as name 
                    FROM thursday_subcategories sub 
                    INNER JOIN thursday_strings str
                    ON sub.name = str.id AND str.$lang IS NOT NULL 
                    WHERE parent=$catId";
        }

        try {
            if(!$admin) {
                $sql = getSqlString($lang, $catId);
                $subcategories = self::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                if (count($subcategories) == 0) {
                    $sql = getSqlString('eng', $catId);
                    $subcategories = self::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                }
            }else{
                $sql = "SELECT id, image, name FROM thursday_subcategories sub WHERE parent=$catId";
                $subcategories = self::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                if(count($subcategories)>0){
                    for($i=0;$i<count($subcategories);$i++){
                        $subId = $subcategories[$i]['name'];
                        $langArr = self::getInstance()->query("SELECT * FROM thursday_strings WHERE id=$subId")->fetch(PDO::FETCH_ASSOC);
                        unset($langArr['id']);
                        $subcategories[$i]['name'] = $langArr;
                        if( $subcategories[$i]['image']=='null')$subcategories[$i]['image']='';
                    }
                }
            }
            return json_encode($subcategories, true);
        }catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public static function getSubCategoryById($id, $lang='eng', $admin){
        $lang = self::getExistLanguage($lang);
        function getSqlString($lang, $id){
            return "SELECT sub.id, sub.image, str.$lang as name 
                    FROM thursday_subcategories sub 
                    INNER JOIN thursday_strings str
                    ON str.id = sub.name AND str.$lang IS NOT NULL 
                    WHERE sub.id=$id";
        }

        try {
            if(!$admin) {
                $sql = getSqlString($lang, $id);
                $subcategories = self::getInstance()->query($sql)->fetch(PDO::FETCH_ASSOC);
                if (!$subcategories) {
                    $sql = getSqlString('eng', $id);
                    $subcategories = self::getInstance()->query($sql)->fetch(PDO::FETCH_ASSOC);
                }
            }else{
                $sql = "SELECT id, image, name FROM thursday_subcategories  WHERE id=$id";
                $subcategories = self::getInstance()->query($sql)->fetch(PDO::FETCH_ASSOC);
                if($subcategories) {
                    $catId = $subcategories['name'];
                    $langArr = self::getInstance()->query("SELECT * FROM thursday_strings WHERE id=$catId")->fetch(PDO::FETCH_ASSOC);
                    unset($langArr['id']);
                    $subcategories['name'] = $langArr;
                    if( $subcategories['image']=='null')$subcategories['image']='';
                }
            }
            if(!$subcategories) throw new Exception();
            return json_encode($subcategories, true);
        }catch (Exception $e){
            throw new Exception('Subcategory request error');
        }
    }

    public static function addSubCategory($name, $catId){
        $params = self::parseKeyValue($name,false);
        $keys = $params['keys'];
        $values = $params['values'];
        $src = '';
        if(count($_FILES)>0) {
            $src = uploadImage('./images/subcategories/');
        }
        try {
            self::getInstance()->beginTransaction();
            $res = self::getInstance()->query("SELECT * FROM thursday_categories WHERE id=$catId")->fetch(PDO::FETCH_LAZY);
            if(!$res) throw new Exception();
            self::getInstance()->exec("INSERT INTO thursday_strings($keys) VALUES($values)");
            self::getInstance()->exec("INSERT INTO thursday_subcategories(name,image,parent) VALUES(LAST_INSERT_ID(),'$src',$catId)");
            $id = self::getInstance()->lastInsertId();
            self::getInstance()->commit();
            return ['id'=>$id,'name'=>json_decode($name),'image'=>$src];
        }catch (Exception $e){
            throw new Exception('Subcategory creating error');
        }
    }

    public static function editSubCategory($id, $name){
        $params = self::parseKeyValue($name,false);
        $keys = $params['keys'];
        $values = $params['values'];
        $updateStr = $params['update'];
        $src = '';
        try{
            self::getInstance()->beginTransaction();
            if(count($_FILES)>0) {
                $src = uploadImage('./images/subcategories/');
                $cat = self::getInstance()->query("SELECT image FROM thursday_subcategories WHERE id=$id")->fetch(PDO::FETCH_LAZY);
                if(!$cat)throw new Exception();
                if($cat['image']!=='null')$res = unlink('images/subcategories/' . $cat['image']);

                self::getInstance()->exec("UPDATE thursday_subcategories SET image='$src' WHERE id=$id");
            }
//            else{
//                self::getInstance()->exec("UPDATE thursday_subcategories SET image='null' WHERE id=$id");
//            }
            self::getInstance()->exec("UPDATE thursday_strings SET $updateStr WHERE id = (SELECT name FROM thursday_subcategories WHERE id=$id)");
            self::getInstance()->commit();
            return json_encode([
                'id'=>$id,
                'name' => json_decode($name),
                'image' => $src
            ],true);
        }catch (Exception $e){
            self::getInstance()->rollBack();
            throw new Exception('Subcategory editing error');
        }
    }

    public static function deleteSubCategory($id){
        if(!ctype_digit($id))throw new Exception('ID must be integer');
        try {
            self::getInstance()->beginTransaction();
            $sub = self::getInstance()->query("SELECT name FROM thursday_subcategories WHERE id=$id")->fetch(PDO::FETCH_LAZY);
            $subId = $sub['name'];
            self::getInstance()->exec("DELETE FROM thursday_strings WHERE id=$subId");
            self::getInstance()->exec("DELETE FROM thursday_subcategories WHERE id=$id");
            self::getInstance()->commit();
            return json_encode(true,true);
        }catch (Exception $e){
            self::getInstance()->rollBack();
            throw new Exception('Error deleting data, maybe ID is not exist');
        }
    }
}