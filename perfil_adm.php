<?php
session_start();
include 'core/conexao.php';
include 'includes/valida_login.php';
?>



<!DOCTYPE html>
<html lang="pt-br">
<link rel="stylesheet" href="css/style_perfil.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Perfil</title>
    <link rel="stylesheet" href="style_perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="profile-container">
        <h1>SEU PERFIL</h1>

        <div class="profile-section foto-section">
            <div class="profile-icon-placeholder">
                <i class="fas fa-user-circle"></i>
            </div>
        </div>

        <div class="profile-section">
            <div class="info-group">
                <span class="info-label">Nome:</span>
                <span class="info-value"><?php echo htmlspecialchars($_SESSION['login']['usuario']['nome']); ?></span>
            </div>
        </div>

        <div class="profile-section">
            <div class="info-group">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($_SESSION['login']['usuario']['email']); ?></span>
            </div>
        </div>

        <div class="profile-section">
            <div class="info-group">
                <span class="info-label">Senha:</span>
                <span class="info-value">********</span> 
            </div>
        </div>

        <div class="profile-section">
            <div class="info-group">
                <span class="info-label">Credencial:</span>
                <span class="info-value">****</span>
            </div>
        </div>
        <br>
        <h2>
        <button class="btn-edit" id="retorno"><a href="tela_principal.php">VOLTAR</a></button>
      </h2>
    </div>
</body>
</html>

