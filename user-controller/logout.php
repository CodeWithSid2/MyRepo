<?php
// Initialize the session
session_start();

    unset($_SESSION["staff_name"]);
    $_SESSION["loggedin"] = false;

    unset($_SESSION[FM_SESSION_ID]['logged']);

    unset($_SESSION[filemanager]);


    $_SESSION = array();
    session_destroy();
// Redirect to login page
    header("location: /");
    exit;
?>
