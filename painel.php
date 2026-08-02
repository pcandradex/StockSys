<?php
session_save_path('/tmp');
include('protect.php');

// Receber o nome de usuário da sessão
$username = $_SESSION['username'];
$active = 'painel';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Painel</title>
</head>
<body class="app-page">

<?php include('includes/menu.php'); ?>

<div class="container">
    <h1 class="page-title">Olá, <?php echo htmlspecialchars($username); ?>!</h1>

    <div class="resumo-cards">
        <a href="produtos.php" class="resumo-card" style="text-decoration:none;">
            <div class="label">Produtos</div>
            <div class="valor">Ver estoque</div>
        </a>
        <a href="historico.php" class="resumo-card" style="text-decoration:none;">
            <div class="label">Movimentações</div>
            <div class="valor">Ver histórico</div>
        </a>
        <a href="relatorio.php" class="resumo-card" style="text-decoration:none;">
            <div class="label">Relatórios</div>
            <div class="valor">Ver estoque atual</div>
        </a>
    </div>
</div>

</body>
</html>
