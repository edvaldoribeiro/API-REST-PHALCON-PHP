<?php

class Util {

	/**
	 * Converte um objeto para array
	 * @param  object $d Objeto a ser convertido
	 * @return array    Array convertido
	 */		
    public function objectToArray($d) {
        if (is_object($d))
            $d = get_object_vars($d);

        return is_array($d) ? array_map(__METHOD__, $d) : $d;
    }

    /**
     * Converter array para objeto
     * @param  array $d Array a ser convertido
     * @return object Objeto convertido     
     */
    public function arrayToObject($d) {
        return is_array($d) ? (object) array_map(__METHOD__, $d) : $d;
    }

    /**
     * extrai os valores de uma chave de subarrays/subobjetos
     * @param  array/object  $coleção de dados para ser extraido os dados
     * @param  string $index nome do idenficador da chave do qual é para extrair os dados
     * @return array -> array com os dados da posição procurada
     */
    public function getDataFromArray($collection, $index){
    	$data = array();
    	foreach ($collection as $key => $value) {
    		if ( is_object($value)  )
    			$data[] = $value->$index;
    		else if ( is_array($value) )
    			$data[] = $value[$index];
    		else
    			return array();
    	}
    	return $data;
    }

    /**
     * Verify if exit subarray
     * @param  array $arr -> array to verify if exits subarray  
     * @return boolean     true if exist and false if not 
     */
    public function existSubArray($arr){        
        foreach ($arr as $value) 
            if (is_array($value))
              return true;        
        return false;
    }



}
?>