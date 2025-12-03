<?php
session_start();

// --- 1) Determina id do usuário logado (tenta várias chaves que seu projeto pode usar)
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
    // não logado -> redireciona para login
    header("Location: login.php");
    exit;
}

// --- 2) Conexão (path correto)
require_once __DIR__ . '/core/conexao.php';
$con = conecta();

// --- 3) Query: pega todos os encontros do torcedor, com dados do atleta, time, jogo e horário do encontro
$sql = "
SELECT 
    aet.id AS registro_id,
    ae.id AS atleta_encontro_id,
    u.nome AS nome_atleta,
    t.nome AS nome_time,
    j.local AS local_jogo,
    j.data AS data_jogo,
    ae.horario_inicial AS horario_encontro,
    ae.duracao AS duracao_minutos
FROM atleta_encontro_torcedor aet
JOIN atleta_encontro ae ON ae.id = aet.id_atleta_encontro
JOIN atleta a ON a.id = ae.id_atleta
LEFT JOIN usuario u ON u.id = a.id_usuario
LEFT JOIN time t ON t.id = a.id_time
LEFT JOIN jogo j ON j.id = ae.id_jogo
WHERE aet.id_torcedor = ?
ORDER BY ae.horario_inicial DESC
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();

$encontros = $res->fetch_all(MYSQLI_ASSOC);

$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Meus Encontros</title>
<link rel="stylesheet" href="css/meus_encontros.css">
<style>
/* Estilo minimal compatível com seu projeto (ajuste se já tiver CSS separado) */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #F4F4F8;
    color: #111;
}
.topo {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:18px 22px;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.topo h1 { margin:0; font-size:20px; }
.container {
    max-width: 980px;
    margin: 24px auto;
    padding: 0 16px;
}
.card {
    background: #fff;
    border-radius: 10px;
    padding: 14px;
    margin-bottom: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px 16px;
    align-items: center;
}
.card .left {
    line-height:1.3;
}
.meta { color: #666; font-size:14px; margin-top:6px; }
.btn-primary {
    background:#8a4fff;
    color:#fff;
    padding:8px 12px;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
}
.empty {
    text-align:center;
    padding:40px;
    background: #fff;
    border-radius:10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.small { font-size:13px; color:#777; }
</style>
</head>
<body>

<div class="topo">
    <a href="config_atleta.php" class="back">←</a>
    <h1>Meus Encontros</h1>
    <div></div>
</div>

<div class="container">
    <?php if (empty($encontros)): ?>
        <div class="empty">
            <p style="font-size:18px; margin:0 0 8px 0;">Nenhum torcedor marcou encontro com você.</p>
            <p class="small">Aguarde até um torcedor marcar um encontro.</p>
        </div>
    <?php else: ?>
        <?php foreach ($encontros as $row): 
            // Preparar exibição de datas
            $dataJogoRaw = $row['data_jogo'];
            $horarioEncontroRaw = $row['horario_encontro'];

            $dataJogo = $dataJogoRaw ? date("d/m/Y", strtotime($dataJogoRaw)) : '—';
            $horaJogo = $dataJogoRaw ? date("H:i", strtotime($dataJogoRaw)) : '—';
            $horarioEncontro = $horarioEncontroRaw ? date("d/m/Y H:i", strtotime($horarioEncontroRaw)) : '—';
        ?>
            <div class="card">
                <div class="left">
                    <strong><?= htmlspecialchars($row['nome_atleta'] ?? '—') ?></strong>
                    <div class="meta">
                        <span><strong>Time:</strong> <?= htmlspecialchars($row['nome_time'] ?? '—') ?></span><br>
                        <span><strong>Local do jogo:</strong> <?= htmlspecialchars($row['local_jogo'] ?? '—') ?></span><br>
                        <span><strong>Data do jogo:</strong> <?= $dataJogo ?> <?= ($horaJogo !== '—' ? "às $horaJogo" : '') ?></span><br>
                        <span><strong>Horário do encontro:</strong> <?= $horarioEncontro ?></span>
                    </div>
                </div>

                <div class="right">
                    <a class="btn-primary" href="detalhe_encontro.php?id=<?= intval($row['atleta_encontro_id']) ?>">Ver</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
