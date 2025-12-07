<?php
session_start();
require_once __DIR__ . '/core/conexao.php';
$con = conecta();

// verifica se é admin
if (!isset($_SESSION['login']['usuario']['id']) || ($_SESSION['login']['usuario']['perfil'] ?? '') !== 'adm') {
    echo "Acesso negado. Você não tem permissão para acessar esta página.";
    exit;
}

// ação: responder (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['responder']) && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);
    $resposta = trim($_POST['resposta'] ?? '');
    if ($resposta !== '') {
        $stmt = $con->prepare("UPDATE ajuda SET resposta = ?, data_resposta = NOW(), status = 'respondida' WHERE id = ?");
        $stmt->bind_param("si", $resposta, $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ajuda_adm.php");
    exit;
}

// ação: excluir (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir']) && isset($_POST['id_excluir']) && is_numeric($_POST['id_excluir'])) {
    $id_ex = intval($_POST['id_excluir']);
    $st = $con->prepare("DELETE FROM ajuda WHERE id = ?");
    $st->bind_param("i", $id_ex);
    $st->execute();
    $st->close();
    header("Location: ajuda_adm.php");
    exit;
}

// busca todas perguntas
$res = $con->query("SELECT a.id, a.id_usuario, u.nome AS autor, a.pergunta, a.resposta, a.data_pergunta, a.data_resposta, a.status FROM ajuda a JOIN usuario u ON u.id = a.id_usuario ORDER BY a.data_pergunta DESC");
$perguntas = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Ajuda — Administração</title>
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
<style>
body { margin:0; font-family:Arial, sans-serif; background:#F4F4F8; color:#111; }
.topo { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
.topo h1 { margin:0; font-size:20px; }
.container { max-width:980px; margin:22px auto; padding:0 16px; }
.card { background:#fff; padding:14px; border-radius:10px; margin-bottom:12px; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
textarea { width:100%; min-height:80px; padding:10px; font-size:14px; border-radius:8px; border:1px solid #ddd; resize:vertical; }
.btn { background:#8a4fff; color:#fff; padding:8px 12px; border-radius:8px; text-decoration:none; display:inline-block; font-weight:700; border:none; cursor:pointer; }
.btn-danger { background:#c0392b; }
.small { font-size:13px; color:#777; }
.meta { color:#666; font-size:13px; margin-top:6px; }
</style>
</head>
<body>

<div class="topo">
    <a href="config_adm.php" class="back">←</a>
    <h1>Ajuda — Administração</h1>
    <div></div>
</div>

<div class="container">

    <?php if (empty($perguntas)): ?>
        <div class="card"><p class="small">Não há perguntas no momento.</p></div>
    <?php else: ?>
        <?php foreach ($perguntas as $p): ?>
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:start;">
                    <div>
                        <strong><?= htmlspecialchars($p['autor']) ?></strong>
                        <div class="meta"><?= date("d/m/Y H:i", strtotime($p['data_pergunta'])) ?></div>
                    </div>
                    <div><span class="small"><?= htmlspecialchars(ucfirst($p['status'])) ?></span></div>
                </div>

                <div style="margin-top:12px; white-space:pre-wrap;"><?= nl2br(htmlspecialchars($p['pergunta'])) ?></div>

                <?php if (!empty($p['resposta'])): ?>
                    <div style="margin-top:12px; padding:12px; background:#f6f6f9; border-radius:8px;">
                        <strong>Resposta</strong>
                        <div class="meta"><?= date("d/m/Y H:i", strtotime($p['data_resposta'])) ?></div>
                        <div style="margin-top:8px; white-space:pre-wrap;"><?= nl2br(htmlspecialchars($p['resposta'])) ?></div>
                    </div>
                <?php else: ?>
                    <form method="post" style="margin-top:12px;">
                        <input type="hidden" name="id" value="<?= intval($p['id']) ?>">
                        <textarea name="resposta" placeholder="Escreva a resposta..."></textarea>
                        <div style="margin-top:8px;">
                            <button class="btn" name="responder" type="submit">Responder</button>
                            <button class="btn btn-danger" name="excluir" type="submit" formaction="?excluir=1&id_excluir=<?= intval($p['id']) ?>">Excluir</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>
