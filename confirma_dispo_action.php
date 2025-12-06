<?php
session_start();
if (!isset($_SESSION['login']['usuario']['id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/core/conexao.php';
$con = conecta();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_reserva'])) {
    header("Location: disponibilidade.php");
    exit();
}

$id_reserva = intval($_POST['id_reserva']);

// Verifica se a reserva existe e pertence a um atleta do usuário logado (segurança)
$sqlCheck = "
SELECT aet.id, ae.id_atleta
FROM atleta_encontro_torcedor aet
JOIN atleta_encontro ae ON ae.id = aet.id_atleta_encontro
JOIN atleta a ON a.id = ae.id_atleta
WHERE aet.id = ?
LIMIT 1
";
$stmt = $con->prepare($sqlCheck);
$stmt->bind_param("i", $id_reserva);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    // reserva não encontrada
    header("Location: disponibilidade.php?erro=nao_encontrado");
    exit();
}

// opcional: validar que o usuário logado é o atleta dono (comparar $_SESSION login usuario id com atleta.id_usuario)
// vou checar rapidamente:
$atletaId = intval($row['id_atleta']);
$stmt2 = $con->prepare("SELECT id_usuario FROM atleta WHERE id = ? LIMIT 1");
$stmt2->bind_param("i", $atletaId);
$stmt2->execute();
$r2 = $stmt2->get_result()->fetch_assoc();
$stmt2->close();

$usuarioLogadoId = intval($_SESSION['login']['usuario']['id'] ?? 0);
if ($r2 && intval($r2['id_usuario']) !== $usuarioLogadoId) {
    header("Location: disponibilidade.php?erro=sem_permissao");
    exit();
}

// Agora atualiza o status (necessário ter coluna 'status' na tabela)
$update = $con->prepare("UPDATE atleta_encontro_torcedor SET status = 'confirmado' WHERE id = ?");
$update->bind_param("i", $id_reserva);
$ok = $update->execute();
$update->close();

if ($ok) {
    header("Location: tela_principal_atleta.php?ok=confirmado");
    exit();
} else {
    header("Location: disponibilidade.php?erro=update");
    exit();
}
