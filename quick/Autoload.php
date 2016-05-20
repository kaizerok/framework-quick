<?php

spl_autoload_register(function($className) {
    $projectRoot = __DIR__ . '/../';
    $path = $projectRoot.str_replace('\\',  DIRECTORY_SEPARATOR,  $className).'.php';
    require $path;
});