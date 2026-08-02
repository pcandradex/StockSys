<?php
// Incluir o arquivo de conexão com o banco de dados
include('conn.php');
include('protect.php');

$active = 'produtos';
$erro = '';

// Verificar se o ID do produto foi fornecido via GET
if(!isset($_GET['id'])) {
    header("Location: produtos.php");
    exit();
}

$id = (int) $_GET['id'];

$result = $mysqli->query("SELECT * FROM produtos WHERE id = " . $id);

if($result->num_rows !== 1) {
    header("Location: produtos.php");
    exit();
}

$produto = $result->fetch_assoc();

// Verificar se o formulário foi submetido
if(isset($_POST['salvar'])) {
    $sku = trim($_POST['sku']);
    $descricao = trim($_POST['descricao']);
    $preco = $_POST['preco'];
    $saldo_novo = (int) $_POST['saldo'];
    $saldo_anterior = (int) $produto['saldo'];

    $update_stmt = $mysqli->prepare("UPDATE produtos SET sku = ?, descricao = ?, preco = ?, saldo = ? WHERE id = ?");
    $update_stmt->bind_param('ssdii', $sku, $descricao, $preco, $saldo_novo, $id);

    if($update_stmt->execute()) {
        // Se o saldo foi alterado manualmente aqui, registrar como "ajuste" no histórico
        if($saldo_novo !== $saldo_anterior) {
            $tipo = 'ajuste';
            $quantidade = abs($saldo_novo - $saldo_anterior);
            $motivo = 'Ajuste manual via edição de produto';
            $usuario_id = $_SESSION['id'] ?? null;
            $usuario_nome = $_SESSION['username'] ?? null;

            $hist_stmt = $mysqli->prepare("INSERT INTO movimentacoes_estoque (produto_id, tipo, quantidade, saldo_anterior, saldo_novo, motivo, usuario_id, usuario_nome) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $hist_stmt->bind_param('isiiisis', $id, $tipo, $quantidade, $saldo_anterior, $saldo_novo, $motivo, $usuario_id, $usuario_nome);
            $hist_stmt->execute();
            $hist_stmt->close();
        }

        header("Location: produtos.php");
        exit();
    } else {
        $erro = "Erro ao salvar o produto. Tente novamente.";
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Editar Produto</title>
</head>
<body class="app-page">

<?php include('includes/menu.php'); ?>

<div class="container">
    <h1 class="page-title">Editar Produto</h1>

    <?php if(!empty($erro)): ?>
        <div class="form-msg"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <form class="styled-form" method="POST">
        <label for="sku">SKU:</label>
        <input type="text" name="sku" id="sku" value="<?php echo htmlspecialchars($produto['sku']); ?>">

        <label for="descricao">Descrição:</label>
        <input type="text" name="descricao" id="descricao" value="<?php echo htmlspecialchars($produto['descricao']); ?>">

        <label for="preco">Preço:</label>
        <input type="number" step="0.01" name="preco" id="preco" value="<?php echo htmlspecialchars($produto['preco']); ?>">

        <label for="saldo">Saldo:</label>
        <input type="number" name="saldo" id="saldo" value="<?php echo htmlspecialchars($produto['saldo']); ?>">
        <small style="display:block; margin-top:-12px; margin-bottom:18px; color: var(--text-light); opacity:0.7;">
            Para entradas/saídas com motivo registrado, use <a href="ajustar_estoque.php?id=<?php echo (int)$produto['id']; ?>">Ajustar Estoque</a>.
        </small>

        <input type="submit" name="salvar" value="Salvar">
        <a class="btn-voltar" href="produtos.php">Voltar</a>
    </form>
</div>

</body>
</html>
