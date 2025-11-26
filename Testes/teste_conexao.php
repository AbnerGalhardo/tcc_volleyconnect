<?php
include 'conexao.php';

echo "<h2>Teste de Conexão com o Banco de Dados</h2>";

if ($conn) {
    echo "<p style='color: green;'>✅ Conexão com o banco de dados estabelecida com sucesso!</p>";
    echo "<p><strong>Servidor:</strong> localhost</p>";
    echo "<p><strong>Banco:</strong> VolleyConnect</p>";
    
    // Testa se consegue fazer uma consulta simples
    $sql = "SHOW TABLES";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Conseguiu executar consulta no banco!</p>";
        echo "<p><strong>Tabelas encontradas:</strong></p>";
        echo "<ul>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao executar consulta: " . mysqli_error($conn) . "</p>";
    }
    
    mysqli_close($conn);
} else {
    echo "<p style='color: red;'>❌ Erro na conexão com o banco de dados!</p>";
}
?>

