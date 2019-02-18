<?php

if(! function_exists('dd')){
    function dd(...$vars){
        dump(...$vars);
        die;
    }
}
