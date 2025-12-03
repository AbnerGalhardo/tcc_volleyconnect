<?php
session_start();

// corrija o path aqui conforme seu projeto (o arquivo que tem a função conecta())
// no seu projeto vimos que a conexão está em core/conexao.php
require_once "core/conexao.php";
require_once "includes/valida_login.php"; // se existir

// Recupera id do usuário logado - tenta a estrutura que você vinha usando
if (isset($_SESSION['login']['usuario']['id'])) {
    $id_torcedor = intval($_SESSION['login']['usuario']['id']);
} elseif (isset($_SESSION['id'])) {
    $id_torcedor = intval($_SESSION['id']);
} else {
    // se não estiver logado, redireciona para login
    header("Location: login.php");
    exit;
}

// recebe dados (podem vir via GET ou POST conforme seu formulário)
// aqui eu assumo que o form envia via GET ou você adaptará conforme necessário
$id_atleta = isset($_GET['atleta']) ? intval($_GET['atleta']) : (isset($_POST['id_atleta']) ? intval($_POST['id_atleta']) : 0);
$id_jogo   = isset($_GET['id_jogo']) ? intval($_GET['id_jogo']) : (isset($_POST['id_jogo']) ? intval($_POST['id_jogo']) : 0);

if ($id_atleta <= 0 || $id_jogo <= 0) {
    // dados inválidos — volta para a lista
    header("Location: encontro.php");
    exit;
}

$con = conecta(); // usa sua função conecta()

// 1) tenta encontrar um registro em atleta_encontro para (id_atleta, id_jogo)
$sqlFind = "SELECT id, horario_inicial FROM atleta_encontro WHERE id_atleta = ? AND id_jogo = ? LIMIT 1";
$stmt = $con->prepare($sqlFind);
$stmt->bind_param("ii", $id_atleta, $id_jogo);
$stmt->execute();
$res = $stmt->get_result();
$ae = $res->fetch_assoc();
$stmt->close();

if (!$ae) {
    // se não existe horário pré-criado, criamos um registro padrão usando a data do jogo
    // buscamos a data do jogo
    $sqlJ = "SELECT data FROM jogo WHERE id = ? LIMIT 1";
    $stj = $con->prepare($sqlJ);
    $stj->bind_param("i", $id_jogo);
    $stj->execute();
    $rj = $stj->get_result();
    $j = $rj->fetch_assoc();
    $stj->close();

    // se jogo existir, usamos sua data; senão, usamos now()
    $horario_inicial = ($j && !empty($j['data'])) ? $j['data'] : date('Y-m-d H:i:s');

    // inserir atleta_encontro com valores padrão (você pode mudar duracao/vagas)
    $sqlInsertAE = "INSERT INTO atleta_encontro (id_atleta, id_jogo, horario_inicial, duracao, vagas) VALUES (?, ?, ?, ?, ?)";
    $stIns = $con->prepare($sqlInsertAE);
    $duracao = 60; // minutos padrão
    $vagas = 10;   // vagas padrão
    $stIns->bind_param("iissi", $id_atleta, $id_jogo, $horario_inicial, $duracao, $vagas);
    $stIns->execute();
    $atleta_encontro_id = $stIns->insert_id;
    $stIns->close();
} else {
    $atleta_encontro_id = intval($ae['id']);
}

// 2) Inserir registro na tabela atleta_encontro_torcedor
$sqlInsertTor = "INSERT INTO atleta_encontro_torcedor (id_atleta_encontro, id_torcedor) VALUES (?, ?)";
$st3 = $con->prepare($sqlInsertTor);
$st3->bind_param("ii", $atleta_encontro_id, $id_torcedor);
$ok = $st3->execute();
$st3->close();

// opcional: você pode verificar $ok e tratar duplicatas (torcedor já inscrito) antes de inserir
// por simplicidade, não tratei duplicatas aqui — posso adicionar verificação se quiser

// fechar conexão (se usa desconecta)
if (function_exists('desconecta')) desconecta($con);

// Se inserção ok, renderiza a tela de sucesso. Caso contrário, mostra erro.
if (!$ok) {
    // tratar erro (p.ex. duplicata)
    $erro = $con->error ?? 'Erro ao agendar encontro.';
    // para debug, pode exibir $erro; em produção prefira mensagem genérica
    die("Erro ao agendar encontro: " . htmlspecialchars($erro));
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Encontro marcado</title>
<link rel="stylesheet" href="css/encontro_sucesso.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
</head>

<body>
<div class="modal">
    <h2>Encontro agendado!</h2>

    <a href="tela_principal.php" class="btn">Voltar</a>
</div>
</body>
</html>
