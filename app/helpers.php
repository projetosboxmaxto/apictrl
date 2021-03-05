<?php
namespace App;


class helpers {
	
    public static function ereg_replace($sintax, $replace, $val){    
               $ret =  preg_replace_callback(
					        $sintax,
					        function ($matches) {
					            return $replace;
					        },
					        $val );

               return $ret;

     }

	public static function removeAcentos($var ){
		
		$ant = $var;
		
		// Variavel recebendo a string já fazendo as substituições
		
		$var = self::ereg_replace("[ÁÀÂÃ]","A",$var);
		
		$var = self::ereg_replace("[áàâãª]","a",$var);
		
		$var = self::ereg_replace("[ÉÈÊ]","E",$var);
		
		$var = self::ereg_replace("[éèê]","e",$var);
		
		$var = self::ereg_replace("[ÓÒÔÕ]","O",$var);
		
		$var = self::ereg_replace("[óòôõº]","o",$var);
		
		$var = self::ereg_replace("[ÚÙÛ]","U",$var);
		
		$var = self::ereg_replace("[úùû]","u",$var);
		
		$var = str_replace("Ç","C",$var);
		
		$var = str_replace("ç","c",$var);	
		
		return $var;
	}



}

?>