<?php
session_start();
require_once "core/conexao.php";
require_once "includes/valida_login.php";

$con = conecta();

// Buscar todos os atletas com nome do usuário
$sql = "
    SELECT 
        atleta.id,
        usuario.nome AS nome_atleta
    FROM atleta
    INNER JOIN usuario ON usuario.id = atleta.id_usuario
    ORDER BY usuario.nome
";

$result = mysqli_query($con, $sql);
$atletas = [];

while ($row = mysqli_fetch_assoc($result)) {
    $atletas[] = [
        "id" => $row["id"],
        "nome" => $row["nome_atleta"],
        // foto padrão por enquanto
        "img" => "img/atletas/default.png"
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Selecione Seu Atleta</title>
<link rel="stylesheet" href="css/encontro.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
</head>

<body>

<header>
    <a href="cronograma_detalhes.php" class="back">←</a>
    <h2>SELECIONE SEU ATLETA PARA ENCONTRO</h2>
    <div class="icons"></div>
</header>

<form method="GET" action="confirma_encontro.php" class="lista-atletas">

    <?php if (empty($atletas)): ?>
        <p style="font-size:20px; text-align:center; grid-column: span 4;">
            Nenhum atleta cadastrado ainda.
        </p>
    <?php else: ?>
        <?php foreach ($atletas as $a): ?>
            <label class="card">
                <img src="<?= $a['img'] ?>">
                <input type="radio" name="atleta" value="<?= $a['id'] ?>" required>
                <span><?= $a['nome'] ?></span>
            </label>
        <?php endforeach; ?>
    <?php endif; ?>

    <button type="submit" class="continuar">Continuar</button>
</form>

</body>
</html>
