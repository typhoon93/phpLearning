<?php
    $dsn ='mysql:host=localhost;dbname=dbeebwid894tma';
    $username = 'utm4syvvbgfak';
    $password = 'uery84kgipar';

    try {
        $db = new PDO($dsn, $username, $password);
    } catch (PDOException $e){
        $error_message = 'Database error: ';
        $error_message .= $e->getMessage();
        echo $error_message;
        exit();
    }

