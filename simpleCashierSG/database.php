<?php
    $dsn ='mysql:host=localhost;dbname=db1ypcblc4hy3m';
    $username = 'u67rjjd69hd3b';
    $password = 'uery84kgipar';

    try {
        $db = new PDO($dsn, $username, $password);
    } catch (PDOException $e){
        $error_message = 'Database error: ';
        $error_message .= $e->getMessage();
        echo $error_message;
        exit();
    }

