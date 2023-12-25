<?php

session_start();

spl_autoload_register(function ($className) {

    $foldersWithClasses = ["./libs"];

    foreach ($foldersWithClasses as $folder) {
        $potentialFileName = "$folder/$className.php";
        if (file_exists($potentialFileName)) {
            require_once $potentialFileName;
            break;
        }
    }
});