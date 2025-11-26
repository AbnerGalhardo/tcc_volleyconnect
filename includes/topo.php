<?php
    session_start();
?>
<div class="card">
    <div class="card-header">
    <?php if(isset($_SESSION['login'])): ?>
    <div class="card-body text-right">
        Olá <?php echo $_SESSION['login']['usuario']['nome'] ?>!
        
        <button class = "btn-edit"><a href="core/usuario_repositorio.php?acao=logout" 
           class="btn btn-link btn-sm" role="button">Sair</a></button> 
    </div>
    <?php endif ?>
</div>
