<?php

include 'core/conexao.php';
session_start();

/*
$login_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        $login_error = "Por favor, preencha todos os campos.";
    } else {
        $user_found = false;
        $user_type = '';
        
        // Array com as tabelas para verificar
        $tables = ['atleta', 'administrador', 'torcedor'];
        
        foreach ($t
        ables as $table) {
            $sql = "SELECT nome, email FROM $table WHERE email = ? AND senha = ?";
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $email, $senha);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if ($result && mysqli_num_rows($result) == 1) {
                    $user_data = mysqli_fetch_assoc($result);
                    $user_found = true;
                    $user_type = $table;
                    
                    // Define as variáveis de sessão
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_type'] = $user_type;
                    $_SESSION['user_name'] = $user_data['nome'];
                    
                    mysqli_stmt_close($stmt);
                    break; // Sai do loop quando encontra o usuário
                }
                mysqli_stmt_close($stmt);
            }
        }
        
        if ($user_found) {
            // Redireciona para a página principal
            header("Location: tela_principal.php");
            exit();
        } else {
            $login_error = "E-mail ou senha inválidos. Verifique suas credenciais.";
        }
    }
}

mysqli_close($conn);
*/
?> 

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <img src="img/logo.png" alt="Ícone" class="icon">
      <h2>
        Login ou <a href="criar_conta.html">Criar conta</a>
      </h2>
      <?php if (!empty($_SESSION['ERRO_LOGIN'])): ?>
          <p class="error-message"><?php echo htmlspecialchars($_SESSION['ERRO_LOGIN']); ?></p>
      <?php endif; ?>

      <form action="core/usuario_repositorio.php" method="post">
       <input type="hidden" name="acao" value="login">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="E-mail" required >
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Senha" required>
        <button type="submit">Login</button>
      </form>

      
      
    </div>
  </div>
</body>
</html>