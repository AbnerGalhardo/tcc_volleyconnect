<?php
session_start();

$userId = null;
$perfil = null;
if (isset($_SESSION['login']['usuario']['id'])) {
    $userId = intval($_SESSION['login']['usuario']['id']);
    $perfil = $_SESSION['login']['usuario']['perfil'] ?? null;
} elseif (isset($_SESSION['id_usuario'])) {
    $userId = intval($_SESSION['id_usuario']);
    $perfil = $_SESSION['perfil'] ?? null;
} elseif (isset($_SESSION['id'])) {
    $userId = intval($_SESSION['id']);
    $perfil = $_SESSION['perfil'] ?? null;
}

if (!$userId) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/core/conexao.php';
$con = conecta();

$encontros = [];

if ($perfil === 'atleta') {
    $sqlAt = "SELECT id FROM atleta WHERE id_usuario = ? LIMIT 1";
    $stAt = $con->prepare($sqlAt);
    $stAt->bind_param("i", $userId);
    $stAt->execute();
    $resAt = $stAt->get_result();
    $rowAt = $resAt->fetch_assoc();
    $stAt->close();

    if ($rowAt) {
        $id_atleta = intval($rowAt['id']);

        $sql = "
        SELECT 
            aet.id AS registro_id,
            ae.id AS atleta_encontro_id,
            u.nome AS nome_torcedor,
            u.foto AS foto_torcedor,
            t.nome AS nome_time,
            j.local AS local_jogo,
            j.data AS data_jogo,
            ae.horario_inicial AS horario_encontro,
            ae.duracao AS duracao_minutos,
            aet.status
        FROM atleta_encontro_torcedor aet
        JOIN atleta_encontro ae ON ae.id = aet.id_atleta_encontro
        JOIN usuario u ON u.id = aet.id_torcedor
        LEFT JOIN atleta a ON a.id = ae.id_atleta
        LEFT JOIN time t ON t.id = a.id_time
        LEFT JOIN jogo j ON j.id = ae.id_jogo
        WHERE ae.id_atleta = ?
          AND (aet.status = 'confirmado' OR aet.status = 'pendente')  -- mostra pendentes e confirmados
        ORDER BY ae.horario_inicial DESC
        ";
        $st = $con->prepare($sql);
        $st->bind_param("i", $id_atleta);
        $st->execute();
        $res = $st->get_result();
        $encontros = $res->fetch_all(MYSQLI_ASSOC);
        $st->close();
    } else {
        $encontros = [];
    }

} else {
    $sql = "
    SELECT 
        aet.id AS registro_id,
        ae.id AS atleta_encontro_id,
        u2.nome AS nome_atleta,
        t.nome AS nome_time,
        j.local AS local_jogo,
        j.data AS data_jogo,
        ae.horario_inicial AS horario_encontro,
        ae.duracao AS duracao_minutos,
        aet.status
    FROM atleta_encontro_torcedor aet
    JOIN atleta_encontro ae ON ae.id = aet.id_atleta_encontro
    JOIN atleta a ON a.id = ae.id_atleta
    LEFT JOIN usuario u2 ON u2.id = a.id_usuario
    LEFT JOIN time t ON t.id = a.id_time
    LEFT JOIN jogo j ON j.id = ae.id_jogo
    WHERE aet.id_torcedor = ?
    ORDER BY ae.horario_inicial DESC
    ";
    $st = $con->prepare($sql);
    $st->bind_param("i", $userId);
    $st->execute();
    $res = $st->get_result();
    $encontros = $res->fetch_all(MYSQLI_ASSOC);
    $st->close();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Meus Encontros</title>
<link rel="stylesheet" href="css/meus_encontros.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
<style>
body { margin: 0; font-family: Arial, sans-serif; background: #F4F4F8; color: #111; }
.topo { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
.topo h1 { margin:0; font-size:20px; }
.container { max-width:980px; margin:24px auto; padding:0 16px; }
.card { background:#fff; border-radius:10px; padding:14px; margin-bottom:14px; box-shadow:0 1px 3px rgba(0,0,0,0.05); display:grid; grid-template-columns:1fr auto; gap:8px 16px; align-items:center; }
.card .left { line-height:1.3; }
.meta { color:#666; font-size:14px; margin-top:6px; }
.btn-primary { background:#8a4fff; color:#fff; padding:8px 12px; border-radius:8px; text-decoration:none; font-weight:600; }
.empty { text-align:center; padding:40px; background:#fff; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.03); }
.small { font-size:13px; color:#777; }
.status { font-weight:700; font-size:13px; padding:6px 10px; border-radius:6px; color:#fff; }
.status.confirmado { background: #28a745; }
.status.pendente { background: #f0ad4e; }
.status.recusado { background: #d9534f; }
</style>
</head>
<body>

<div class="topo">
    <a href="<?= ($perfil === 'atleta') ? 'config_atleta.php' : 'index.php' ?>" class="back">←</a>
    <h1>Meus Encontros</h1>
    <div></div>
</div>

<div class="container">
    <?php if (empty($encontros)): ?>
        <div class="empty">
            <?php if ($perfil === 'atleta'): ?>
                <p style="font-size:18px; margin:0 0 8px 0;">Nenhum torcedor marcou encontro com você.</p>
                <p class="small">Aguarde até um torcedor marcar um encontro.</p>
            <?php else: ?>
                <p style="font-size:18px; margin:0 0 8px 0;">Você ainda não marcou nenhum encontro.</p>
                <p class="small">Vá até o cronograma e escolha um atleta para marcar seu encontro.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php foreach ($encontros as $row):
            $dataJogoRaw = $row['data_jogo'];
            $horarioEncontroRaw = $row['horario_encontro'];

            $dataJogo = $dataJogoRaw ? date("d/m/Y", strtotime($dataJogoRaw)) : '—';
            $horaJogo = $dataJogoRaw ? date("H:i", strtotime($dataJogoRaw)) : '—';
            $horarioEncontro = $horarioEncontroRaw ? date("d/m/Y H:i", strtotime($horarioEncontroRaw)) : '—';

            $status = $row['status'] ?? 'pendente';
        ?>
            <div class="card">
                <div class="left">
                    <?php if ($perfil === 'atleta'): ?>
                        <strong><?= htmlspecialchars($row['nome_torcedor'] ?? '—') ?></strong>
                    <?php else: ?>
                        <strong><?= htmlspecialchars($row['nome_atleta'] ?? '—') ?></strong>
                    <?php endif; ?>

                    <div class="meta">
                        <span><strong>Local do jogo:</strong> <?= htmlspecialchars($row['local_jogo'] ?? '—') ?></span><br>
                        <span><strong>Data do jogo:</strong> <?= $dataJogo ?> <?= ($horaJogo !== '—' ? "às $horaJogo" : '') ?></span><br>
                        <span><strong>Horário do encontro:</strong> <?= $horarioEncontro ?></span>
                    </div>
                </div>

                <div class="right">
                    <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
                        <span class="status <?= htmlspecialchars($status) ?>"><?= ucfirst(htmlspecialchars($status)) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
