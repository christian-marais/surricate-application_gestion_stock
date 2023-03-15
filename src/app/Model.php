<?php

 namespace Surricate;
 use \PDO;
 use \Exception;

abstract class Model extends DB{
    
    protected $dataToUnsetIfNull=null;
    private $dataNullToZero=['active','page','qte'];
    protected $data;
    protected $datas;
    protected $metadatas;
    private $host="localhost:3306";
    protected $connexion;
    private static $pdo;
    protected $table;
    protected $id;
    protected $stmt;
   
    protected function getConnection(){
        if(self::$pdo===null){
            $this->connexion=null;
            try{
                parent::setEnv();
                self::$pdo = new PDO('mysql:local='.$this->host.'; dbname='.$_ENV['BDD_NAME'], $_ENV['USERNAME'],$_ENV['PASSWORD'],array(PDO::ATTR_PERSISTENT    => true));
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE,ERROR_MODE);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
                self::$pdo->exec('set names utf8');
            }catch(Exception $e){
                echo'Erreur :'.$e->getMessage();
            }
        }
        $this->connexion=self::$pdo;
    }

    protected function getAllBy($champ,$valeur){
        $sql='SELECT * FROM '.$this->table.' WHERE '.$champ.' = ?';
        return $this->baseSql($sql,$champ,$valeur)->fetchAll();
    }

    protected function deleteBy($champ,$valeur){
        $sql='DELETE FROM '.$this->table.' WHERE '.$this->table.'.'.$champ.' = ?';
        return $this->baseSql($sql,$champ,$valeur)->execute(array($valeur));
    }

    protected function getAll($addSql=null,$valeurs=null){
        $sql='SELECT DISTINCT * FROM '.$this->table;
        (!empty($this->sqlViewByCenter))?$sql.=$this->sqlViewByCenter:'';
        ($addSql!=null && $valeurs!=null)?$sql.=$addSql:'';
        $query=$this->connexion->prepare($sql);
        $this->testSql($sql,$valeurs);
        $this->setDataNull($valeurs);
        (!empty($this->param)&&!empty($this->sqlViewByCenter))?$query->bindParam(':currentCentre',$this->param,PDO::PARAM_STR):'';
        $this->bindParamSearchAndOffset($query,$addSql,$valeurs);
        $query->execute();
        $this->debugSql($query);
        $this->countedRow=$query->rowCount();
        return $query->fetchAll();
    }

    protected function bindParamSearchAndOffset(&$query,&$addSql,&$valeurs,$p=1){
        if($addSql!=null && !empty($valeurs['search'])){
            $query->bindParam(':search0',$valeurs['search'][0],PDO::PARAM_STR);
            $query->bindParam(':search1',$valeurs['search'][1],PDO::PARAM_STR);
        }
         if($addSql!=null && $valeurs['page']!=null){
            $query->bindParam(':page0',$valeurs['page'][0],PDO::PARAM_INT);
            $query->bindParam(':page1',$valeurs['page'][1],PDO::PARAM_INT);
        }
    }
    
    protected function bindParams(&$query,&$sql,$stringVal=null,$intVAl=null,$p=1){
        if($sql!=null && !empty($stringVal)){
            foreach($stringVal as $string){
                $query->bindParam($p++,$string,PDO::PARAM_STR);
            }
        }
        if($sql!=null && !empty($intVal)){
            foreach($intVal as $int){
                $query->bindParam($p++,$int,PDO::PARAM_INT);
            }
        }
    }
    
    protected function countAll($addSql=null,$valeurs=null,$countSql=null){
        $sql='SELECT count(*) FROM '.$this->table;
        (!empty($countSql))?$sql.=$countSql:'';
        $sqlCondSyntaxe=(empty($countSql))?' WHERE ':' AND ';
        (!empty($addSql) && !empty($valeurs))?$sql.=$sqlCondSyntaxe.$addSql:'';
        $req=$this->connexion->prepare($sql);
        $this->setDataNull($valeurs);
        (!empty($addSql) && !empty($valeurs))?$req->execute($valeurs):$req->execute();
        return $req->fetch();
        
    }
    
    protected function setSearchAndOffset($champ1,$champ2,&$search,&$page,&$sql,&$data,$option='WHERE'){
        $this->setSearch($champ1,$champ2,$search,$sql,$data,$option);
        $this->setOffset($page,$sql,$data);
        return $this->getAll($sql,$data);
    }
  
    protected function setSearch($champ1,$champ2,&$search,&$sql,&$data,$option='AND'){
        ($search!=null)?$sql=" $option $champ1 LIKE CONCAT('%',:search0,'%') OR $champ2 LIKE CONCAT('%',:search1,'%')":$sql=null;
        ($search!=null)?$data['search']=array_merge(array($search),array($search)):"";
    }
   
    private function setOffset(&$page,&$sql,&$data){
        $data['page']=$page;
        ($page!=null)?$sql.= ' LIMIT :page0, :page1 ':'';
    }
   
    protected function getOne($sql=null){
        $sql='SELECT * FROM '.$this->table.' WHERE id_'.$this->table.' = ?';
        return $this->baseSql($sql,null,$this->id)->fetch();
    }
    
    protected function insertOne($data,$isToEncrypt=null,$key=self::ENCRYPT_SQL_KEY){
        $this->secure($data);//
        $colonnes=implode(",",array_keys($data));
        $valeurs=array_values($data);
        //$params=preg_replace('/([a-z_]+)/', '?',$colonnes); Decommenter et commanter la ligne en dessous pour revenir à une version sans encryptage
        $params=$this->enDecryptDataIfMatch($data,$isToEncrypt,$key);
        $sql= 'INSERT INTO '.$this->table.' ('.$colonnes.') VALUES ('.$params.')';
        $results=$this->baseSql($sql,null,$valeurs);
        return $this->stmt=$results;
    }

    
    protected function updateBy($id,$valeur_id,$data,$isToEncrypt=null,$key=self::ENCRYPT_SQL_KEY){
        $datas=array($id,$valeur_id,$data);
        $this->secure($datas); 
        extract($datas);
        $i=0;
        $sql="UPDATE $this->table SET";
        foreach($data as $colonne => $valeur){
          $sql.=($isToEncrypt!=null && !empty($key) && in_array($colonne,$isToEncrypt))?' '.$colonne.' = '.$this->enDecryptData('?',$key).',':' '.$colonne.' = ? ,';
          $valeurs[$i]= $valeur;
          $i++;
        }
        $valeurs[$i]= $valeur_id;
        $sql=substr($sql,0,-1);
        $sql.=" WHERE $id = ?";
        return $this->baseSql($sql,$id,$valeurs);
    }
    
    protected function getBy($champ,$valeur){
        $sql='SELECT * FROM '.$this->table.' WHERE '.$champ.' = ?';
        return $this->baseSql($sql,$champ,$valeur)->fetch();
    }

    private function baseSql($sql,$champ,$valeur){
        $data=array($champ,$valeur);
        $this->secure($data);
        extract($data);
        $this->testSql($sql,$valeur);
        $query=$this->connexion->prepare($sql);
        $this->setDataNull($valeur);
        $valeur=(is_array($valeur)?$valeur:array($valeur));
        $query->execute($valeur);
        $this->debugSql($query);
        return $query;
    }

    
    protected function secure(&$data){
        Security::sanitizeUserData($data);
    }
       
    public function __destruct(){
        $this->stmt =null;
        $this->connexion = null;
    }

    protected function setDataNull(&$datas,$search=null){
        (empty($search) && !empty($this->dataNullToZero) && is_array($this->dataNullToZero))?$search=$this->dataNullToZero:'';
        if(gettype($datas)=="array"){
            foreach($datas as $data =>$value){
                (!empty($datas[$data])|| $datas[$data]===0)?"":$datas[$data] = null;
                ($datas[$data]==null && $search!=null && in_array($data,$search) && $data!=0)?$datas[$data] = 0:"";
            }
        }
    }

    protected function unsetEmptyInsertion(&$data, $dataNames=null){
        ($dataNames==null && !empty($this->dataToUnsetIfNull))?$dataNames=$this->dataToUnsetIfNull:'';// si aucune valeur à nullifier est selectionnée elle prend la valeur renseigné par défaut dans l'objet
        foreach($dataNames as $dataName){
            if(empty($data[$dataName])){
                unset ($data[$dataName]);
            } 
        }
    }
    
    protected function getUserCentre($id){
        $sql='call get_centre(?)';
        return $this->baseSql($sql,null,$id)->fetch();
    }
     
    protected function setViewByCentre(){
        (!empty($_SESSION['id']))?$centrGroup = $this-> getUserCentre($_SESSION['id']):'';
        if(!empty($centrGroup) && strtoupper($centrGroup['id_groupe'])!=='ADMIN'){
         $this->param=$centrGroup['id_centre'];
         $this->sqlCondSyntaxe = 'AND';
         $this->t1='t1.';
         $this->t2='t2.';
        }else{
            $this->sqlViewByCenter=null;
            $this->sqlCondSyntaxe = 'WHERE';
            $this->t1='';
            $this->t2='';
        }
    }

}
?>