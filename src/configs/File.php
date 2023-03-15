<?php
    namespace Surricate;
    use \Exception;
   
    class  File Extends Security{
        private $file;
        private const HTACCESS_CONTENT = "Options -Indexes \n order deny,allow\n deny from all\n allow from 127.0.0.1";
        
        public function __construct(){
            self::setHtaccess();
        }

        protected function moveFile($file,$newPath){
            rename($file,ROOT.$newPath.$this->file['filename'].$this->file['extendedName'].'.'.$this->file['type']);
        }

        protected function copyFile($file,$newPath){
            copy($file,$newPath);
        }

        public function uploadFile($data,$nameForm='file'){
            if($_FILES['file']['error']!==0){
                $this->message("Veuillez joindre un fichier.");
            }else{
                Methodes::joinArray($_FILES[$nameForm],pathInfo($_FILES[$nameForm]['tmp_name']));
                Security::sanitizeUserData($this->file=$_FILES[$nameForm]);
                $this->file['extendedName'] = bin2hex(random_bytes(5));
                if($this->isExtSizeOk($this->file['name'],$data)){
                    if(move_uploaded_file($this->file['tmp_name'],$file=Sources::TEMP_FOLDER.$this->file['filename'].'.'.$this->file['type'])){
                        $this->setImageFileProperties($file);
                        $isFixedWidthForUpload=(!empty($data['check']) && $this->file['width']==$data['width'] && $this->file['height']==$data['height']);
                        $isMaxWidthForUpload=(empty($data['check']) &&$this->file['width']<=$data['width'] && $this->file['height']<=$data['height']);
                        (empty($data['check']))? $choice=' supérieure ':$choice=' inférieures ';
                        if($isFixedWidthForUpload || $isMaxWidthForUpload){
                            $this->moveFile($file,$data['path']);
                        }else{
                            $this->message('Width et height doivent être '.$choice.' à '.$this->file['width'].' et '.$this->file['height'],__FUNCTION__);
                            try{
                                if(!@unlink($file)){
                                    throw new Exception('Impossible de supprimer le fichier temporaire');
                                }
                            }catch(Exception $e){
                            }
                        }
                    }
                }else{
                    $this->message('Le fichier n\'est pas valide. Est autorisé : '.implode(',',$data['ext']).' inférieur à '.$data['size'].' ko.',__FUNCTION__);
                }
              return $this->file;
            }
            self::setHtaccess();
        }

        private function isExtSizeOk($filename,$data,$type='image'){

            $isFileError=($this->file['error']===0);
            $this->file['type']=str_replace($type.'/','',$this->file['type']);
            ($this->file['type']==='jpeg')?$this->file['type']='jpg':'';
            $isFileTypeOk=in_array($this->file['type'],$data['ext']);
            $isExtOk=(!$this->isExtIn($filename,Security::NOT_ALLOWED_EXT)&& !$this->isDoubleExt($filename) && $this->isExtIN($filename,$data['ext']));
            $isSizeOk=(($this->file['size']=ceil($this->file['size']/1000))<$data['size']);
            return ($isExtOk && $isSizeOk && $isFileError && $isFileTypeOk);
        }

        private function setImageFileProperties($file){
            $file=getimagesize($file);
            $file=array_combine(['width','height'],explode(' ',preg_replace('/([a-z])*(")*(=)*/','',$file[3])));
            $this->file['width'] = $file['width'];
            $this->file['height']=$file['height'];
           
        }

        private static function setHtaccess($content=File::HTACCESS_CONTENT, $folder = Sources::TEMP_FOLDER){ 
            Security::createHtaccess($folder,$content);
        }
    
    }
?>