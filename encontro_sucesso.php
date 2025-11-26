<?php
include 'includes/header.php';
include 'config.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT e.*, u.nome as usuario_nome, a.nome as atleta_nome, a.foto as atleta_foto, j.data_hora
                       FROM encontros e
                       JOIN users u ON u.id = e.user_id
                       JOIN atletas a ON a.id = e.atleta_id
                       JOIN jogos j ON j.id = e.jogo_id
                       WHERE e.id = :id");
$stmt->execute(['id'=>$id]);
$en = $stmt->fetch();
if (!$en) {
    die('Encontro não encontrado.');
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><link rel="stylesheet" href="css/confirmar_encontro.css"></head>
<body>
<div class="modal">
    <img src="<?= htmlspecialchars($en['atleta_foto']) ?>" class="foto">
    <p class="texto">
        Seu encontro com <b><?= htmlspecialchars($en['atleta_nome']) ?></b> foi solicitado.<br>
        Data/hora: <?= date('d/m/Y \à\s H:i', strtotime($en['data_hora'])) ?>
    </p>
    <div class="botoes">
        <a class="btn confirmar" href="perfil.php">Ok</a>
    </div>
</div>
</body>
</html>
