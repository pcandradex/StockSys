<?php
// Incluir o arquivo de conexão com o banco de dados
include('conn.php');
include('protect.php');

$active = 'produtos';

// Verificar se o botão de exclusão foi clicado e se o ID do produto foi fornecido
if(isset($_POST['excluir']) && isset($_POST['produto_id'])) {
    $produto_id = (int) $_POST['produto_id'];

    // Consulta SQL para excluir o produto do banco de dados (prepared statement)
    $stmt = $mysqli->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param('i', $produto_id);
    $result = $stmt->execute();
    $stmt->close();

    // Verificar se a exclusão foi bem-sucedida
    if($result) {
        echo '<script>alert("Produto excluído com sucesso.");</script>';
        // Atualizar a página para refletir a exclusão
        echo '<script>window.location.href = "produtos.php";</script>';
        exit();
    } else {
        echo '<script>alert("Erro ao excluir o produto.");</script>';
    }
}

// Consulta SQL para buscar todos os produtos cadastrados
$sql = "SELECT * FROM produtos ORDER BY descricao ASC";
$result = $mysqli->query($sql);

// Limite para considerar "saldo baixo" (pode ser ajustado conforme a necessidade)
define('SALDO_BAIXO', 5);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Produtos</title>
</head>
<body class="app-page">

<?php include('includes/menu.php'); ?>

<div class="container">
    <h1 class="page-title">Produtos</h1>

    <?php if(isset($_GET['ajuste'])): ?>
        <div class="form-msg" style="background-color: rgba(0,255,136,0.15); color: #7CFFC4;">Estoque atualizado com sucesso!</div>
    <?php endif; ?>

    <ul class="produto-list">
        <?php
        // Loop para exibir cada produto como um item de lista
        while($row = $result->fetch_assoc()) {
            $saldo_baixo = $row['saldo'] <= SALDO_BAIXO;
            echo '<li class="produto">';
            echo '<img src="' . htmlspecialchars($row['imagem']) . '" alt="' . htmlspecialchars($row['descricao']) . '">';
            echo '<h2>' . htmlspecialchars($row['descricao']) . '</h2>';
            echo '<p><strong>SKU:</strong> ' . htmlspecialchars($row['sku']) . '</p>';
            echo '<p><strong>Preço:</strong> R$ ' . number_format($row['preco'], 2, ',', '.') . '</p>';
            echo '<p><strong>Saldo:</strong> <span class="' . ($saldo_baixo ? 'saldo-baixo' : '') . '">' . (int)$row['saldo'] . ($saldo_baixo ? ' ⚠' : '') . '</span></p>';

            echo '<div class="produto-actions">';
            echo '<a class="btn-ajustar" href="ajustar_estoque.php?id=' . (int)$row['id'] . '">Ajustar Estoque</a>';
            echo '<a class="btn-editar" href="editar_produto.php?id=' . (int)$row['id'] . '">Editar</a>';
            echo '<form class="excluir-form" method="POST" onsubmit="return confirm(\'Tem certeza que deseja excluir este produto?\');">';
            echo '<input type="hidden" name="produto_id" value="' . (int)$row['id'] . '">';
            echo '<button class="btn-excluir" type="submit" name="excluir">Excluir</button>';
            echo '</form>';
            echo '</div>';

            echo '</li>';
        }
        ?>
    </ul>
</div>

</body>
</html>
