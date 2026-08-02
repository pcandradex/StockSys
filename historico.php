<?php
include('conn.php');
include('protect.php');

$active = 'historico';

// Filtros opcionais
$produto_id = isset($_GET['produto_id']) && $_GET['produto_id'] !== '' ? (int) $_GET['produto_id'] : null;
$tipo = isset($_GET['tipo']) && in_array($_GET['tipo'], ['entrada', 'saida', 'ajuste'], true) ? $_GET['tipo'] : null;
$data_inicio = isset($_GET['data_inicio']) && $_GET['data_inicio'] !== '' ? $_GET['data_inicio'] : null;
$data_fim = isset($_GET['data_fim']) && $_GET['data_fim'] !== '' ? $_GET['data_fim'] : null;

// Montar consulta dinamicamente com escape manual (ambiente sem mysqlnd/get_result)
$sql = "SELECT m.*, p.descricao AS produto_descricao, p.sku AS produto_sku
        FROM movimentacoes_estoque m
        JOIN produtos p ON p.id = m.produto_id
        WHERE 1=1";

if($produto_id) {
    $sql .= " AND m.produto_id = " . (int) $produto_id;
}
if($tipo) {
    // $tipo já validado contra whitelist ['entrada','saida','ajuste'] acima
    $sql .= " AND m.tipo = '" . $mysqli->real_escape_string($tipo) . "'";
}
if($data_inicio && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_inicio)) {
    $sql .= " AND m.criado_em >= '" . $mysqli->real_escape_string($data_inicio) . " 00:00:00'";
}
if($data_fim && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_fim)) {
    $sql .= " AND m.criado_em <= '" . $mysqli->real_escape_string($data_fim) . " 23:59:59'";
}

$sql .= " ORDER BY m.criado_em DESC LIMIT 500";

$result = $mysqli->query($sql);

// Lista de produtos para o filtro
$produtos_result = $mysqli->query("SELECT id, sku, descricao FROM produtos ORDER BY descricao ASC");

$badge_labels = ['entrada' => 'Entrada', 'saida' => 'Saída', 'ajuste' => 'Ajuste'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Histórico de Movimentações</title>
</head>
<body class="app-page">

<?php include('includes/menu.php'); ?>

<div class="container">
    <h1 class="page-title">Histórico de Movimentações</h1>

    <form class="filtros" method="GET">
        <div class="campo">
            <label for="produto_id">Produto</label>
            <select name="produto_id" id="produto_id">
                <option value="">Todos</option>
                <?php while($p = $produtos_result->fetch_assoc()): ?>
                    <option value="<?php echo (int)$p['id']; ?>" <?php echo ($produto_id === (int)$p['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['descricao'] . ' (' . $p['sku'] . ')'); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="campo">
            <label for="tipo">Tipo</label>
            <select name="tipo" id="tipo">
                <option value="">Todos</option>
                <option value="entrada" <?php echo $tipo === 'entrada' ? 'selected' : ''; ?>>Entrada</option>
                <option value="saida" <?php echo $tipo === 'saida' ? 'selected' : ''; ?>>Saída</option>
                <option value="ajuste" <?php echo $tipo === 'ajuste' ? 'selected' : ''; ?>>Ajuste</option>
            </select>
        </div>

        <div class="campo">
            <label for="data_inicio">De</label>
            <input type="date" name="data_inicio" id="data_inicio" value="<?php echo htmlspecialchars($data_inicio ?? ''); ?>">
        </div>

        <div class="campo">
            <label for="data_fim">Até</label>
            <input type="date" name="data_fim" id="data_fim" value="<?php echo htmlspecialchars($data_fim ?? ''); ?>">
        </div>

        <button type="submit">Filtrar</button>
    </form>

    <table class="data-table">
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Produto</th>
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Saldo Anterior</th>
                <th>Saldo Novo</th>
                <th>Motivo</th>
                <th>Usuário</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows === 0): ?>
                <tr><td colspan="8" style="text-align:center;">Nenhuma movimentação encontrada.</td></tr>
            <?php else: ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['criado_em'])); ?></td>
                        <td><?php echo htmlspecialchars($row['produto_descricao']); ?> (<?php echo htmlspecialchars($row['produto_sku']); ?>)</td>
                        <td><span class="badge badge-<?php echo $row['tipo']; ?>"><?php echo $badge_labels[$row['tipo']]; ?></span></td>
                        <td><?php echo (int) $row['quantidade']; ?></td>
                        <td><?php echo (int) $row['saldo_anterior']; ?></td>
                        <td><?php echo (int) $row['saldo_novo']; ?></td>
                        <td><?php echo htmlspecialchars($row['motivo'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['usuario_nome'] ?? '-'); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
