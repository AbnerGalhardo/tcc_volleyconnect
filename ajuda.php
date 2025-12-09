<?php
session_start();
require_once __DIR__ . '/core/conexao.php';
require_once __DIR__ . '/core/sql.php';
require_once __DIR__ . '/core/mysql.php';
$con = conecta();

// garante que está logado
if (!isset($_SESSION['login']['usuario']['id'])) {
    header("Location: login.php");
    exit;
}
$userId = intval($_SESSION['login']['usuario']['id']);
$userName = $_SESSION['login']['usuario']['nome'] ?? 'Usuário';

// POST: novo pedido de ajuda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pergunta'])) {
    $pergunta = trim($_POST['pergunta']);
    if ($pergunta !== '') {
        $campos_ajuda = [
            'id_usuario' => $userId,
            'pergunta' => $pergunta
        ];

        $idPergunta = insere('ajuda', $campos_ajuda);
        // redireciona para evitar re-submissão
        header("Location: ajuda.php?ok=1");
        exit;
    } else {
        $erro = "Pergunta vazia não pode ser enviada.";
    }
}

// opcional: excluir própria pergunta (via GET para simplicidade; exige confirmação no front)
if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    $idExcluir = intval($_GET['id']);
    // só exclui se for do usuário logado
    $st = $con->prepare("DELETE FROM ajuda WHERE id = ? AND id_usuario = ?");
    $st->bind_param("ii", $idExcluir, $userId);
    $st->execute();
    $st->close();
    header("Location: ajuda.php?excluido=1");
    exit;
}

// Busca perguntas (mostramos todas, respondidas e pendentes)
// Você pode optar por mostrar só as publicadas; aqui mostramos todas para transparência.
$sql = "
SELECT a.id, a.id_usuario, u.nome AS autor, a.pergunta, a.resposta, a.data_pergunta, a.data_resposta, a.status
FROM ajuda a
JOIN usuario u ON u.id = a.id_usuario
ORDER BY a.data_pergunta DESC
";
$res = $con->query($sql);
$perguntas = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Ajuda & Perguntas Frequentes</title>
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
<style>
/* estilo consistente com o site */
body { margin:0; font-family:Arial, sans-serif; background:#F4F4F8; color:#111; }
.topo { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
.topo h1 { margin:0; font-size:20px; }
.container { max-width:980px; margin:22px auto; padding:0 16px; }
.card { background:#fff; padding:14px; border-radius:10px; margin-bottom:12px; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
.form-box { background:#fff; padding:16px; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.04); margin-bottom:14px; }
textarea { width:100%; min-height:100px; padding:10px; font-size:14px; border-radius:8px; border:1px solid #ddd; resize:vertical; }
.btn { background:#8a4fff; color:#fff; padding:10px 14px; border-radius:8px; text-decoration:none; display:inline-block; font-weight:700; border:none; cursor:pointer; }
.meta { color:#666; font-size:13px; margin-top:8px; }
.status { padding:6px 10px; border-radius:8px; color:#fff; font-weight:700; }
.status.pendente { background:#f0ad4e; }
.status.respondida { background:#28a745; }
.small { font-size:13px; color:#777; }
.actions { margin-top:10px; display:flex; gap:8px; }
.link-del { color:#c0392b; text-decoration:none; font-weight:700; }
</style>
</head>
<body>

<div class="topo">
    <a href="config.php" class="back">←</a>
    <h1>Ajuda e Perguntas Frequentes</h1>
    <div></div>
</div>

<div class="container">

    <div class="form-box card">
        <strong>Enviar uma pergunta / pedir ajuda</strong>
        <form method="post" style="margin-top:10px;">
            <textarea name="pergunta" placeholder="Descreva sua dúvida ou peça de ajuda..."></textarea>
            <div style="margin-top:10px;">
                <button class="btn" type="submit">Enviar pergunta</button>
                <?php if (!empty($erro)): ?><div class="small" style="color:#b00020;margin-top:8px;"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
            </div>
        </form>
    </div>

    <div style="margin-bottom:10px;"><strong>Perguntas recentes</strong></div>

    <?php if (empty($perguntas)): ?>
        <div class="card">
            <p class="small">Ainda não há perguntas. Seja o primeiro a perguntar.</p>
        </div>
    <?php else: ?>
        <?php foreach ($perguntas as $p): ?>
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:start;">
                    <div>
                        <strong><?= htmlspecialchars($p['autor']) ?></strong>
                        <div class="meta"><?= date("d/m/Y H:i", strtotime($p['data_pergunta'])) ?></div>
                    </div>
                    <div>
                        <span class="status <?= $p['status'] === 'respondida' ? 'respondida' : 'pendente' ?>">
                            <?= ucfirst($p['status']) ?>
                        </span>
                    </div>
                </div>

                <div style="margin-top:12px; white-space:pre-wrap;"><?= nl2br(htmlspecialchars($p['pergunta'])) ?></div>

                <?php if (!empty($p['resposta'])): ?>
                    <div style="margin-top:12px; padding:12px; background:#f6f6f9; border-radius:8px;">
                        <strong>Resposta do administrador</strong>
                        <div class="meta"><?= date("d/m/Y H:i", strtotime($p['data_resposta'])) ?></div>
                        <div style="margin-top:8px; white-space:pre-wrap;"><?= nl2br(htmlspecialchars($p['resposta'])) ?></div>
                    </div>
                <?php endif; ?>

                <div class="actions">
                    <?php if (intval($p['id_usuario']) === $userId): ?>
                        <a class="link-del" href="ajuda.php?acao=excluir&id=<?= intval($p['id']) ?>" onclick="return confirm('Deseja realmente excluir sua pergunta?')">Excluir</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>
