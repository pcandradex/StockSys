<?php
include('conn.php');
include('protect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku']);
    $descricao = trim($_POST['descricao']);
    $preco = $_POST['preco'];
    $saldo = $_POST['saldo'];

    // Upload da imagem, com validação de tipo/extensão
    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/webp'];

        $imagem_nome_original = basename($_FILES['imagem']['name']);
        $extensao = strtolower(pathinfo($imagem_nome_original, PATHINFO_EXTENSION));

        if (!in_array($extensao, $extensoes_permitidas) || !in_array($_FILES['imagem']['type'], $tipos_permitidos)) {
            die("Tipo de arquivo não permitido. Envie uma imagem JPG, PNG ou WEBP.");
        }

        if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
            die("Imagem muito grande. O limite é 5MB.");
        }

        $imagem_tmp = $_FILES['imagem']['tmp_name'];
        // Nome único para evitar sobrescrever arquivos e problemas de caracteres especiais
        $imagem_nome = uniqid('produto_', true) . '.' . $extensao;
        $imagem_destino = 'uploads/' . $imagem_nome;

        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }

        if (move_uploaded_file($imagem_tmp, $imagem_destino)) {
            $imagem = $imagem_destino;
        }
    }

    $stmt = $mysqli->prepare("INSERT INTO produtos (sku, descricao, imagem, preco, saldo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssdi', $sku, $descricao, $imagem, $preco, $saldo);

    if ($stmt->execute()) {
        $produto_id = $mysqli->insert_id;

        // Registrar a entrada inicial no histórico de movimentações
        $tipo = 'entrada';
        $motivo = 'Cadastro inicial do produto';
        $usuario_id = $_SESSION['id'] ?? null;
        $usuario_nome = $_SESSION['username'] ?? null;
        $saldo_anterior = 0;

        $hist_stmt = $mysqli->prepare("INSERT INTO movimentacoes_estoque (produto_id, tipo, quantidade, saldo_anterior, saldo_novo, motivo, usuario_id, usuario_nome) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $hist_stmt->bind_param('isiiisis', $produto_id, $tipo, $saldo, $saldo_anterior, $saldo, $motivo, $usuario_id, $usuario_nome);
        $hist_stmt->execute();
        $hist_stmt->close();

        $stmt->close();
        header("Location: cadastros.php?success=1");
        exit();
    } else {
        $stmt->close();
        echo "Erro ao cadastrar o produto. Verifique os dados e tente novamente.";
    }
}
?>
