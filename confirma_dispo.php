<?php
session_start();

if (!isset($_SESSION['login']['usuario']['id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/core/conexao.php';
$con = conecta();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Requisição inválida.";
    exit;
}

$id_reserva = intval($_GET['id']);

$sql = "
SELECT 
  aet.id AS id_reserva,
  aet.id_torcedor,
  u.nome AS nome_torcedor,
  u.foto AS foto_torcedor,
  ae.horario_inicial
FROM atleta_encontro_torcedor aet
JOIN atleta_encontro ae ON ae.id = aet.id_atleta_encontro
JOIN usuario u ON u.id = aet.id_torcedor
WHERE aet.id = ?
LIMIT 1
";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id_reserva);
$stmt->execute();
$res = $stmt->get_result();
$dados = $res->fetch_assoc();
$stmt->close();

if (!$dados) {
    echo "Reserva não encontrada.";
    exit;
}

$dt = $dados['horario_inicial'];
$data_formatada = $dt ? date("d/m/Y", strtotime($dt)) : '—';
$hora_formatada = $dt ? date("H:i", strtotime($dt)) : '—';

$foto = !empty($dados['foto_torcedor']) ? $dados['foto_torcedor'] : 'img/atletas/default.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Confirmar Encontro</title>
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
<style>

body {
    background: linear-gradient(135deg, #0D0B4F, #4118A8);
    height:100vh;
    margin:0;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family: Arial, sans-serif;
}

/* Modal */
.modal {
    background:#fff;
    width:420px;
    padding:30px 30px 32px;
    border-radius:14px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.25);
    text-align:center;
}

/* Logo */
.logo { 
    width:90px; 
    margin-bottom:15px; 
}

/* Torcedor */
.torcedor {
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-bottom:15px;
}

.torcedor img {
    width:80px;
    height:80px;
    border-radius:50%;
    object-fit:cover;
    margin-bottom:10px;
    border:3px solid #AD39FF;
}

/* Texto */
.texto { 
    font-size:18px; 
    line-height:1.5; 
    margin-bottom:25px;
    color:#111; 
}

/* Botões */
.botoes {
    display:flex;
    justify-content:center;
    gap:15px;
}

.btn {
  padding:12px 18px;
  border-radius:8px;
  font-weight:700;
  text-decoration:none;
  font-size:15px;
  cursor:pointer;
  border:none;
  width:130px;
}

.confirmar { 
    background:#AD39FF; 
    color:#fff; 
}

.cancelar { 
    background:#777; 
    color:#fff; 
}

.confirmar:hover { background:#922DDB; }
.cancelar:hover  { background:#5e5e5e; }

</style>
</head>
<body>

<div class="modal">

    <img src="img/logo.png" class="logo" alt="logo">

    <div class="torcedor">
        <div style="font-weight:700; font-size:17px;">
            <?= htmlspecialchars($dados['nome_torcedor']) ?>
        </div>
        <div style="font-size:13px;color:#666;">Reservou este horário</div>
    </div>

    <p class="texto">
        Deseja confirmar o encontro às 
        <b><?= $hora_formatada ?></b> 
        no dia 
        <b><?= $data_formatada ?></b>?
    </p>

    <div class="botoes">
        <form method="post" action="confirma_dispo_action.php">
            <input type="hidden" name="id_reserva" value="<?= intval($id_reserva) ?>">
            <button type="submit" class="btn confirmar">Confirmar</button>
        </form>

        <a href="disponibilidade.php" class="btn cancelar">Cancelar</a>
    </div>

</div>

</body>
</html>
