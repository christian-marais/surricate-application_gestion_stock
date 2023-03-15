<?php


/*
function stop($data){
    return $data.'ok';
}

function secure(&$data,&$p=0){
    $method=['stop'];
    foreach($method as $s){
        switch(true){ 
            case(gettype($data)=="array"):
                foreach($data as $element => &$value){
                    switch(true){ 
                        case(gettype($value)=="array"):
                            $p++;
                            $data[$element][$p]=secure($value,$p); 
                            if($data[$element][$p]==null){
                                unset($data[$element][$p]);
                            }
                        break;
                        default:
                        $data[$element]=$s($value);
                    }
                }
            break;
            default:
            $data=$s($data);
        }
    }
}

$_POST['test']='t';
$f=["mouais","autantt",["attends"=>"là",['pourquoi',['donc'=>"odnc",'hola'=>"ici"],'puisque'],'eclair'],'vraiment'];
$s="oui";
$t=['huile'=>"feu"];
$h=['glace'=>$t];
$array=[
    'marchine'=>$_POST['test'],
    'chine'=>$f,
    'marc'=>$s
];
$data=$array;

secure($data);
extract($data);
var_dump($chine);
die();
*/
   require_once ('autoload/Autoload.php');
    \Surricate\Autoload::init();//on importe l'autoloader
?>

