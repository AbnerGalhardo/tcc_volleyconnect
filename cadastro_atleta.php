<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Atleta</title>
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

        <h1>Cadastro Atleta</h1>
        <form action="core/usuario_repositorio.php" method="post">
            <input type="hidden" name="acao" value="<?php echo empty($id) ? 'insert' : 'update' ?>">
            <input type="hidden" name="id" value="<?php echo $entidade['id'] ?? '' ?>">
            <input type="hidden" name="perfil" value="atleta">

            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome">
            <br>
            <label for="cpf">CPF</label>
            <input type="tel" name="cpf" id="cpf"> 
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required> 
            <br>
            <label for="senha">Senha</label>
            <input type="password" name="senha" id="senha">
            <br>
            <label for="time">Time:</label>
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
                <select name="time" id="time" required>
                <?php
                    foreach($result as $entidade):
                ?>
                <option value=<?php echo "'".$entidade['id']."'" ?> > <?php echo $entidade['nome'] ?> </option>
                <?php endforeach; ?>
               </select>  

            
            <br>   
            <label for="posicao">Posição:</label>
            <select name="posicao" id="posicao" required>
                <option value="levantador">Levantador</option>
                <option value="oposto">Oposto</option>
                <option value="libero">Líbero</option>
                <option value="ponteiro">Ponteiro</option>
                <option value="central">Central</option>
            </select> 
            <br>
            <label for="genero">Gênero:</label>
            <select name="genero" id="genero" required>
                <option value="masculino">Masculino</option>
                <option value="feminino">Feminino</option>
            </select>  
            <br>
            <label for="idade">Idade</label>
            <input type="number" name="idade" id="idade">     
            <br>
        </select>
            <button type="submit">Cadastrar</button>
        </form>
    </div>
    
</body>
</html>