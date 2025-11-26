<link rel="stylesheet" href="css/cronograma_detalhes.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
<div class="container-cronograma">

    <a href="cronograma.php" class="voltar-topo" aria-label="Voltar">←</a>

    <div class="titulo-area">
       <label class="titulo-area">
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
                <option value="paulista">Paulista</option>
                <option value="nacional">Nacional</option>
                <option value="internacional">Internacional</option>
            </select>
        </label>
    </div>

    <div class="lista-jogos" id="lista-jogos">

        <div class="item" data-mes="1" data-genero="masculino" data-campeonato="regional">
            <div class="left">
                <img src="img/time1.png" alt="Time A">
                <span class="x">x</span>
                <img src="img/time2.png" alt="Time B">
            </div>

            <div class="info">
                <strong>REGIONAL</strong>
                Birigui - SP, 15/01 às 15:00
            </div>

            <a class="btn" href="encontro.php">MARCAR ENCONTRO</a>
        </div>

        <div class="item" data-mes="1" data-genero="masculino" data-campeonato="paulista">
            <div class="left">
                <img src="img/time2.png" alt="Time B">
                <span class="x">x</span>
                <img src="img/time3.png" alt="Time C">
            </div>

            <div class="info">
                <strong>REGIONAL</strong>
                Birigui - SP, 15/01 às 17:00
            </div>

            <a class="btn" href="encontro.php">MARCAR ENCONTRO</a>
        </div>

        <div class="item" data-mes="1" data-genero="feminino" data-campeonato="nacional">
            <div class="left">
                <img src="img/time1.png" alt="Time A">
                <span class="x">x</span>
                <img src="img/time3.png" alt="Time C">
            </div>

            <div class="info">
                <strong>REGIONAL</strong>
                Birigui - SP, 15/01 às 19:00
            </div>

            <a class="btn" href="encontro.php">MARCAR ENCONTRO</a>
        </div>

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

    // define valor inicial (1 = Janeiro)
    selectMes.value = "1";
    titulo.textContent = meses[0] + "";

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
            const itemGenero = item.getAttribute('data-genero') || 'todos';
            const itemCamp = item.getAttribute('data-campeonato') || 'todos';

            const okMes = (mesSel === 'todos') ? true : (itemMes === mesSel);
            const okGenero = (generoSel === 'todos') ? true : (itemGenero === generoSel);
            // campeonato do item pode ter valores variados, permitimos 'todos' e também comparar substrings
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

    // if you want an option "Todos meses", uncomment next lines:
    // const optTodos = document.createElement('option');
    // optTodos.value = 'todos'; optTodos.textContent = 'Todos'; selectMes.insertBefore(optTodos, selectMes.firstChild);

    // aplica filtro inicial
    aplicarFiltro();
})();
</script>

