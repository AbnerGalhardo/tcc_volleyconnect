<?php
session_start();
require_once 'includes/funcoes.php';
require_once 'core/conexao.php';
require_once 'core/sql.php';
require_once 'core/mysql.php';
include 'includes/valida_login.php';

$con = conecta();

if(!$con){
    die("Erro ao conectar com o banco.");
}

// busca todos os jogos ordenados por data
$sql = "
    SELECT 
        jogo.id,
        jogo.local,
        jogo.data,
        jogo.genero,
        jogo.id_campeonato,
        jogo.id_time1,
        jogo.id_time2,
        t1.nome AS time1,
        t2.nome AS time2,
        camp.nome AS campeonato
    FROM jogo
    LEFT JOIN `time` t1 ON jogo.id_time1 = t1.id
    LEFT JOIN `time` t2 ON jogo.id_time2 = t2.id
    LEFT JOIN `campeonato` camp ON jogo.id_campeonato = camp.id
    ORDER BY jogo.data ASC
";

$result = $con->query($sql);

// agrupa por mês numérico (1..12)
$mesesDados = []; // chave: número do mês (1..12)
if ($result && $result->num_rows > 0) {
    while ($j = $result->fetch_assoc()) {
        $ts = strtotime($j['data']);
        $mesNum = intval(date('n', $ts)); // 1..12
        $dia = date('d/m', $ts);
        $hora = date('H:i', $ts);

        $mesesDados[$mesNum][] = [
            'id' => $j['id'],
            'time1' => $j['time1'] ?? 'Time A',
            'time2' => $j['time2'] ?? 'Time B',
            'dia' => $dia,
            'hora' => $hora,
            'local' => $j['local'],
            'genero' => $j['genero'],
            'campeonato' => $j['campeonato']
        ];
    }
}

// helper nomes meses (MAIÚSCULO)
$mesesNome = [
    1 => 'JANEIRO', 2 => 'FEVEREIRO', 3 => 'MARÇO', 4 => 'ABRIL',
    5 => 'MAIO', 6 => 'JUNHO', 7 => 'JULHO', 8 => 'AGOSTO',
    9 => 'SETEMBRO', 10 => 'OUTUBRO', 11 => 'NOVEMBRO', 12 => 'DEZEMBRO'
];
?>

<link rel="stylesheet" href="css/cronograma.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">

<div class="topo">
    <a href="tela_principal.php" class="voltar">←</a>
    <h2><img src="img/calendar.png" class="icon">Cronograma</h2>
    <div class="icons-right">
    </div>
</div>

<div class="meses">
    <?php
    // se não houver jogos cadastrados, mostra mensagem padrão
    if (empty($mesesDados)) {
        echo '<p style="padding:20px">Nenhum jogo cadastrado.</p>';
    } else {
        // percorre todos os meses que têm jogos (em ordem númerica)
        ksort($mesesDados);
        foreach ($mesesDados as $mesNum => $jogos) :
            $nomeMes = $mesesNome[$mesNum] ?? strtoupper(strftime('%B', mktime(0,0,0,$mesNum,1)));
    ?>
        <div class="mes">
            <h1><?= $nomeMes ?></h1>

            <?php foreach ($jogos as $j): ?>
                <a href="cronograma_detalhes.php?id=<?= htmlspecialchars($j['id']) ?>">
                    <div class="jogo" data-mes="<?= $mesNum ?>" data-genero="<?= htmlspecialchars(strtolower($j['genero'])) ?>" data-campeonato="<?= htmlspecialchars(strtolower($j['campeonato'])) ?>">
                        <img src="img/time1.png" alt="time1">
                        <span class="x">x</span>
                        <img src="img/time2.png" alt="time2">
                        <span class="data"><?= htmlspecialchars($j['dia']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php
        endforeach;
    }
    ?>
</div>

<div class="mais-info">
    <?php if(($_SESSION['login']['usuario']['perfil']=='atleta')): ?>
        <a href="cronograma_detalhes_atleta.php" class="header-item">clique aqui</a> para mais informações
    <?php endif ?>
    <?php if(($_SESSION['login']['usuario']['perfil']=='torcedor')): ?>
        <a href="cronograma_detalhes.php">clique aqui</a> para mais informações
    <?php endif ?>
</div>
