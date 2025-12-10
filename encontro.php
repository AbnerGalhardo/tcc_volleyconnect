<?php
session_start();
require_once "core/conexao.php";
require_once "core/sql.php";
require_once "core/mysql.php";
require_once "includes/valida_login.php";

$con = conecta();

$sql = "
    SELECT 
        atleta.id,
        atleta.id_time,
        usuario.nome AS nome_atleta
    FROM atleta
    INNER JOIN usuario ON usuario.id = atleta.id_usuario
    ORDER BY usuario.nome
";
$result = mysqli_query($con, $sql);
$atletas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $atletas[] = [
        "id" => $row["id"],
        "id_time" => $row["id_time"],
        "nome" => $row["nome_atleta"],
    ];
}

$jogo = isset($_GET["jogo"]) && is_numeric($_GET["jogo"]) ? intval($_GET["jogo"]) : 0;

$ids_times = [['id_time1' => null, 'id_time2' => null]];
if ($jogo) {
    $criterio_jogo = [['id', '=', $jogo]];
    $tmp = buscar('jogo', ['id_time1', 'id_time2'], $criterio_jogo);
    if (!empty($tmp) && isset($tmp[0]['id_time1'])) {
        $ids_times = $tmp;
    }
}

$id_time1 = isset($ids_times[0]['id_time1']) ? $ids_times[0]['id_time1'] : 0;
$id_time2 = isset($ids_times[0]['id_time2']) ? $ids_times[0]['id_time2'] : 0;

$atletas_filtrados = [];
foreach ($atletas as $a) {
    if ($a['id_time'] == $id_time1 || $a['id_time'] == $id_time2) {
        $atletas_filtrados[] = $a;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Selecione Seu Atleta</title>
<link rel="stylesheet" href="css/encontro.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
</head>

<body>

<header>
    <a href="cronograma_detalhes.php" class="back">←</a>
    <h2>SELECIONE SEU ATLETA PARA ENCONTRO</h2>
</header>

<form method="GET" action="confirma_encontro.php" class="lista-atletas">

    <?php if (empty($atletas_filtrados)): ?>
        <p style="font-size:22px; text-align:center; margin-top:40px; width:100%;">
            Nenhum atleta disponível para encontro neste jogo.
        </p>
    <?php else: ?>
        <?php foreach ($atletas_filtrados as $a): ?>
            <label class="card">
                <span><?= htmlspecialchars($a['nome']) ?></span>
                <input type="radio" name="atleta" value="<?= intval($a['id']) ?>" required>
            </label>
        <?php endforeach; ?>

        <button type="submit" class="continuar">Continuar</button>
    <?php endif; ?>

    <input type="hidden" name="id_jogo" value="<?= intval($jogo) ?>">

</form>

</body>
</html>
