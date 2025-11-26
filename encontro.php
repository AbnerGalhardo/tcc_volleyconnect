<?php
$atletas = [
    ["id" => 1, "nome" => "Carolana", "img" => "img/atletas/carolana.jpg"],
    ["id" => 2, "nome" => "Julia B.", "img" => "img/atletas/julia.jpg"],
    ["id" => 3, "nome" => "Rosamaria", "img" => "img/atletas/rosamaria.jpg"],
    ["id" => 4, "nome" => "Gattaz", "img" => "img/atletas/gattaz.jpg"],
    ["id" => 5, "nome" => "Darlan", "img" => "img/atletas/darlan.jpg"],
    ["id" => 6, "nome" => "Lucas B.", "img" => "img/atletas/lucas.jpg"],
    ["id" => 7, "nome" => "Honorato", "img" => "img/atletas/honorato.jpg"],
    ["id" => 8, "nome" => "Lucarelli", "img" => "img/atletas/lucarelli.jpg"]
];
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
    <div class="icons">
        
    </div>
</header>

<form method="GET" action="confirma_encontro.php" class="lista-atletas">

    <?php foreach ($atletas as $a): ?>
        <label class="card">
            <img src="<?= $a['img'] ?>">
            <input type="radio" name="atleta" value="<?= $a['id'] ?>" required>
            <span><?= $a['nome'] ?></span>
        </label>
    <?php endforeach; ?>

    <button type="submit" class="continuar">Continuar</button>
</form>

</body>
</html>
