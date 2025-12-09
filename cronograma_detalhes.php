<?php
require_once 'includes/funcoes.php';
require_once 'core/conexao.php';
require_once 'core/sql.php';
require_once 'core/mysql.php';

$con = conecta();

if(!$con){
    die("Erro ao conectar com o banco.");
}

// opcional: filtrar por mês via GET (mes=1..12 ou 'todos')
$mesFilter = isset($_GET['mes']) ? $_GET['mes'] : 'todos';
$mesInt = null;
if ($mesFilter !== 'todos' && is_numeric($mesFilter)) {
    $mesInt = intval($mesFilter);
}

// busca jogos (aplica filtro por mês se fornecido)
$sql = "
    SELECT 
        jogo.id,
        jogo.local,
        jogo.data,
        jogo.genero,
        t1.nome AS time1,
        t2.nome AS time2,
        camp.nome AS campeonato
    FROM jogo
    LEFT JOIN time t1 ON jogo.id_time1 = t1.id
    LEFT JOIN time t2 ON jogo.id_time2 = t2.id
    LEFT JOIN campeonato camp ON jogo.id_campeonato = camp.id
";

if ($mesInt) {
    // seleciona mês (MONTH(data) = ?)
    $sql .= " WHERE MONTH(jogo.data) = " . $mesInt;
}
$sql .= " ORDER BY jogo.data ASC";

$result = $con->query($sql);

// preparar lista de jogos em array para renderizar
$jogosList = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ts = strtotime($row['data']);
        $mesNum = intval(date('n', $ts));
        $dia = date('d/m', $ts);
        $hora = date('H:i', $ts);

        $jogosList[] = [
            'id' => $row['id'],
            'time1' => $row['time1'] ?? 'Time A',
            'time2' => $row['time2'] ?? 'Time B',
            'campeonato' => $row['campeonato'] ?? '',
            'local' => $row['local'] ?? '',
            'dia' => $dia,
            'hora' => $hora,
            'mes' => $mesNum,
            'genero' => strtolower($row['genero'] ?? ''),
        ];
    }
}

// nomes meses
$meses = [
    "JANEIRO","FEVEREIRO","MARÇO","ABRIL","MAIO","JUNHO",
    "JULHO","AGOSTO","SETEMBRO","OUTUBRO","NOVEMBRO","DEZEMBRO"
];
?>

<link rel="stylesheet" href="css/cronograma_detalhes.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
<div class="container-cronograma">

    <a href="cronograma.php" class="voltar-topo" aria-label="Voltar">←</a>

    <div class="titulo-area">
        <select id="select-mes" class="select-mes" aria-label="Selecionar mês"></select>
        <h1 id="titulo-mes" class="titulo">JANEIRO</h1>
    </div>

    <div class="filtros">
        <label class="filtro">
            <span class="label-text">GÊNERO</span>
            <select id="select-genero" aria-label="Selecionar gênero">
                <option value="todos">Todos</option>
                <option value="masculino">Masculino</option>
                <option value="feminino">Feminino</option>
            </select>
        </label>

        <label class="filtro">
            <span class="label-text">CAMPEONATO</span>
            <select id="select-campeonato" aria-label="Selecionar campeonato">
                <option value="todos">Todos</option>
                <?php
                // popula lista de campeonatos distintos (dinâmico)
                $campSql = "SELECT DISTINCT nome FROM campeonato ORDER BY nome ASC";
                $campRes = $con->query($campSql);
                if ($campRes && $campRes->num_rows > 0) {
                    while($c = $campRes->fetch_assoc()) {
                        $val = strtolower($c['nome']);
                        echo '<option value="'.htmlspecialchars($val).'">'.htmlspecialchars($c['nome']).'</option>';
                    }
                }
                ?>
            </select>
        </label>
    </div>

    <div class="lista-jogos" id="lista-jogos">

        <?php if (empty($jogosList)): ?>
            <p style="padding:20px">Nenhum jogo encontrado para este período.</p>
        <?php endif; ?>

        <?php foreach ($jogosList as $item): ?>

            <?php
                    $criterio_jogo = [
                        ['id', '=', $item['id']]
                    ];

                    $id_time1 = buscar('jogo', ['id_time1'], $criterio_jogo);
                    $id_time2 = buscar('jogo', ['id_time2'], $criterio_jogo);

                    $criterio_time1 = [
                        ['id', '=', $id_time1[0]['id_time1']]
                    ];

                    $criterio_time2 = [
                        ['id', '=', $id_time2[0]['id_time2']]
                    ];

                    $logo1 = buscar('time', ['logo'], $criterio_time1);
                    $logo2 = buscar('time', ['logo'], $criterio_time2);
                ?>

            <div class="item"
                 data-mes="<?= htmlspecialchars($item['mes']) ?>"
                 data-genero="<?= htmlspecialchars($item['genero']) ?>"
                 data-campeonato="<?= htmlspecialchars(strtolower($item['campeonato'])) ?>">

                <div class="left">
                    <img src="<?=$logo1[0]['logo']?>" alt="<?= htmlspecialchars($item['time1']) ?>">
                    <span class="x">x</span>
                    <img src="<?=$logo2[0]['logo']?>" alt="<?= htmlspecialchars($item['time2']) ?>">
                </div>

                <div class="info">
                    <strong><?= htmlspecialchars(strtoupper($item['campeonato'])) ?></strong><br>
                    <?= htmlspecialchars($item['local']) ?>, <?= $item['dia'] ?> às <?= $item['hora'] ?>
                </div>

                <a class="btn" href="encontro.php?jogo=<?= htmlspecialchars($item['id']) ?>">MARCAR ENCONTRO</a>
            </div>
        <?php endforeach; ?>

    </div>

