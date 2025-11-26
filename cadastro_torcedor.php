<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Torcedor</title>
    <link rel="stylesheet" href="css/style_torcedor.css">
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

        <h1>Cadastro Torcedor</h1>
        <form action="core/usuario_repositorio.php" method="post">
            <input type="hidden" name="acao" value="<?php echo empty($id) ? 'insert' : 'update' ?>">
            <input type="hidden" name="id" value="<?php echo $entidade['id'] ?? '' ?>">
            <input type="hidden" name="perfil" value="torcedor">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome">
            <br>
            <label for="cpf">CPF</label>
            <input type="tel" name="cpf" id="cpf"> 
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email">
            <br>
            <label for="senha">Senha:</label>      
            <input type="password" name="senha" id="senha"> 
            <br>
        </select>
            <button type="submit">Cadastrar</button>
        </form>
    </div>
</body>
</html>