<?php

    //VARIAVEIS

    $v = '?v='.time();
    

    //FUNÇÕES

    function checkArrayType($variable) {
        try{
            if (is_array($variable)) {
                if (array_keys($variable) !== range(0, count($variable) - 1)) return 3; //dicionario
                else return 2; //array
            } else return 1; // não é array
        } catch (Exception $e) {
            return 0; //error
        }
    }

    function e_dicionario($variable){
        return checkArrayType($variable) == 3 ? true : false;
    }

    function e_array($variable){
        return checkArrayType($variable) == 2 ? true : false;
    }

?>