<?php namespace Makson;
require_once $_SERVER["DOCUMENT_ROOT"]."/components/DB.php";

if ($_POST['type'] == 'logout') {
    session_start();
    unset($_SESSION['session_username']);
    session_destroy();

    echo 'Session successfully destroyed';
}