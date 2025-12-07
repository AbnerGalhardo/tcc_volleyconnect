<?php
session_start();

// Verifica sessão e perfil
if (
    !isset($_SESSION['login']['usuario']['id']) ||
    $_SESSION['login']['usuario']['perfil'] !== 'adm'
) {
    echo "Acesso negado. Você não tem permissão para acessar esta página.";
    exit();
}

$adminId = intval($_SESSION['login']['usuario']['id']);

require_once __DIR__ . '/core/conexao.php';
$con = conecta();

/* =====================================================
   1) JOGOS FUTUROS
===================================================== */
$sqlJogos = "
SELECT 
    j.id,
    j.local,
    j.data,
    t1.nome AS time1,
    t2.nome AS time2
FROM jogo j
LEFT JOIN time t1 ON t1.id = j.id_time1
LEFT JOIN time t2 ON t2.id = j.id_time2
WHERE j.data >= NOW()
ORDER BY j.data ASC
";

$resJogos = $con->query($sqlJogos);
$jogos = $resJogos->fetch_all(MYSQLI_ASSOC);

/* =====================================================
   2) ÚLTIMOS USUÁRIOS
===================================================== */
$sqlUsers = "
SELECT id, nome, email, perfil
FROM usuario
ORDER BY id DESC
LIMIT 10
";
$resUsers = $con->query($sqlUsers);
$usuarios = $resUsers->fetch_all(MYSQLI_ASSOC);

/* =====================================================
   3) ENCONTROS PENDENTES
===================================================== */
$sqlEncontros = "
SELECT 
    aet.id AS id_registro,
    tor.nome AS nome_torcedor,
    atl.nome AS nome_atleta,
    ae.horario_inicial,
    aet.status
FROM atleta_encontro_torcedor aet
JOIN atleta_encontro ae ON ae.id = aet.id_atleta_encontro
JOIN usuario tor ON tor.id = aet.id_torcedor
JOIN atleta a ON a.id = ae.id_atleta
JOIN usuario atl ON atl.id = a.id_usuario
WHERE aet.status = 'pendente'
ORDER BY ae.horario_inicial ASC
";

$resEncontros = $con->query($sqlEncontros);
$encontrosPendentes = $resEncontros->fetch_all(MYSQLI_ASSOC);

/* =====================================================
   4) PERGUNTAS PARA O ADMINISTRADOR (AJUDA)
===================================================== */

/* PERGUNTAS PENDENTES */
$sqlPendentes = "
SELECT a.id, a.pergunta, u.nome AS nome_usuario, a.data_pergunta
FROM ajuda a
JOIN usuario u ON u.id = a.id_usuario
WHERE a.status = 'pendente'
ORDER BY a.data_pergunta DESC
";
$resPend = $con->query($sqlPendentes);
$ajudaPendentes = $resPend->fetch_all(MYSQLI_ASSOC);

/* PERGUNTAS RESPONDIDAS RECENTES */
$sqlResp = "
SELECT a.id, a.pergunta, a.resposta, u.nome AS nome_usuario, a.data_resposta
FROM ajuda a
JOIN usuario u ON u.id = a.id_usuario
WHERE a.status = 'respondida'
ORDER BY a.data_resposta DESC
LIMIT 10
";
$resResp = $con->query($sqlResp);
$ajudaRespondidas = $resResp->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Notificações do Administrador</title>
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

.container {
    max-width: 900px;
    margin: 24px auto;
    padding: 0 16px;
}

.section-title {
    margin: 25px 0 12px 5px;
    font-size: 18px;
    font-weight: 600;
    color: #444;
}

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

.pendente { color:#d47900; }

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
    <a href="tela_principal_adm.php" class="back">←</a>
    <h1>Notificações do Administrador</h1>
    <div></div>
</div>

<div class="container">

    <!-- JOGOS FUTUROS -->
    <div class="section-title">Próximos jogos cadastrados</div>

    <?php if (empty($jogos)): ?>
        <p style="color:#777; margin-left:6px;">Nenhum jogo encontrado.</p>
    <?php else: ?>
        <?php foreach ($jogos as $j):
            $dataFmt = date("d/m/Y H:i", strtotime($j['data']));
        ?>
            <div class="card">
                <strong><?= htmlspecialchars($j['time1']) ?> vs <?= htmlspecialchars($j['time2']) ?></strong><br>
                Local: <?= htmlspecialchars($j['local']) ?><br>
                Data: <?= $dataFmt ?><br>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <!-- ÚLTIMOS USUÁRIOS -->
    <div class="section-title">Últimos usuários cadastrados</div>

    <?php foreach ($usuarios as $u): ?>
        <div class="card">
            <strong><?= htmlspecialchars($u['nome']) ?></strong><br>
            Email: <?= htmlspecialchars($u['email']) ?><br>
            Perfil: <?= htmlspecialchars($u['perfil']) ?><br>
        </div>
    <?php endforeach; ?>



    <!-- PERGUNTAS PENDENTES -->
    <div class="section-title">Perguntas pendentes (Ajuda)</div>

    <?php if (empty($ajudaPendentes)): ?>
        <p style="color:#777; margin-left:6px;">Nenhuma pergunta pendente.</p>
    <?php else: ?>
        <?php foreach ($ajudaPendentes as $p): ?>
            <div class="card">
                <strong><?= htmlspecialchars($p['nome_usuario']) ?> perguntou:</strong><br>
                <?= nl2br(htmlspecialchars($p['pergunta'])) ?><br>
                <small>Enviado em <?= date("d/m/Y H:i", strtotime($p['data_pergunta'])) ?></small>

                <a href="ajuda_adm.php?id=<?= $p['id'] ?>" class="btn">Responder</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <!-- PERGUNTAS RESPONDIDAS -->
    <div class="section-title">Últimas perguntas respondidas</div>

    <?php if (empty($ajudaRespondidas)): ?>
        <p style="color:#777; margin-left:6px;">Nenhuma pergunta respondida ainda.</p>
    <?php else: ?>
        <?php foreach ($ajudaRespondidas as $p): ?>
            <div class="card">
                <strong><?= htmlspecialchars($p['nome_usuario']) ?> perguntou:</strong><br>
                <?= nl2br(htmlspecialchars($p['pergunta'])) ?><br><br>

                <strong>Resposta do admin:</strong><br>
                <?= nl2br(htmlspecialchars($p['resposta'])) ?><br>

                <small>Respondido em <?= date("d/m/Y H:i", strtotime($p['data_resposta'])) ?></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>
