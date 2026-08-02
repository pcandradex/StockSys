<?php
// Incluir o arquivo de conexão com o banco de dados
include('conn.php');
include('protect.php');

header('Content-Type: application/json');

// Verificar se o ID do produto foi fornecido
if(isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $result = $mysqli->query("SELECT * FROM produtos WHERE id = " . $id);

    if($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
        echo json_encode($produto);
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Produto não encontrado']);
    }
} else {
    http_response_code(400);
    echo json_encode(['erro' => 'ID do produto não fornecido']);
}
?>
