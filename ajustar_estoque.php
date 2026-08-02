<?php
include('conn.php');
include('protect.php');

$active = 'produtos';
$erro = '';

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

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $quantidade_form = (int) ($_POST['quantidade'] ?? 0);
    $motivo = trim($_POST['motivo'] ?? '');

    $saldo_atual = (int) $produto['saldo'];

    if(!in_array($tipo, ['entrada', 'saida', 'ajuste'], true)) {
        $erro = "Selecione um tipo de movimentação válido.";
    } else if($quantidade_form <= 0) {
        $erro = "Informe uma quantidade maior que zero.";
    } else {
        if($tipo === 'entrada') {
            $saldo_novo = $saldo_atual + $quantidade_form;
            $quantidade_log = $quantidade_form;
        } else if($tipo === 'saida') {
            $saldo_novo = $saldo_atual - $quantidade_form;
            $quantidade_log = $quantidade_form;
            if($saldo_novo < 0) {
                $erro = "Saldo insuficiente. Saldo atual: {$saldo_atual}.";
            }
        } else { // ajuste: define o saldo diretamente para o valor informado
            $saldo_novo = $quantidade_form;
            $quantidade_log = abs($saldo_novo - $saldo_atual);
        }

        if(empty($erro)) {
            $update_stmt = $mysqli->prepare("UPDATE produtos SET saldo = ? WHERE id = ?");
            $update_stmt->bind_param('ii', $saldo_novo, $id);

            if($update_stmt->execute()) {
                $usuario_id = $_SESSION['id'] ?? null;
                $usuario_nome = $_SESSION['username'] ?? null;

                $hist_stmt = $mysqli->prepare("INSERT INTO movimentacoes_estoque (produto_id, tipo, quantidade, saldo_anterior, saldo_novo, motivo, usuario_id, usuario_nome) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $hist_stmt->bind_param('isiiisis', $id, $tipo, $quantidade_log, $saldo_atual, $saldo_novo, $motivo, $usuario_id, $usuario_nome);
                $hist_stmt->execute();
                $hist_stmt->close();

                header("Location: produtos.php?ajuste=1");
                exit();
            } else {
                $erro = "Erro ao atualizar o saldo. Tente novamente.";
            }
            $update_stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Ajustar Estoque</title>
</head>
<body class="app-page">

<?php include('includes/menu.php'); ?>

<div class="container">
    <h1 class="page-title">Ajustar Estoque</h1>

    <div class="card-box">
        <p><strong>Produto:</strong> <?php echo htmlspecialchars($produto['descricao']); ?> (SKU: <?php echo htmlspecialchars($produto['sku']); ?>)</p>
        <p><strong>Saldo atual:</strong> <?php echo (int) $produto['saldo']; ?></p>
    </div>

    <?php if(!empty($erro)): ?>
        <div class="form-msg"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <form class="styled-form" method="POST">
        <label for="tipo">Tipo de movimentação:</label>
        <select name="tipo" id="tipo" required>
            <option value="">Selecione...</option>
            <option value="entrada">Entrada (compra, devolução, etc.)</option>
            <option value="saida">Saída (venda, perda, etc.)</option>
            <option value="ajuste">Ajuste (definir saldo exato, ex: contagem de estoque)</option>
        </select>

        <label for="quantidade" id="label-quantidade">Quantidade:</label>
        <input type="number" name="quantidade" id="quantidade" min="1" required>
        <small id="ajuda-quantidade" style="display:block; margin-top:-12px; margin-bottom:18px; color: var(--text-light); opacity:0.7;">
            Para "Entrada"/"Saída", informe a quantidade a somar ou subtrair. Para "Ajuste", informe o saldo final desejado.
        </small>

        <label for="motivo">Motivo / observação:</label>
        <textarea name="motivo" id="motivo" rows="3" placeholder="Ex: Compra do fornecedor X, venda pedido #123, contagem de inventário..."></textarea>

        <input type="submit" value="Registrar Movimentação">
        <a class="btn-voltar" href="produtos.php">Voltar</a>
    </form>
</div>

</body>
</html>
