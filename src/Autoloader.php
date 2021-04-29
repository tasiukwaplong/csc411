<?php
/**
 * @author: @tasiukwaplong
 */
require 'Config/config.php';

spl_autoload_register(function($className){
    $dir = @scandir(__DIR__);
    // if (gettype($dir) !== 'array') die("$className class not found");
    for ($i=0; $i < count($dir); $i++) { 
        $file = resolve_path($dir[$i], $className);

        if (file_exists($file.'.php')){
            require_once $file.'.php';
        }

        if($className === 'Database'){
            require_once 'Models/Database/Database.php';
        }
    }
});

if (file_exists(__DIR__.'\libs\vendor\autoload.php')) {
    // if using composer include composer autoloader
    require __DIR__.'\libs\vendor\autoload.php';
}

function resolve_path($dir, $className){
    $path = __DIR__.'\\'.$dir.'\\'.$className;
    return str_replace('\\','/',$path);
}
