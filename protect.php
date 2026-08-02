<?php

session_save_path('/tmp');

if(!isset($_SESSION)){
    session_start();
}

if(!isset($_SESSION['id'])){
    die("Você não pode acessar por não estar logado. <p><a href=\"index.php\">Entrar</a></p>");
}
?>