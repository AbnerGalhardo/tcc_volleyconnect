<?php
// Lista fixa (substituir depois pelo banco)
$atletas = [
    1 => ["nome" => "Carolana", "img" => "img/atletas/carolana.jpg"],
    2 => ["nome" => "Julia B.", "img" => "img/atletas/julia.jpg"],
    3 => ["nome" => "Rosamaria", "img" => "img/atletas/rosamaria.jpg"],
    4 => ["nome" => "Gattaz", "img" => "img/atletas/gattaz.jpg"],
    5 => ["nome" => "Darlan", "img" => "img/atletas/darlan.jpg"],
    6 => ["nome" => "Lucas B.", "img" => "img/atletas/lucas.jpg"],
    7 => ["nome" => "Honorato", "img" => "img/atletas/honorato.jpg"],
    8 => ["nome" => "Lucarelli", "img" => "img/atletas/lucarelli.jpg"]
];

$id = $_GET['atleta'];

$info = $atletas[$id];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Confirmar encontro</title>
<link rel="stylesheet" href="css/confirma_encontro.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
</head>

<body>

<div class="modal">
    <img src="<?= $info['img'] ?>" class="foto">
    <p class="texto">
        Deseja marcar seu encontro <br>
        com <b><?= $info['nome'] ?></b>? <br>
        15/09 às 18:00
    </p>

    <div class="botoes">
        <a href="encontro_sucesso.php" class="btn confirmar">Confirmar</button>
        <a href="encontro.php" class="btn cancelar">Cancelar</a>
    </div>
</div>

</body>
</html>
