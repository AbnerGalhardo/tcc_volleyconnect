<?php
session_start();
require_once __DIR__ . '/core/conexao.php';
$con = conecta();

$sessionUserId = $_SESSION['login']['usuario']['id'] ?? null;
$sessionPerfil = $_SESSION['login']['usuario']['perfil'] ?? null;

if (!$sessionUserId || $sessionPerfil !== 'adm') {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Acesso negado. Você não tem permissão.']);
        exit;
    } else {
        header("HTTP/1.1 403 Forbidden");
        echo "<h2>Acesso negado. Você não tem permissão para acessar esta página.</h2>";
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) || isset($_POST['id']))) {
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? '';
    try {
        // Usamos transações para operações que tocam várias tabelas
        if ($action === 'delete_jogo') {
            $id_jogo = intval($_POST['id'] ?? 0);
            if ($id_jogo <= 0) throw new Exception('ID de jogo inválido.');

            $con->begin_transaction();

            // 1) pegar ids de atleta_encontro ligados ao jogo
            $q = $con->prepare("SELECT id FROM atleta_encontro WHERE id_jogo = ?");
            $q->bind_param("i", $id_jogo);
            $q->execute();
            $res = $q->get_result();
            $ae_ids = [];
            while ($r = $res->fetch_assoc()) $ae_ids[] = intval($r['id']);
            $q->close();

            if (!empty($ae_ids)) {
                // 2) deletar atleta_encontro_torcedor referenciando esses encontros
                $in = implode(',', array_fill(0, count($ae_ids), '?'));
                $types = str_repeat('i', count($ae_ids));
                $sql = "DELETE FROM atleta_encontro_torcedor WHERE id_atleta_encontro IN ($in)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param($types, ...$ae_ids);
                $stmt->execute();
                $stmt->close();

                // 3) deletar atleta_encontro
                $sql2 = "DELETE FROM atleta_encontro WHERE id_jogo = ?";
                $st2 = $con->prepare($sql2);
                $st2->bind_param("i", $id_jogo);
                $st2->execute();
                $st2->close();
            }

            // 4) finalmente deletar o jogo
            $del = $con->prepare("DELETE FROM jogo WHERE id = ?");
            $del->bind_param("i", $id_jogo);
            $delOk = $del->execute();
            $del->close();

            if (!$delOk) throw new Exception('Falha ao remover jogo.');

            $con->commit();
            echo json_encode(['ok' => true, 'msg' => 'Jogo removido com sucesso.']);
            exit;
        }

        if ($action === 'delete_user') {
            $id_user = intval($_POST['id'] ?? 0);
            if ($id_user <= 0) throw new Exception('ID de usuário inválido.');

            if ($id_user === intval($sessionUserId)) {
                throw new Exception('Você não pode excluir sua própria conta.');
            }

            $con->begin_transaction();

            // 1) Se usuário é torcedor: remover suas reservas (aet where id_torcedor)
            $st = $con->prepare("DELETE FROM atleta_encontro_torcedor WHERE id_torcedor = ?");
            $st->bind_param("i", $id_user);
            $st->execute();
            $st->close();

            // 2) Se usuário for atleta: precisamos deletar atleta -> seus encontros -> reservas
            // buscar atleta(s) vinculados ao usuário
            $stA = $con->prepare("SELECT id FROM atleta WHERE id_usuario = ?");
            $stA->bind_param("i", $id_user);
            $stA->execute();
            $resA = $stA->get_result();
            $atleta_ids = [];
            while ($r = $resA->fetch_assoc()) $atleta_ids[] = intval($r['id']);
            $stA->close();

            foreach ($atleta_ids as $aid) {
                // pegar encontros do atleta
                $stE = $con->prepare("SELECT id FROM atleta_encontro WHERE id_atleta = ?");
                $stE->bind_param("i", $aid);
                $stE->execute();
                $rE = $stE->get_result();
                $ae_ids = [];
                while ($rr = $rE->fetch_assoc()) $ae_ids[] = intval($rr['id']);
                $stE->close();

                if (!empty($ae_ids)) {
                    // deletar reservas ligadas a esses encontros
                    $in = implode(',', array_fill(0, count($ae_ids), '?'));
                    $types = str_repeat('i', count($ae_ids));
                    $sql = "DELETE FROM atleta_encontro_torcedor WHERE id_atleta_encontro IN ($in)";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param($types, ...$ae_ids);
                    $stmt->execute();
                    $stmt->close();

                    // deletar os encontros
                    $delE = $con->prepare("DELETE FROM atleta_encontro WHERE id_atleta = ?");
                    $delE->bind_param("i", $aid);
                    $delE->execute();
                    $delE->close();
                }

                // deletar atleta (registro)
                $delAt = $con->prepare("DELETE FROM atleta WHERE id = ?");
                $delAt->bind_param("i", $aid);
                $delAt->execute();
                $delAt->close();
            }

            // 3) deletar outras referências como torcedor_atleta_salvo
            $stS = $con->prepare("DELETE FROM torcedor_atleta_salvo WHERE id_torcedor = ? OR id_atleta = ?");
            $stS->bind_param("ii", $id_user, $id_user);
            $stS->execute();
            $stS->close();

            // 4) finalmente deletar o usuário
            $delU = $con->prepare("DELETE FROM usuario WHERE id = ?");
            $delU->bind_param("i", $id_user);
            $delOk = $delU->execute();
            $delU->close();

            if (!$delOk) throw new Exception('Falha ao remover usuário.');

            $con->commit();
            echo json_encode(['ok' => true, 'msg' => 'Usuário removido com sucesso.']);
            exit;
        }

        if ($action === 'toggle_admin') {
            $id_user = intval($_POST['id'] ?? 0);
            if ($id_user <= 0) throw new Exception('ID inválido.');
            if ($id_user === intval($sessionUserId)) throw new Exception('Não é possível alterar seu próprio perfil.');

            // buscar perfil atual
            $st = $con->prepare("SELECT perfil FROM usuario WHERE id = ? LIMIT 1");
            $st->bind_param("i", $id_user);
            $st->execute();
            $res = $st->get_result();
            $row = $res->fetch_assoc();
            $st->close();

            if (!$row) throw new Exception('Usuário não encontrado.');

            $novo = ($row['perfil'] === 'adm') ? 'torcedor' : 'adm';

            $up = $con->prepare("UPDATE usuario SET perfil = ? WHERE id = ?");
            $up->bind_param("si", $novo, $id_user);
            $ok = $up->execute();
            $up->close();

            if (!$ok) throw new Exception('Falha ao atualizar perfil.');

            echo json_encode(['ok' => true, 'msg' => 'Perfil atualizado.', 'novo_perfil' => $novo]);
            exit;
        }

        // ação desconhecida
        throw new Exception('Ação inválida.');

    } catch (Exception $ex) {
        if ($con->errno) $con->rollback();
        echo json_encode(['ok' => false, 'msg' => $ex->getMessage()]);
        exit;
    }
}

