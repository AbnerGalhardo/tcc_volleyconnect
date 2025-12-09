<?php
session_start();

require_once "core/conexao.php";
require_once "includes/valida_login.php"; 

if (isset($_SESSION['login']['usuario']['id'])) {
    $id_torcedor = intval($_SESSION['login']['usuario']['id']);
} elseif (isset($_SESSION['id'])) {
    $id_torcedor = intval($_SESSION['id']);
} else {
    header("Location: login.php");
    exit;
}


$id_atleta = isset($_GET['atleta']) ? intval($_GET['atleta']) : (isset($_POST['id_atleta']) ? intval($_POST['id_atleta']) : 0);
$id_jogo   = isset($_GET['id_jogo']) ? intval($_GET['id_jogo']) : (isset($_POST['id_jogo']) ? intval($_POST['id_jogo']) : 0);

if ($id_atleta <= 0 || $id_jogo <= 0) {
    header("Location: encontro.php");
    exit;
}

$con = conecta(); 

$sqlFind = "SELECT id, horario_inicial FROM atleta_encontro WHERE id_atleta = ? AND id_jogo = ? LIMIT 1";
$stmt = $con->prepare($sqlFind);
$stmt->bind_param("ii", $id_atleta, $id_jogo);
$stmt->execute();
$res = $stmt->get_result();
$ae = $res->fetch_assoc();
$stmt->close();

if (!$ae) {
    $sqlJ = "SELECT data FROM jogo WHERE id = ? LIMIT 1";
    $stj = $con->prepare($sqlJ);
    $stj->bind_param("i", $id_jogo);
    $stj->execute();
    $rj = $stj->get_result();
    $j = $rj->fetch_assoc();
    $stj->close();

    $horario_inicial = ($j && !empty($j['data'])) ? $j['data'] : date('Y-m-d H:i:s');

    $sqlInsertAE = "INSERT INTO atleta_encontro (id_atleta, id_jogo, horario_inicial, duracao, vagas) VALUES (?, ?, ?, ?, ?)";
    $stIns = $con->prepare($sqlInsertAE);
    $duracao = 60; 
    $vagas = 10;   
    $stIns->bind_param("iissi", $id_atleta, $id_jogo, $horario_inicial, $duracao, $vagas);
    $stIns->execute();
    $atleta_encontro_id = $stIns->insert_id;
    $stIns->close();
} else {
    $atleta_encontro_id = intval($ae['id']);
}

$sqlInsertTor = "INSERT INTO atleta_encontro_torcedor (id_atleta_encontro, id_torcedor) VALUES (?, ?)";
$st3 = $con->prepare($sqlInsertTor);
$st3->bind_param("ii", $atleta_encontro_id, $id_torcedor);
$ok = $st3->execute();
$st3->close();


if (function_exists('desconecta')) desconecta($con);

if (!$ok) {
    $erro = $con->error ?? 'Erro ao agendar encontro.';
    die("Erro ao agendar encontro: " . htmlspecialchars($erro));
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Encontro marcado</title>
<link rel="stylesheet" href="css/encontro_sucesso.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
</head>

<body>
<div class="modal">
    <h2>Encontro solicitado!</h2>

    <a href="tela_principal.php" class="btn">Voltar</a>
</div>
</body>
</html>
