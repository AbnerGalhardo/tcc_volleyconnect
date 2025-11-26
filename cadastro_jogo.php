<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Jogos</title>
    <link rel="stylesheet" href="css/style_atleta.css">
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">

</head>
<body>
    <div class="container">
        <?php
            require_once 'includes/funcoes.php';
            require_once 'core/conexao.php';
            require_once 'core/sql.php';
            require_once 'core/mysql.php';
        ?>

        <h1>Cadastro Jogos</h1>
        <form action="core/jogo_repositorio.php" method="post">
            <input type="hidden" name="acao" value="<?php echo empty($id) ? 'insert' : 'update' ?>">
            <input type="hidden" name="id" value="<?php echo $entidade['id'] ?? '' ?>">
            <input type="hidden" name="perfil" value="atleta">

            <label for="Campeonato">Campeonato</label>
            <?php
                $result = buscar (
                    'campeonato',
                    [
                        'id',
                        'nome',
                        'genero'
                    ],
                    [],
                    ''
                );
                ?>
                <select name="campeonato" id="campeonato" required>
                <?php
                    foreach($result as $entidade):
                ?>
                <option value=<?php echo "'".$entidade['id']."'" ?> > <?php echo $entidade['nome'] ?> </option>
                <?php endforeach; ?>
               </select>  
            <label for="Time 1">Time 1</label>
            <?php
                $result = buscar (
                    'time',
                    [
                        'id',
                        'nome',
                        'genero'
                    ],
                    [],
                    ''
                );
                ?>
                <select name="time1" id="time1" required>
                <?php
                    foreach($result as $entidade):
                ?>
                <option value=<?php echo "'".$entidade['id']."'" ?> > <?php echo $entidade['nome'] ?> </option>
                <?php endforeach; ?>
               </select>  
            <label for="Time 2">Time 2</label>
            <?php
                $result = buscar (
                    'time',
                    [
                        'id',
                        'nome',
                        'genero'
                    ],
                    [],
                    ''
                );
                ?>
                <select name="time2" id="time2" required>
                <?php
                    foreach($result as $entidade):
                ?>
                <option value=<?php echo "'".$entidade['id']."'" ?> > <?php echo $entidade['nome'] ?> </option>
                <?php endforeach; ?>
               </select>  
            <br>
            <label for="Data">Data e Hora</label>
            <input type="datetime-local" name="data" id="data">
            <br>
            <label for="Local">Local</label>
            <input type="text" name="local" id="local">
            <br>
            <label for="genero">Gênero:</label>
            <select name="genero" id="genero" required>
                <option value="masculino">Masculino</option>
                <option value="feminino">Feminino</option>
            </select>  
            <button type="submit">Cadastrar</button>
        </form>
    </div>
</body>
</html>