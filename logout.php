<?php
session_save_path('/tmp');

if(!isset($_SESSION)){
    session_start();
}

session_destroy();

header("Location: index.php");
?>
