<?php
include('conn.php');
include('protect.php');

$active = 'relatorio';

$sql = "SELECT * FROM produtos ORDER BY descricao ASC";
$result = $mysqli->query($sql);

$produtos = [];
$total_itens = 0;
$valor_total = 0;
$qtd_saldo_baixo = 0;
define('SALDO_BAIXO_RELATORIO', 5);

while($row = $result->fetch_assoc()) {
    $produtos[] = $row;
    $total_itens += (int) $row['saldo'];
    $valor_total += ((float) $row['preco']) * ((int) $row['saldo']);
    if((int) $row['saldo'] <= SALDO_BAIXO_RELATORIO) {
        $qtd_saldo_baixo++;
    }
}

$data_emissao = date('d/m/Y H:i');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Relatório de Estoque</title>
</head>
<body class="app-page">

<?php include('includes/menu.php'); ?>

<div class="container">
    <h1 class="page-title">Relatório de Estoque</h1>

    <p class="no-print" style="margin: -10px 0 20px; opacity: 0.7;">Emitido em <?php echo $data_emissao; ?></p>
    <p style="display:none;" class="print-only-date">Emitido em <?php echo $data_emissao; ?></p>

    <div class="resumo-cards">
        <div class="resumo-card">
            <div class="label">Produtos cadastrados</div>
            <div class="valor"><?php echo count($produtos); ?></div>
        </div>
        <div class="resumo-card">
            <div class="label">Itens em estoque</div>
            <div class="valor"><?php echo $total_itens; ?></div>
        </div>
        <div class="resumo-card">
            <div class="label">Valor total em estoque</div>
            <div class="valor">R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></div>
        </div>
        <div class="resumo-card">
            <div class="label">Produtos com saldo baixo (≤ <?php echo SALDO_BAIXO_RELATORIO; ?>)</div>
            <div class="valor"><?php echo $qtd_saldo_baixo; ?></div>
        </div>
    </div>

    <button class="btn-imprimir no-print" onclick="window.print()">🖨️ Imprimir Relatório</button>

    <br><br>

    <table class="data-table">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Descrição</th>
                <th>Preço Unit.</th>
                <th>Saldo</th>
                <th>Valor em Estoque</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($produtos)): ?>
                <tr><td colspan="5" style="text-align:center;">Nenhum produto cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach($produtos as $p): ?>
                    <?php $valor_item = ((float)$p['preco']) * ((int)$p['saldo']); ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['sku']); ?></td>
                        <td><?php echo htmlspecialchars($p['descricao']); ?></td>
                        <td>R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo (int)$p['saldo']; ?><?php echo ((int)$p['saldo'] <= SALDO_BAIXO_RELATORIO) ? ' ⚠' : ''; ?></td>
                        <td>R$ <?php echo number_format($valor_item, 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
    @media print {
        .print-only-date { display: block !important; margin-bottom: 20px; }
    }
</style>

</body>
</html>
