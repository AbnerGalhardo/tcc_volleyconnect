<?php
session_start();

// Se o usuário não estiver logado, redireciona
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/core/conexao.php";

$id_usuario = $_SESSION['id_usuario'];

// Busca os encontros marcados pelo usuário
$sql = "SELECT e.id, e.data, e.horario, u.nome AS nome_jogador, t.nome AS nome_time
        FROM encontros e
        JOIN usuario u ON u.id = e.id_jogador
        JOIN time t ON t.id = e.id_time
        WHERE e.id_usuario = $id_usuario
        ORDER BY e.data, e.horario";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Encontros</title>

    <style>
        body {
            background: linear-gradient(135deg, #0F0F3D, #2D2BB7);
            font-family: Arial, sans-serif;
            color: white;
            text-align: center;
            padding: 30px;
        }

        h1 {
            margin-bottom: 30px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            color: black;
            border-radius: 12px;
            padding: 20px;
        }

        .item {
            background: #EEE;
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 8px;
            text-align: left;
        }

        .btn-voltar {
            margin-top: 25px;
            display: inline-block;
            background: #8a4fff;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>Meus Encontros</h1>

    <div class="container">

        <?php if ($result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="item">
                    <strong>Jogador:</strong> <?= $row['nome_jogador'] ?><br>
                    <strong>Time:</strong> <?= $row['nome_time'] ?><br>
                    <strong>Data:</strong> <?= date("d/m/Y", strtotime($row['data'])) ?><br>
                    <strong>Horário:</strong> <?= $row['horario'] ?>
                </div>
            <?php endwhile; ?>

        <?php else: ?>

            <p>Nenhum encontro marcado ainda.</p>

        <?php endif; ?>

        <a href="index.php" class="btn-voltar">Voltar</a>

    </div>

</body>
</html>
