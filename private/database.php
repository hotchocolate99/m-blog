<?php 
require_once 'env.php';

ini_set('display_errors',true);

function dbConnect(){

    $host = DB_HOST;
    $dbname = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASSWORD;
    $dsn = "mysql:host=$host; dbname=$dbname; charset=utf8";

    try{
    $dbh = new PDO($dsn,$user,$pass,[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
     //echo '接続OK';

    } catch(PDOException $e){
        echo 'failed to connect'. $e->getMessage();
        exit();
    }

    return $dbh;
}