// -----------------------
// 3) RENDERIZA A PÁGINA (GET)
// -----------------------

// buscar lista de usuários
$qUsers = $con->query("SELECT id, nome, email, perfil FROM usuario ORDER BY id DESC");
$users = $qUsers ? $qUsers->fetch_all(MYSQLI_ASSOC) : [];

// buscar lista de jogos (com times)
$qJogos = $con->query("
SELECT j.id, j.local, j.data, t1.nome AS time1, t2.nome AS time2
FROM jogo j
LEFT JOIN time t1 ON t1.id = j.id_time1
LEFT JOIN time t2 ON t2.id = j.id_time2
ORDER BY j.data DESC
");
$jogos = $qJogos ? $qJogos->fetch_all(MYSQLI_ASSOC) : [];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8" />
<title>Painel do Administrador</title>
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
<meta name="viewport" content="width=device-width,initial-scale=1" />
<style>
/* Estilos simples e consistentes com o resto do site */
body { margin:0; font-family: Arial, sans-serif; background:#F4F4F8; color:#111; }
.topo { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.06); }
.topo h1 { margin:0; font-size:20px; }
.container { max-width:1100px; margin:24px auto; padding:0 16px; display:grid; grid-template-columns: 1fr 1fr; gap:18px; }
.card { background:#fff; border-radius:10px; padding:14px; box-shadow:0 1px 3px rgba(0,0,0,0.05); }
.table { width:100%; border-collapse:collapse; }
.table th, .table td { padding:10px 8px; border-bottom:1px solid #eee; text-align:left; }
.actions { display:flex; gap:8px; justify-content:flex-end; }
.btn { padding:8px 12px; border-radius:8px; text-decoration:none; color:#fff; background:#8a4fff; border:none; cursor:pointer; }
.btn-danger { background:#d9534f; }
.btn-muted { background:#6c757d; }
.badge { padding:6px 10px; border-radius:8px; color:#fff; font-weight:700; font-size:13px; }
.badge-admin { background:#343a40; }
.badge-user { background:#17a2b8; }
.small { font-size:13px; color:#666; }
.loading { opacity:0.6; pointer-events:none; }
</style>
</head>
<body>

<div class="topo">
    <a href="tela_principal_adm.php" class="back">←</a>
    <h1>Painel do Administrador</h1>
    <div></div>
</div>

<div class="container">
    <div class="card">
        <h3>Usuários</h3>
        <table class="table" id="tbl-users">
            <thead>
                <tr>
                    <th>ID</th><th>Nome</th><th>E-mail</th><th>Perfil</th><th style="width:220px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr data-id="<?= intval($u['id']) ?>">
                        <td><?= intval($u['id']) ?></td>
                        <td><?= htmlspecialchars($u['nome']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <?php if ($u['perfil'] === 'adm'): ?>
                                <span class="badge badge-admin">Administrador</span>
                            <?php else: ?>
                                <span class="badge badge-user"><?= htmlspecialchars(ucfirst($u['perfil'])) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <?php if ($u['id'] != $sessionUserId): ?>
                                <button class="btn btn-toggle" data-id="<?= intval($u['id']) ?>">
                                    <?= $u['perfil'] === 'adm' ? 'Rebaixar' : 'Promover' ?>
                                </button>
                                <button class="btn btn-danger btn-delete-user" data-id="<?= intval($u['id']) ?>">Excluir</button>
                            <?php else: ?>
                                <span class="small">Sua conta</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>Jogos</h3>
        <table class="table" id="tbl-jogos">
            <thead>
                <tr><th>ID</th><th>Times</th><th>Local / Data</th><th style="width:140px">Ações</th></tr>
            </thead>
            <tbody>
                <?php foreach ($jogos as $j): 
                    $dt = $j['data'] ? date("d/m/Y H:i", strtotime($j['data'])) : '-';
                ?>
                    <tr data-id="<?= intval($j['id']) ?>">
                        <td><?= intval($j['id']) ?></td>
                        <td><?= htmlspecialchars($j['time1'] . " x " . $j['time2']) ?></td>
                        <td><?= htmlspecialchars($j['local']) ?> — <?= $dt ?></td>
                        <td class="actions">
                            <button class="btn btn-danger btn-delete-jogo" data-id="<?= intval($j['id']) ?>">Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// helper fetch wrapper
async function postAction(data) {
    const form = new FormData();
    for (const k in data) form.append(k, data[k]);
    const res = await fetch(window.location.pathname, { method: 'POST', body: form, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    return res.json();
}

function setLoading(elem, on=true) {
    if (on) elem.classList.add('loading'); else elem.classList.remove('loading');
}

// Delete user
document.querySelectorAll('.btn-delete-user').forEach(btn=>{
    btn.addEventListener('click', async (e)=>{
        const id = btn.dataset.id;
        if (!confirm('Deseja realmente excluir este usuário? Essa ação é irreversível.')) return;
        setLoading(btn, true);
        const res = await postAction({ action: 'delete_user', id });
        setLoading(btn, false);
        alert(res.msg || 'Resposta não recebida');
        if (res.ok) {
            // remove linha da tabela
            const row = btn.closest('tr');
            row.parentNode.removeChild(row);
        }
    });
});

// Toggle admin/promote/rebaixar
document.querySelectorAll('.btn-toggle').forEach(btn=>{
    btn.addEventListener('click', async (e)=>{
        const id = btn.dataset.id;
        if (!confirm('Confirma alteração de perfil deste usuário?')) return;
        setLoading(btn, true);
        const res = await postAction({ action: 'toggle_admin', id });
        setLoading(btn, false);
        alert(res.msg || 'Resposta não recebida');
        if (res.ok) {
            // atualizar label e botão text
            const row = btn.closest('tr');
            const perfilCell = row.querySelector('td:nth-child(4)');
            if (res.novo_perfil === 'adm') {
                perfilCell.innerHTML = '<span class="badge badge-admin">adm</span>';
                btn.textContent = 'Rebaixar';
            } else {
                perfilCell.innerHTML = '<span class="badge badge-user">Torcedor</span>';
                btn.textContent = 'Promover';
            }
        }
    });
});

// Delete jogo
document.querySelectorAll('.btn-delete-jogo').forEach(btn=>{
    btn.addEventListener('click', async (e)=>{
        const id = btn.dataset.id;
        if (!confirm('Deseja realmente excluir este jogo?')) return;
        setLoading(btn, true);
        const res = await postAction({ action: 'delete_jogo', id });
        setLoading(btn, false);
        alert(res.msg || 'Resposta não recebida');
        if (res.ok) {
            const row = btn.closest('tr');
            row.parentNode.removeChild(row);
        }
    });
});
</script>

</body>
</html>