</div>

<script>
(function(){
    const meses = [
        "JANEIRO","FEVEREIRO","MARÇO","ABRIL","MAIO","JUNHO",
        "JULHO","AGOSTO","SETEMBRO","OUTUBRO","NOVEMBRO","DEZEMBRO"
    ];
    const selectMes = document.getElementById('select-mes');
    const titulo = document.getElementById('titulo-mes');
    const selectGenero = document.getElementById('select-genero');
    const selectCampeonato = document.getElementById('select-campeonato');
    const lista = document.getElementById('lista-jogos');

    // popular select de meses (valores 1..12)
    meses.forEach((m, i) => {
        const opt = document.createElement('option');
        opt.value = i+1;
        opt.textContent = m;
        selectMes.appendChild(opt);
    });

    // define valor inicial (1 = Janeiro) ou tenta manter mês via querystring
    const urlParams = new URLSearchParams(window.location.search);
    const mesParam = urlParams.get('mes');
    if (mesParam && !isNaN(parseInt(mesParam,10))) {
        selectMes.value = mesParam;
    } else {
        selectMes.value = "1";
    }
    titulo.textContent = meses[parseInt(selectMes.value,10)-1] + "";

    // handler que atualiza o título e aplica o filtro
    function aplicarFiltro() {
        const mesSel = selectMes.value;
        const generoSel = selectGenero.value;
        const campSel = selectCampeonato.value;

        // atualiza título
        const mesIndex = parseInt(mesSel, 10) - 1;
        titulo.textContent = (meses[mesIndex] || '') + " ▼";

        // percorre itens e mostra/oculta
        const itens = lista.querySelectorAll('.item');
        itens.forEach(item => {
            const itemMes = item.getAttribute('data-mes');
            const itemGenero = (item.getAttribute('data-genero') || 'todos').toLowerCase();
            const itemCamp = (item.getAttribute('data-campeonato') || 'todos').toLowerCase();

            const okMes = (mesSel === 'todos') ? true : (itemMes === mesSel);
            const okGenero = (generoSel === 'todos') ? true : (itemGenero === generoSel);
            const okCamp = (campSel === 'todos') ? true : (itemCamp === campSel);

            if (okMes && okGenero && okCamp) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // eventos
    selectMes.addEventListener('change', aplicarFiltro);
    selectGenero.addEventListener('change', aplicarFiltro);
    selectCampeonato.addEventListener('change', aplicarFiltro);

    // aplica filtro inicial
    aplicarFiltro();
})();
</script>
