<?php
header_remove(); 
class Utils 

{

  private $rut;

    
  private function __construct() { }


   public function calculaDV($_rol) 

    { 
        /* Bonus: remuevo los ceros del comienzo. */
        while($_rol[0] == "0") {
            $_rol = substr($_rol, 1);
            }
        $factor = 2;
        $suma = 0;
            for($i = strlen($_rol) - 1; $i >= 0; $i--) {
            $suma += $factor * $_rol[$i];
            $factor = $factor % 7 == 0 ? 2 : $factor + 1;
            }   
        $dv = 11 - $suma % 11;
        /* Por alguna razón me daba que 11 % 11 = 11. Esto lo resuelve. */
        $dv = $dv == 11 ? 0 : ($dv == 10 ? "K" : $dv);
       return $dv;
       // return $_rol . "-" . $dv;
        
    }
}