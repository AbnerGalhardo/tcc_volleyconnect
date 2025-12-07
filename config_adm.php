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
</head>
<body>
    <div class="config-container">
        <h1>CONFIGURAÇÕES</h1>
        
        <form method="post" action="config.php">
            
            <a class="botão" href='notif_adm.php'>
            <div class="config-item">
                <div class="config-content">
                    <span class="config-label">Notificações</span>
                </div>
                <div class="config-action">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div></a>

            <a class="botão" href='cadastro_jogo.php'><div class="config-item">
                <div class="config-content">
                    <span class="config-label">Cadastro de Jogos</span>
                </div>
                <div class="config-action">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div></a>
        
            <a class="botão" href='admin_painel.php'>
            <div class="config-item">
                <div class="config-content">
                    <span class="config-label">Painel Administrador</span>
                </div>
                <div class="config-action">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div></a>

            <a class="botão" href='ajuda_adm.php'> 
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
                    <button class="btn-edit" id="retorno"><a href="tela_principal_adm.php">VOLTAR</a></button>
                    </h2>
        </form>
    </div>
</body>
</html>



            

