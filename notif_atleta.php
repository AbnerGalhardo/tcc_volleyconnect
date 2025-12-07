<?php
session_start();

if (!isset($_SESSION['login']['usuario']['id'])) {
    header("Location: login.php");
    exit;
}

$atletaUserId = intval($_SESSION['login']['usuario']['id']);

require_once __DIR__ . '/core/conexao.php';
$con = conecta();


// Buscar dados do atleta
$sqlAtleta = "SELECT id, id_time FROM atleta WHERE id_usuario = ?";
$stmt = $con->prepare($sqlAtleta);
$stmt->bind_param("i", $atletaUserId);
$stmt->execute();
$resAtleta = $stmt->get_result();
$atleta = $resAtleta->fetch_assoc();
$stmt->close();

if (!$atleta) {
    echo "Erro: atleta não encontrado.";
    exit;
}

$idAtleta = intval($atleta['id']);
$idTime   = intval($atleta['id_time']);


// ---------------------------------------------
// BUSCAR NOTIFICAÇÕES DE PERGUNTAS E AJUDA
// ---------------------------------------------

// Perguntas feitas pelo atleta
$sqlPerguntas = "
SELECT id, pergunta, resposta, status, data_pergunta, data_resposta
FROM ajuda
WHERE id_usuario = ?
ORDER BY data_pergunta DESC
";
$stmt = $con->prepare($sqlPerguntas);
$stmt->bind_param("i", $atletaUserId);
$stmt->execute();
$resPerguntas = $stmt->get_result();
$perguntas = $resPerguntas->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// ---------------------------------------------
// BUSCAR JOGOS DO ATLETA
// ---------------------------------------------

$sqlJogos = "
    SELECT 
        j.id AS id_jogo,
        j.local,
        j.data,
        t.nome AS nome_time1,
        t2.nome AS nome_time2
    FROM jogo j
    JOIN time t ON t.id = j.id_time1
    JOIN time t2 ON t2.id = j.id_time2
    WHERE j.id_time1 = ? OR j.id_time2 = ?
    ORDER BY j.data ASC
";
$stmt = $con->prepare($sqlJogos);
$stmt->bind_param("ii", $idTime, $idTime);
$stmt->execute();
$resJogos = $stmt->get_result();
$jogos = $resJogos->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// ---------------------------------------------
// BUSCAR SOLICITAÇÕES DE ENCONTRO
// ---------------------------------------------

$sqlEncontros = "
SELECT 
    aet.id AS id_registro,
    u.nome AS nome_torcedor,
    ae.horario_inicial,
    aet.status
FROM atleta_encontro_torcedor aet
JOIN atleta_encontro ae ON ae.id = aet.id_atleta_encontro
JOIN usuario u ON u.id = aet.id_torcedor
WHERE ae.id_atleta = ?
ORDER BY ae.horario_inicial ASC
";

$stmt = $con->prepare($sqlEncontros);
$stmt->bind_param("i", $idAtleta);
$stmt->execute();
$resEncontros = $stmt->get_result();
$encontros = $resEncontros->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Notificações</title>
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
<style>
body {
    margin: 0;
    background: #F4F4F8;
    font-family: Arial, sans-serif;
}

.topo {
    display:flex;
    justify-content: space-between;
    align-items:center;
    background:#fff;
    padding:18px 22px;
    box-shadow:0 1px 4px rgba(0,0,0,0.07);
}
.topo h1 { margin:0; font-size:20px; }

.container {
    max-width: 900px;
    margin: 24px auto;
    padding: 0 16px;
}

.section-title {
    font-size:18px;
    margin:25px 0 12px 5px;
    font-weight:600;
    color:#444;
}

.card {
    background:#fff;
    padding:14px;
    border-radius:10px;
    margin-bottom:12px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06);
}

.status { margin-top:8px; font-weight:bold; }

.confirmado { color:#009933; }
.pendente   { color:#cc6600; }
.recusado   { color:#cc0000; }

.btn {
    margin-top:10px;
    display:inline-block;
    background:#8a4fff;
    color:#fff;
    padding:8px 12px;
    border-radius:8px;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="topo">
    <a href="config_atleta.php" class="back">←</a>
    <h1>Notificações</h1>
    <div></div>
</div>

<div class="container">

    <!-- JOGOS -->
    <div class="section-title">Seus jogos</div>

    <?php if (empty($jogos)): ?>
        <p style="color:#777; margin-left:6px;">Nenhum jogo encontrado para sua equipe.</p>
    <?php else: ?>
        <?php foreach ($jogos as $j): ?>
            <div class="card">
                <strong><?= htmlspecialchars($j['nome_time1']) ?> x <?= htmlspecialchars($j['nome_time2']) ?></strong><br>
                Local: <?= htmlspecialchars($j['local']) ?><br>
                Data: <?= date("d/m/Y H:i", strtotime($j['data'])) ?><br>

                <a class="btn" href="disponibilidade.php?id_jogo=<?= $j['id_jogo'] ?>">
                    Informar disponibilidade
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <!-- SOLICITAÇÕES DE ENCONTRO -->
    <div class="section-title">Solicitações de encontro</div>

    <?php if (empty($encontros)): ?>
        <p style="color:#777; margin-left:6px;">
            Nenhum torcedor tentou marcar encontro com você até o momento.
        </p>
    <?php else: ?>
        <?php foreach ($encontros as $e): ?>
            <div class="card">
                <strong>Torcedor: <?= htmlspecialchars($e['nome_torcedor']) ?></strong><br>
                Horário: <?= date("d/m/Y H:i", strtotime($e['horario_inicial'])) ?><br>

                <div class="status <?= htmlspecialchars($e['status']) ?>">
                    Status: <?= ucfirst($e['status']) ?>
                </div>

                <?php if ($e['status'] === 'pendente'): ?>
                    <a class="btn" href="confirma_dispo.php?id=<?= $e['id_registro'] ?>">
                        Confirmar encontro
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>



    <!-- NOVA SEÇÃO: AJUDA -->
    <div class="section-title">Ajuda e Perguntas</div>

    <?php if (empty($perguntas)): ?>
        <p style="color:#777; margin-left:6px;">Você ainda não enviou perguntas.</p>
    <?php else: ?>
        <?php foreach ($perguntas as $p): ?>
            <div class="card">
                <strong>Pergunta:</strong><br>
                <?= nl2br(htmlspecialchars($p['pergunta'])) ?><br><br>

                <strong>Status:</strong>
                <?php if ($p['status'] === "respondida"): ?>
                    <span class="confirmado">Respondida</span><br><br>
                    <strong>Resposta:</strong><br>
                    <?= nl2br(htmlspecialchars($p['resposta'])) ?><br>
                    <small>Respondido em: <?= date("d/m/Y H:i", strtotime($p['data_resposta'])) ?></small>
                <?php else: ?>
                    <span class="pendente">Pendente</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>
