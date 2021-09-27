<?php
require 'config.php';
 if(!empty($_GET["login"]) && !empty($_GET["password"])){
    $login = $_GET['login'];
    $pass = $_GET['password'];
    $data = status_log($login, $pass);
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Expose-Headers: Content-Length,Content-Type,Date,Server,Connection');
    header('Content-Type: application/json');
    echo json_encode($data);
}
?>