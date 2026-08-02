<?php 

$host = 'SEUSITE';
$usuario = 'SEUSUARIO';
$senha = 'SUASENHA';
$database = 'SUABASE';

$mysqli = new mysqli($host, $usuario, $senha, $database);

if($mysqli -> connect_error){
    die("Falha ao conectar ao BD: " . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

?>