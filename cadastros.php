<?php
include('protect.php');
$active = 'cadastros';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Cadastro de Produtos</title>
</head>
<body class="app-page">

<?php include('includes/menu.php'); ?>

<div class="container">
    <h2 class="page-title">Cadastro de Produtos</h2>
    <form class="styled-form" action="processar_cadastro.php" method="post" enctype="multipart/form-data">
        <label for="sku">SKU:</label>
        <input type="text" id="sku" name="sku" required>

        <label for="descricao">Descrição:</label>
        <input type="text" id="descricao" name="descricao" required>

        <label for="preco">Preço:</label>
        <input type="number" id="preco" name="preco" step="0.01" min="0" required>

        <label for="saldo">Saldo inicial:</label>
        <input type="number" id="saldo" name="saldo" min="0" required>

        <label for="imagem">Imagem:</label>
        <input type="file" id="imagem" name="imagem" accept="image/png, image/jpeg, image/webp" required>

        <input type="submit" value="Cadastrar">
    </form>
</div>

<!-- Modal -->
<div id="successModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p class="modal-message">Produto cadastrado com sucesso!</p>
    <button class="modal-btn">OK</button>
  </div>
</div>

<script>
    function openModal() {
        var modal = document.getElementById("successModal");
        modal.style.display = "block";

        var closeButton = document.getElementsByClassName("close")[0];
        var okButton = document.getElementsByClassName("modal-btn")[0];

        closeButton.onclick = function() { modal.style.display = "none"; }
        okButton.onclick = function() { modal.style.display = "none"; }
        window.onclick = function(event) {
            if (event.target == modal) { modal.style.display = "none"; }
        }
    }

    window.onload = function() {
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            openModal();
        }
    }
</script>

</body>
</html>
