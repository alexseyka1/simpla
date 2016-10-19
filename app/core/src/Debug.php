<?php
/**
 * Created by PhpStorm.
 * User: alexseyka1
 * Date: 13.10.16
 * Time: 12:17
 */

namespace Simpla\Core;


class Debug
{
    static function arrDump($arr){
        try{
            if(empty($arr))
                throw new \Exception('Не определен аргумент 1 в классе ' .__CLASS__);
            if(!is_array($arr) && !is_object($arr))
                throw new \Exception('Переданный агрумент не массив и не объект!');
            if(is_array($arr)){
                echo '<pre>';
                print_r($arr);
                echo '</pre>';
            }elseif(is_object($arr)){
                echo '<pre>';
                var_dump($arr);
                echo '</pre>';
            }
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }
}