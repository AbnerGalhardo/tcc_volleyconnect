<?php
include 'conexao.php';

// Vamos testar com dados específicos para debugar
echo "<h2>Debug do Login</h2>";

// Substitua estes valores pelos dados que você cadastrou
$email_teste = "abnerrrksjkk@gmail.com"; // COLOQUE AQUI UM EMAIL QUE VOCÊ CADASTROU
$senha_teste = "1212";          // COLOQUE AQUI A SENHA QUE VOCÊ CADASTROU

echo "<p><strong>Testando com:</strong></p>";
echo "<p>Email: $email_teste</p>";
echo "<p>Senha: $senha_teste</p>";
echo "<hr>";

$tables = ['atleta', 'administrador', 'torcedor'];

foreach ($tables as $table) {
    echo "<h3>Testando tabela: $table</h3>";
    
    // Primeiro, vamos ver todos os dados da tabela
    $sql_all = "SELECT nome, email, senha FROM $table LIMIT 5";
    $result_all = mysqli_query($conn, $sql_all);
    
    if ($result_all && mysqli_num_rows($result_all) > 0) {
        echo "<p><strong>Dados encontrados na tabela $table:</strong></p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Nome</th><th>Email</th><th>Senha</th></tr>";
        while ($row = mysqli_fetch_assoc($result_all)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['senha']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>Nenhum dado encontrado na tabela $table</p>";
    }
    
    // Agora testa a consulta específica
    $sql = "SELECT nome, email FROM $table WHERE email = ? AND senha = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $email_teste, $senha_teste);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) == 1) {
            $user_data = mysqli_fetch_assoc($result);
            echo "<p style='color: green;'>✅ USUÁRIO ENCONTRADO na tabela $table!</p>";
            echo "<p>Nome: " . htmlspecialchars($user_data['nome']) . "</p>";
            echo "<p>Email: " . htmlspecialchars($user_data['email']) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Usuário NÃO encontrado na tabela $table</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p style='color: red;'>Erro ao preparar consulta para $table</p>";
    }
    
    echo "<hr>";
}

mysqli_close($conn);
?>

<style>
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
</style>

