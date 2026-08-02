<?php
include('conn.php');

session_save_path('/tmp');

$erro = '';

if(isset($_POST['username'], $_POST['senha'])){
    $username = $_POST['username'];
    $senha = $_POST['senha'];

    if(empty($username)){
        $erro = "Preencha seu username";
    }
    else if(empty($senha)){
        $erro = "Preencha sua senha";
    }
    else{
        $username_esc = $mysqli->real_escape_string($username);
        $sql_query = $mysqli->query("SELECT * FROM usuarios WHERE username = '$username_esc'");

        if($sql_query->num_rows == 1){
            $usuario = $sql_query->fetch_assoc();

            // Verificar a senha usando password_verify
            if(password_verify($senha, $usuario['senha'])){
                if(!isset($_SESSION)){
                    session_start();
                }

                $_SESSION['id'] = $usuario['id'];
                $_SESSION['username'] = $usuario['username'];
                header("Location: painel.php");
                exit();
            } else {
                $erro = "Falha ao Logar! Username ou senha incorretos.";
            }
        } else {
            $erro = "Falha ao Logar! Usuário não encontrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Login</title>
</head>
<body class="auth-page">
    <div class="main-login">
        <div class="left-login">
            <h1>Bem-Vindo!</h1>
            <img src="hand_coding.svg" class="left-login-image" alt="HandCode">
        </div>
        <div class="right-login">
            <div class="card-login">
                <h1>LOGIN</h1>
                <?php if(!empty($erro)): ?>
                    <div class="form-msg"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="textfield">
                        <label for="username">Usuário</label>
                        <input type="text" name="username" placeholder="Usuário">
                    </div>
                    <div class="textfield">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" placeholder="Senha">
                    </div>
                    <button type="submit" class="btn-login">Login</button>
                </form>
                <!--<div class="cadastro-link">
                    <p>Ainda não tem uma conta? <a href="cadastrar.php">Cadastrar-se</a></p> -->
                </div>
            </div>
        </div>
    </div>
</body>
</html>
