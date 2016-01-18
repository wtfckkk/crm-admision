<?php
header_remove(); 
class Utils 

{

  private $rut;

    
  private function __construct() { }


   public function calculaDV($rut)
    {
        $s = 0;
    $rut = strrev($rut);
    $aux = 1;for($i=0;$i<strlen($rut);$i++){
                $aux++;
                $s += intval($rut)*$aux;
                    if($aux == 7)
                    {$aux=1;}
        }
    $digit = 11-($s%11);
    
        if($digit == 11){
            $d=0;
        }elseif($digit == 10){
            $d = "K";
        }else{
            $d = $digit;
        }
        
        return strlen($rut);                         
        
    }
}