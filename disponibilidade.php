<?php
session_start();

// --- 1) tenta descobrir ID do usuário logado em várias chaves comuns
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
    // não está logado -> redireciona para login
    header("Location: login.php");
    exit;
}

// --- 2) includes e conexão
require_once __DIR__ . '/core/conexao.php';
$con = conecta();

// --- 3) verificar se o usuário logado é realmente um atleta (buscar atleta.id onde id_usuario = $userId)
$sqlAt = "SELECT id FROM atleta WHERE id_usuario = ? LIMIT 1";
$stmtAt = $con->prepare($sqlAt);
$stmtAt->bind_param("i", $userId);
$stmtAt->execute();
$resAt = $stmtAt->get_result();
$atletaRow = $resAt->fetch_assoc();
$stmtAt->close();

if (!$atletaRow) {
    // não é atleta (ou não tem registro) -> mostrar mensagem amigável
    $mensagens = "Você não possui registro como atleta.";
    $reservas = [];
} else {
    $id_atleta = intval($atletaRow['id']);

    // --- 4) Buscar reservas pendentes para esse atleta
    // seleciona reservas (aet) com dados do torcedor e do encontro
    $sql = "
        SELECT
            aet.id AS id_reserva,
            aet.id_torcedor,
            u.nome AS nome_torcedor,
            u.foto AS foto_torcedor,
            ae.horario_inicial,
            ae.duracao,
            ae.vagas,
            ae.id AS id_atleta_encontro,
            j.local AS local_jogo,
            j.data AS data_jogo
        FROM atleta_encontro_torcedor aet
        JOIN atleta_encontro ae ON ae.id = aet.id_atleta_encontro
        JOIN usuario u ON u.id = aet.id_torcedor
        LEFT JOIN jogo j ON j.id = ae.id_jogo
        WHERE ae.id_atleta = ?
        ORDER BY ae.horario_inicial ASC
    ";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_atleta);
    $stmt->execute();
    $res = $stmt->get_result();
    $reservas = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Disponibilidade</title>
<link rel="stylesheet" href="css/disponibilidade.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
<style>
/* Estilo básico compatível com seu layout */
body { margin:0; font-family: Arial, sans-serif; background:#F6F6F8; color:#111; }
.topo { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
.topo h1{ margin:0; font-size:20px; letter-spacing:2px; }
.container { max-width:1000px; margin:22px auto; padding:0 18px; }
.slot { background:#f3efed; padding:18px; border-radius:6px; display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
.slot strong { font-size:18px; }
.slot .meta { color:#555; margin-top:6px; }
.btn { background:#111; color:#fff; padding:10px 14px; border-radius:6px; text-decoration:none; font-weight:600; }
.empty { text-align:center; padding:40px; background:#fff; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.03); }
.preview { display:flex; align-items:center; gap:14px; }
.preview img { width:64px; height:64px; object-fit:cover; border-radius:8px; }
.small { font-size:13px; color:#777; }
</style>
</head>
<body>

<div class="topo">
    <a href="cronograma_detalhes_atleta.php" style="text-decoration:none; color:#111;">←</a>
    <h1>DISPONIBILIDADE</h1>
    <div></div>
</div>

<div class="container">
    <?php if (isset($mensagens)): ?>
        <div class="empty">
            <p style="font-size:18px; margin:0 0 8px 0;"><?= htmlspecialchars($mensagens) ?></p>
        </div>
    <?php elseif (empty($reservas)): ?>
        <div class="empty">
            <p style="font-size:18px; margin:0 0 8px 0;">Nenhum agendamento pendente para confirmação.</p>
            <p class="small">Quando torcedores reservarem horários, eles aparecerão aqui para que você confirme.</p>
        </div>
    <?php else: ?>
        <?php foreach ($reservas as $r): 
            $dtRaw = $r['horario_inicial'];
            $data = $dtRaw ? date("d/m/Y", strtotime($dtRaw)) : '—';
            $hora = $dtRaw ? date("H:i", strtotime($dtRaw)) : '—';
            $foto = $r['foto_torcedor'] ? $r['foto_torcedor'] : 'img/atletas/default.png';
        ?>
            <div class="slot">
                <div>
                    <div class="preview">
                        <img src="<?= htmlspecialchars($foto) ?>" alt="foto">
                        <div>
                            <strong>Encontro das <?= htmlspecialchars($hora) ?> às <?= date("H:i", strtotime($dtRaw . " +{$r['duracao']} minutes")) ?></strong>
                            <div class="meta"><?= htmlspecialchars($r['nome_torcedor']) ?> — <?= htmlspecialchars($r['local_jogo'] ?? '') ?></div>
                            <div class="small">Data do jogo: <?= htmlspecialchars($data) ?></div>
                        </div>
                    </div>
                </div>

                <div>
                    <a class="btn" href="confirma_dispo.php?id=<?= intval($r['id_reserva']) ?>">Confirmar</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
