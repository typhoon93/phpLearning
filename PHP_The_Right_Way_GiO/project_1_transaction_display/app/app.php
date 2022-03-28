<?php

declare(strict_types = 1);

// Your Code

function get_csv_contents(string $filename){
    $file = fopen($filename , 'r');
    $contents_arr = [];
    while(($line = fgetcsv($file))!== false){
        $contents_arr[] = $line;
        
    }
    unset($contents_arr[0]); // removing description line
    $contents_arr = array_values($contents_arr); //reindexing values so they start with 0
    fclose($file);
    return $contents_arr;
}

function get_dir_files(string $directory){
   
    $files_and_dirs = scandir($directory);
    $files = [];
    foreach($files_and_dirs as $item){
        if(is_file($directory . $item)){
            $files[] = $directory . $item;
         }
    }
    
    return $files;
}


function dir_csv_contents($all_files){

    $contents = [];
    foreach($all_files as $file){

       $contents = array_merge($contents, get_csv_contents($file));

    }

    return $contents;
}