<?php
/**
 * @author Summer
 * @time 2019-06-10 23:35
 */
function route_class(){
    return str_replace('.','-',Route::currentRouteName());
}