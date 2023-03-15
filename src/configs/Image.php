<?php 
namespace Surricate;

class Image{
    public static function getImage(){
        header("content-type:images/jpeg");
        return file_get_contents(ROOT.'images/banque/patou.jpg');
    }
}

?>