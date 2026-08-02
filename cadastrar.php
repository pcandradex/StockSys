<?php
include('conn.php');

session_save_path('/tmp');

$msg = '';
$sucesso = false;

// Verificar se o formulário de cadastro foi enviado
if(isset($_POST['cadastrar'])){
    $username = $_POST['username'];
    $senha = $_POST['senha'];

    if(empty($username)){
        $msg = "Preencha seu username";
    }
    else if(empty($senha)){
        $msg = "Preencha sua senha";
    }
    else{
        // Verificar se o usuário já existe no banco de dados
        $username_esc = $mysqli->real_escape_string($username);
        $check_result = $mysqli->query("SELECT id FROM usuarios WHERE username = '$username_esc'");

        if($check_result->num_rows > 0){
            $msg = "Usuário já existe. Por favor, escolha outro.";
        }
        else{
            // Hash da senha
            $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

            // Inserir novo usuário no banco de dados
            $insert_stmt = $mysqli->prepare("INSERT INTO usuarios (username, senha) VALUES (?, ?)");
            $insert_stmt->bind_param('ss', $username, $hashed_password);
            if($insert_stmt->execute()){
                $msg = "Usuário cadastrado com sucesso!";
                $sucesso = true;
            }
            else{
                $msg = "Erro ao cadastrar usuário. Tente novamente.";
            }
            $insert_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="auth-page">
    <div class="main-login">
        <div class="left-login">
            <h1>Bem-Vindo!</h1>
            <img src="hand_coding.svg" class="left-login-image" alt="HandCode">
        </div>
        <div class="right-login">
            <div class="card-cadastro">
                <h1>CADASTRAR-SE</h1>
                <?php if(!empty($msg)): ?>
                    <div class="form-msg" style="<?php echo $sucesso ? 'background-color: rgba(0,255,136,0.15); color: #7CFFC4;' : ''; ?>">
                        <?php echo htmlspecialchars($msg); ?>
                    </div>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="textfield">
                        <label for="cadastro-username">Usuário</label>
                        <input type="text" name="username" placeholder="Usuário">
                    </div>
                    <div class="textfield">
                        <label for="cadastro-senha">Senha</label>
                        <input type="password" name="senha" placeholder="Senha">
                    </div>
                    <button type="submit" name="cadastrar" class="btn-cadastro">Cadastrar</button>
                </form>
                <div class="cadastro-link">
                    <p>Já possui uma conta? <a href="index.php">Logar</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
