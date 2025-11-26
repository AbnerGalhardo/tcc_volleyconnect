<?php
include 'conexao.php';
session_start();

$login_error = '';
$debug_info = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    
    $debug_info[] = "Email recebido: '$email'";
    $debug_info[] = "Senha recebida: '$senha'";

    if (empty($email) || empty($senha)) {
        $login_error = "Por favor, preencha todos os campos.";
        $debug_info[] = "Erro: Campos vazios";
    } else {
        $user_found = false;
        $user_type = '';
        
        // Array com as tabelas para verificar
        $tables = ['atleta', 'administrador', 'torcedor'];
        
        foreach ($tables as $table) {
            $debug_info[] = "Verificando tabela: $table";
            
            $sql = "SELECT nome, email FROM $table WHERE email = ? AND senha = ?";
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $email, $senha);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                $debug_info[] = "Consulta executada para $table";
                $debug_info[] = "Número de resultados: " . mysqli_num_rows($result);
                
                if ($result && mysqli_num_rows($result) == 1) {
                    $user_data = mysqli_fetch_assoc($result);
                    $user_found = true;
                    $user_type = $table;
                    
                    $debug_info[] = "USUÁRIO ENCONTRADO na tabela $table!";
                    $debug_info[] = "Nome: " . $user_data['nome'];
                    
                    // Define as variáveis de sessão
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_type'] = $user_type;
                    $_SESSION['user_name'] = $user_data['nome'];
                    
                    mysqli_stmt_close($stmt);
                    break; // Sai do loop quando encontra o usuário
                }
                mysqli_stmt_close($stmt);
            } else {
                $debug_info[] = "ERRO ao preparar consulta para $table";
            }
        }
        
        if ($user_found) {
            $debug_info[] = "LOGIN REALIZADO COM SUCESSO!";
            $debug_info[] = "Redirecionando para index.php...";
            // Comentei o redirecionamento para ver o debug
            // header("Location: index.php");
            // exit();
        } else {
            $login_error = "E-mail ou senha inválidos. Verifique suas credenciais.";
            $debug_info[] = "ERRO: Usuário não encontrado em nenhuma tabela";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - Debug</title>
    <link rel="stylesheet" href="style_login.css">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <img src="img/logo.png" alt="Ícone" class="icon">
      <h2>
        Login ou <a href="criar_conta.html">Criar conta</a>
      </h2>

      <?php if (!empty($login_error)): ?>
          <p class="error-message"><?php echo htmlspecialchars($login_error); ?></p>
      <?php endif; ?>

      <?php if (!empty($debug_info)): ?>
          <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 4px; font-size: 12px;">
              <strong>Debug Info:</strong><br>
              <?php foreach ($debug_info as $info): ?>
                  <?php echo htmlspecialchars($info); ?><br>
              <?php endforeach; ?>
          </div>
      <?php endif; ?>

      <form action="login_debug.php" method="post">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="E-mail" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
<br>
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Senha" required>
        <button type="submit">Login</button>
      </form>
    </div>
  </div>
</body>
</html>

