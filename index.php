<?php


if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ./main/");
    exit;
  }


  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION['staff_name'] == null ){
    header("location: ./user-controller/login");
    exit;
   }

