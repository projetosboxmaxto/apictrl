<?php
namespace App\Http\Service;

class ErrorsService{
    
    var $errors;
    
    function __construct() {
        $this->errors = array();
    }
    function add($code, $msg){
        $this->errors[ count($this->errors)] = array("code"=>$code, "msg" => $msg );
    }
    
    
    function has($code){
       $erros =  $this->errors;
       
       for ( $i = 0; $i < count($erros); $i++ ){
           if ( $erros[$i]["code"]==$code )
               return true;
       }
       
       return false;
    }
    
    function first($code){
       $erros =  $this->errors;
         for ( $i = 0; $i < count($erros); $i++ ){
           if ( $erros[$i]["code"]==$code )
               return $erros[$i]["msg"];
         }
         
         return "";
        
    }
    
    function hasErrors(){
        return count($this->errors);
    }
    
    
    
} 