<?php
include 'core/conexao.php';

session_start();
include 'includes/valida_login.php';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações</title>
    <link rel="stylesheet" href="css/style_config.css">
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="config-container">
        <h1>CONFIGURAÇÕES</h1>
        
        <form method="post" action="config.php">
            
            <a class="botão" href='notif.php'>
            <div class="config-item">
                <div class="config-content">
                    <span class="config-label">Notificações</span>
                </div>
                <div class="config-action">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div></a>

            <a class="botão" href='meus_encontros.php'>
            <div class="config-item">
                <div class="config-content">
                    <span class="config-label">Meus encontros</span>
                </div>
                <div class="config-action">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div></a>

            <a class="botão" href='ajuda.php'> 
            <div class="config-item">
                <div class="config-content">
                    <span class="config-label">Ajuda e perguntas frequentes</span>
                </div>
                <div class="config-action">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div></a>
            <br>
                    <h2>
                    <button class="btn-edit" id="retorno"><a href="tela_principal.php">VOLTAR</a></button>
                    </h2>
        </form>
    </div>
</body>
</html>



            

