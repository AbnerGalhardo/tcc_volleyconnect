<?php
session_start();

if (!isset($_SESSION["login"]["usuario"]["id"])) {
    header("Location: login.php");
    exit();
}

require_once "core/conexao.php";

if (!isset($_GET['id']) || !isset($_GET['data']) || !isset($_GET['hora'])) {
    echo "Parâmetros inválidos!";
    exit();
}

$id = $_GET['id'];
$data = $_GET['data'];
$hora = $_GET['hora'];

// Formatando data
$data_formatada = date("d/m/Y", strtotime($data));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Disponibilidade</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="gradient">
    <div class="card-confirmacao">
        <img src="assets/img/logo.png" class="logo">

        <p class="texto-confirmacao">
            Deseja reservar um horário para encontro com seus fãs às 
            <b><?php echo $hora; ?>h</b> no dia 
            <b><?php echo $data_formatada; ?></b>?
        </p>

        <div class="botoes">
            <a href="salvar_dispo.php?id=<?php echo $id; ?>&data=<?php echo $data; ?>&hora=<?php echo $hora; ?>" 
               class="btn confirmar">Confirmar</a>

            <a href="disponibilidade.php" class="btn cancelar">Cancelar</a>
        </div>
    </div>
</body>
</html>
