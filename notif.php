<?php
session_start();

$userId = null;
if (isset($_SESSION['login']['usuario']['id'])) {
    $userId = intval($_SESSION['login']['usuario']['id']);
} elseif (isset($_SESSION['id_usuario'])) {
    $userId = intval($_SESSION['id_usuario']);
} elseif (isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
} elseif (isset($_SESSION['usuario_id'])) {
    $userId = intval($_SESSION['usuario_id']);
}

if (!$userId) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/core/conexao.php';
$con = conecta();

/* -------------------------------------------------------
   NOTIFICAÇÕES SOBRE JOGOS
------------------------------------------------------- */
$sqlJogos = "
SELECT 
    j.id AS id_jogo,
    j.local,
    j.data,
    t1.nome AS nome_time1,
    t2.nome AS nome_time2
FROM jogo j
LEFT JOIN time t1 ON t1.id = j.id_time1
LEFT JOIN time t2 ON t2.id = j.id_time2
WHERE j.data >= NOW()
ORDER BY j.data ASC
";

$resJogos = $con->query($sqlJogos);
$jogos = $resJogos->fetch_all(MYSQLI_ASSOC);

/* -------------------------------------------------------
   NOTIFICAÇÕES SOBRE ENCONTROS
------------------------------------------------------- */
$sqlEncontros = "
SELECT 
    aet.id AS id_registro,
    u.nome AS nome_atleta,
    t.nome AS nome_time,
    ae.horario_inicial,
    aet.status
FROM atleta_encontro_torcedor aet
JOIN atleta_encontro ae ON ae.id = aet.id_atleta_encontro
JOIN atleta a ON a.id = ae.id_atleta
JOIN usuario u ON u.id = a.id_usuario
JOIN time t ON t.id = a.id_time
WHERE aet.id_torcedor = ?
ORDER BY ae.horario_inicial ASC
";

$stmt = $con->prepare($sqlEncontros);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resEncontros = $stmt->get_result();
$encontros = $resEncontros->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* -------------------------------------------------------
   🔔 NOVA SEÇÃO — NOTIFICAÇÕES SOBRE AJUDA / PERGUNTAS
------------------------------------------------------- */

$sqlAjuda = "
SELECT 
    id,
    pergunta,
    resposta,
    data_pergunta,
    data_resposta,
    status
FROM ajuda
WHERE id_usuario = ?
ORDER BY data_pergunta DESC
";

$stmtA = $con->prepare($sqlAjuda);
$stmtA->bind_param("i", $userId);
$stmtA->execute();
$resAjuda = $stmtA->get_result();
$ajudas = $resAjuda->fetch_all(MYSQLI_ASSOC);
$stmtA->close();

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

/* TOPO */
.topo {
    display:flex;
    justify-content: space-between;
    align-items:center;
    background:#fff;
    padding:18px 22px;
    box-shadow:0 1px 4px rgba(0,0,0,0.07);
}
.topo h1 { margin:0; font-size:20px; }

/* CONTEÚDO */
.container {
    max-width: 900px;
    margin: 24px auto;
    padding: 0 16px;
}

/* SEÇÕES */
.section-title {
    font-size:18px;
    margin:25px 0 12px 5px;
    font-weight:600;
    color:#444;
}

/* CARDS */
.card {
    background:#fff;
    padding:14px;
    border-radius:10px;
    margin-bottom:12px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06);
}

.status {
    margin-top:8px;
    font-weight:bold;
}

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
    <a href="config.php" class="back">←</a>
    <h1>Notificações</h1>
    <div></div>
</div>

<div class="container">

    <!-- JOGOS -->
    <div class="section-title">Jogos disponíveis para marcar encontro</div>

    <?php if (empty($jogos)): ?>
        <p style="color:#777; margin-left:6px;">Nenhum jogo encontrado.</p>
    <?php else: ?>
        <?php foreach ($jogos as $j): 
            $dataFmt = date("d/m/Y H:i", strtotime($j['data']));
        ?>
            <div class="card">
                <strong><?= htmlspecialchars($j['nome_time1']) ?> x <?= htmlspecialchars($j['nome_time2']) ?></strong><br>
                Local: <?= htmlspecialchars($j['local']) ?><br>
                Data: <?= $dataFmt ?><br>

                <a class="btn" href="cronograma.php">Marcar encontro</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <!-- ENCONTROS -->
    <div class="section-title">Atualizações dos seus encontros</div>

    <?php if (empty($encontros)): ?>
        <p style="color:#777; margin-left:6px;">Você ainda não marcou encontros.</p>
    <?php else: ?>
        <?php foreach ($encontros as $e): 
            $dataEncontro = date("d/m/Y H:i", strtotime($e['horario_inicial']));
            $classe = $e['status']; 
        ?>
            <div class="card">
                <strong>Encontro com <?= htmlspecialchars($e['nome_atleta']) ?></strong><br>
                Time: <?= htmlspecialchars($e['nome_time']) ?><br>
                Horário: <?= $dataEncontro ?><br>

                <div class="status <?= $classe ?>">
                    Status: <?= ucfirst($e['status']) ?>
                </div>

                <a class="btn" href="meus_encontros.php">Ver detalhes</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <!-- PERGUNTAS / AJUDA -->
    <div class="section-title">Ajuda e Perguntas</div>

    <?php if (empty($ajudas)): ?>
        <p style="color:#777; margin-left:6px;">Você ainda não enviou perguntas.</p>
    <?php else: ?>
        <?php foreach ($ajudas as $a): ?>
            <div class="card">
                <strong>Sua pergunta:</strong><br>
                <?= nl2br(htmlspecialchars($a['pergunta'])) ?><br><br>

                <strong>Status:</strong> 
                <span class="<?= $a['status'] ?>">
                    <?= ucfirst($a['status']) ?>
                </span><br>

                <?php if ($a['status'] === "respondida"): ?>
                    <br>
                    <strong>Resposta do administrador:</strong><br>
                    <?= nl2br(htmlspecialchars($a['resposta'])) ?><br>
                    <a class="btn" href="ajuda.php">Ver todas</a>
                <?php else: ?>
                    <br>
                    <span style="color:#555;">Aguardando resposta...</span>
                    <br><a class="btn" href="ajuda.php">Ver detalhes</a>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>
