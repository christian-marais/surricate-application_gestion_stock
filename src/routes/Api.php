<?php
 namespace Surricate;

class API extends Controller{

    public function getUtilisation(){
        
    }
    public function generateApiRest(){
        // $data = [''=>''];
        // echo json_encode($data);  
    }

    public function articles($slug){
        if(!empty($_SESSION['login']) && $_SESSION['login'] =='logged'){
            ($_SERVER ==="api/articles/")?$this->redirection("api/articles"):"";
            (!$this->checkUserContenuPermission('API',['LECTURE']))?$this->redirection('pages/blocked'):'';
            $this->loadModel('Article');
            $articles=$this->Article->getAllArticles();
            foreach($articles as $key =>$value){
                $data[$key]=$value;
                $data['server']=$_SERVER;
            }
        }else{
            $data="{'message':'Vous n'avez pas acces a la ressource'}";
        }
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    public function readApi(){
        $curl = curl_init($url);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
        //curl_setopt($curl, CURLOPT_CAINFO,$certificatlocation);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
         $data= curl_exec($curl);
        if($data==false){
            var_dump(curl_error($curl));
        }else{
            $data=json_decode($data,true);
        }
        
        curl_close($curl);
    }

}
?>