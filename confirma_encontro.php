<?php
session_start();
require_once "core/conexao.php";
require_once "includes/valida_login.php";

if (!isset($_GET['atleta']) || !is_numeric($_GET['atleta'])) {
    // se não veio atleta, volta para a lista
    header("Location: encontro.php");
    exit;
}

$id_atleta = intval($_GET['atleta']);
$id_jogo = isset($_GET['id_jogo']) && is_numeric($_GET['id_jogo']) ? intval($_GET['id_jogo']) : null;

$con = conecta();

// 1) busca dados do atleta (inclui id_time do atleta)
$sqlAt = "SELECT a.id, a.id_time, a.posicao, a.idade, u.nome AS nome_atleta
          FROM atleta a
          LEFT JOIN usuario u ON u.id = a.id_usuario
          WHERE a.id = ?
          LIMIT 1";
$stmtAt = $con->prepare($sqlAt);
$stmtAt->bind_param("i", $id_atleta);
$stmtAt->execute();
$resAt = $stmtAt->get_result();
$info = $resAt->fetch_assoc();
$stmtAt->close();

if (!$info) {
    header("Location: encontro.php");
    exit;
}

// 2) se recebi id_jogo, uso ele; senão tento encontrar o próximo jogo do time do atleta
$jogo = null;
if ($id_jogo) {
    $sqlJ = "SELECT id, data, local, id_time1, id_time2 FROM jogo WHERE id = ? LIMIT 1";
    $st = $con->prepare($sqlJ);
    $st->bind_param("i", $id_jogo);
    $st->execute();
    $rj = $st->get_result();
    $jogo = $rj->fetch_assoc();
    $st->close();
} 

if (!$jogo) {
    // tenta achar próximo jogo (data >= now) onde time participa
    $now = date('Y-m-d H:i:s');
    $sqlNext = "SELECT id, data, local, id_time1, id_time2
                FROM jogo
                WHERE (id_time1 = ? OR id_time2 = ?) AND data >= ?
                ORDER BY data ASC
                LIMIT 1";
    $st2 = $con->prepare($sqlNext);
    $st2->bind_param("iis", $info['id_time'], $info['id_time'], $now);
    $st2->execute();
    $r2 = $st2->get_result();
    $jogo = $r2->fetch_assoc();
    $st2->close();
}

// Se ainda não encontrou jogo, set mensagem
if (!$jogo) {
    $mensagemData = "Data e horário não disponíveis";
    $linkCancelar = "encontro.php";
    $confirm_href = "encontro_sucesso.php?atleta=" . $id_atleta;
} else {
    // formatar data/hora
    $timestamp = strtotime($jogo['data']);
    $data_formatada = date("d/m", $timestamp);
    $hora_formatada = date("H:i", $timestamp);
    $mensagemData = "{$data_formatada} às {$hora_formatada}";

    // construir link de confirmação preservando id_jogo
    $confirm_href = "encontro_sucesso.php?atleta=" . $id_atleta . "&id_jogo=" . $jogo['id'];
    $linkCancelar = "encontro.php?id_jogo=" . $jogo['id'];
}

// imagem padrão (substituir quando tiver fotos)
$img = "img/atletas/default.png";
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
    <img src="<?= htmlspecialchars($img) ?>" class="foto" alt="Foto atleta">
    <p class="texto">
        Deseja marcar seu encontro <br>
        com <b><?= htmlspecialchars($info['nome_atleta']) ?></b>? <br>
        <?= htmlspecialchars($mensagemData) ?>
    </p>

    <div class="botoes">
        <a href="<?= htmlspecialchars($confirm_href) ?>" class="btn confirmar">Confirmar</a>
        <a href="<?= htmlspecialchars($linkCancelar) ?>" class="btn cancelar">Cancelar</a>
    </div>
</div>

</body>
</html>
