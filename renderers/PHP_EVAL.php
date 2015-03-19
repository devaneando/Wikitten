<?php

function PHP_EVAL($source,$path){
    if(strpos($source, '<?php //eval')===0){
        if($HTML = include($path)){
            return $HTML;
        }
    }
}