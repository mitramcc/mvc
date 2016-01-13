<?php

class Model{

    function __construct(){
        $this->db = new Database();
    }

    public function load($name)
    {
        $path = "../app/models/".$name.".php";
        if(file_exists($path)){
            require_once($path);
            $modelName = $name."_Model";
            $this->{$name} = new $modelName();

        }else{
            error_log("NO MODEL 4 ".$name);
        }

    }

    public function arrayFlatten($array, $label)
    {
        $res = [];
        foreach ($array as $value) {
            $res[] = $value[$label];
        }
        return $res;
    }

    public function execQuery($query, $execArray=[], $op="query", $driver="mysql")
    {
        try{
            if ($driver=="mysql") {
                $stm = $this->db->prepare($query);
            } else {
                $stm = $driver->prepare($query);
            }
            $data = $stm->execute($execArray);
            if ($op=="query") {
                $data = $stm->fetchAll();
            } elseif ($op=="one") {
                $data = $stm->fetch();
            } elseif ($op=="rowcount") {
                $data = $stm->rowCount();
            } elseif ($op=="lastid") {
                $data = $this->db->lastInsertId();
            }
        }catch(Exception $e){
            error_log($e->getMessage());
            throw new Exception($e->getMessage());
        }

        return $data;
    }

    //true se tiver todos parametros
    public function hasMissingParams($array, $params=[])
    {
        foreach ($params as $param) {
            if(!isset($array[$param]) || empty($array[$param]))
                return true;
        }
        return false;
    }

    public function total($values=[])
    {
        if(empty($values))
            return false;

        $qty      = isset($values["qty"]) ? $values["qty"] : 1;
        $iva      = isset($values["iva"]) ? $values["iva"] : 1;
        $taxas    = isset($values["taxas"]) ? $values["taxas"] : 0;
        if(is_int($iva*1)){
            $iva = 1+$iva/100;
        }
        $eco      = (isset($values["eco"]) ? $values["eco"] : 0) * $qty;
        $preco    = (isset($values["preco"]) ? $values["preco"] : 0) * $qty;

        $desconto = isset($values["desconto"]) ? $values["desconto"] : 0;
        return (($preco+$eco+$taxas)*$iva)-$desconto;

    }

    public function subtotal($values=[])
    {
        if(empty($values))
            return false;

        $qty      = isset($values["qty"]) ? $values["qty"] : 1;
        $eco      = (isset($values["eco"]) ? $values["eco"] : 0) * $qty;
        $preco    = (isset($values["preco"]) ? $values["preco"] : 0) * $qty;
        $taxas    = isset($values["taxas"]) ? $values["taxas"] : 0;
        return $preco+$eco+$taxas;

    }

    public function truncate($string, $limit, $break=" ", $pad="...")
    {
        if(strlen($string) <= $limit) return $string;

        $string = substr($string, 0, $limit);
        if(false !== ($breakpoint = strrpos($string, $break))) {
            $string = substr($string, 0, $breakpoint);
        }

        return $string . $pad;
    }

    public function uploadImage($file, $filename, $destination, $ignoreEmpty=true)
    {
        if( isset($file[$filename]["size"]) && $file[$filename]["size"] == 0 ){
            if($ignoreEmpty){
                return false;
            }
            throw new Exception("Nenhum ficheiro seleccionado.");

        }else{
            if(!$this->fileExists(IMAGES_PATH.$destination)){
                throw new Exception("Erro ao criar directoria.");

            }else{
                $prefix = date("YmdGis")."_";

            }
            if ($file[$filename]["error"] == UPLOAD_ERR_OK) {
                $name = $prefix.$file[$filename]["name"];
                if(move_uploaded_file( $file[$filename]["tmp_name"], IMAGES_PATH.$destination.$name)){
                    return  IMAGES_URL.$destination.$name;

                }
                else{
                    throw new Exception("Erro ao guardar o Imagem.".$name);

                }
            }else{
                throw new Exception("Erro ao guardar o Imagem.".$prefix.$file[$filename]["name"]);
            }
        }
        return false;
    }

    public function uploadFile($fileData, $filename, $destination, $ignoreEmpty=true)
    {
        ini_set('post_max_size', '20M');
        ini_set('upload_max_filesize', '20M');

        if( isset($fileData[$filename]["size"]) && $fileData[$filename]["size"] == 0 ){
            if($ignoreEmpty){
                return false;
            }
            throw new Exception("Nenhum ficheiro seleccionado.");

        }else{
            if(!$this->fileExists(ROOT."public/".$destination)){
                throw new Exception("Erro ao criar directoria.");

            }else{
                $prefix = date("YmdGis")."_";

            }

            if ($fileData[$filename]["error"] == UPLOAD_ERR_OK) {
                $name = $prefix.$fileData[$filename]["name"];
                if(move_uploaded_file( $fileData[$filename]["tmp_name"], ROOT."public/".$destination.$name)){
                    return  BASE_URL."public/".$destination.$name;

                }
                else{
                    throw new Exception("Erro ao guardar o ficheiro.".$name);

                }
            }else{
                throw new Exception("Erro ao guardar o ficheiro.".$prefix.$fileData[$filename]["name"]);
            }
        }
        die();
        return false;
    }

    public function fileExists($path)
    {
        if (!file_exists($path)) {
            if(!mkdir($path, 0775, true)){
                return false;
            }
        }
        return true;
    }

    public function deleteFile($path)
    {
        if(!$this->fileExists($path)){
            return false;
        }
        return unlink($path);
    }

    public function currency($value=0)
    {
        return number_format($value, 2)." €";
    }

    public function double($value=0)
    {
        return number_format($value, 2);
    }

    public function int($value=0)
    {
        return number_format($value, 0);
    }

    public static function isAdmin()
    {
        if(Session::is_set("CRUZ_level")){
            return ADMIN_LEVEL==Session::get("CRUZ_level");
        }else{
            return false;
        }
    }

    public static function isCliente()
    {
        if(Session::is_set("CRUZ_level")){
            return USER_LEVEL==Session::get("CRUZ_level");
        }else{
            return false;
        }
    }

    public function isLoggedIn()
    {
        return self::isAdmin() || self::isCliente();
    }

    public function basenameDir()
    {
        return basename(__DIR__);
    }

    public function debug($var, $die=false)
    {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
        if ($die) {
            die("hard");
        }
    }

}

?>