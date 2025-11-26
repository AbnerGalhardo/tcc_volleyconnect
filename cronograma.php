<?php 
?>
<link rel="stylesheet" href="css/cronograma.css">
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">

<div class="topo">
    <a href="tela_principal.php" class="voltar">←</a>
    <h2><img src="img/calendar.png" class="icon">Cronograma</h2>
    <div class="icons-right">
    </div>
</div>

<?php        
     session_start();
     require_once 'includes/funcoes.php';
     require_once 'core/conexao.php';
     require_once 'core/sql.php';
     require_once 'core/mysql.php';
     include 'includes/valida_login.php';
 
     $criterio = [
         ['id_usuario', '=', $_SESSION['login']['usuario']['id']]
     ];
 
 
 
     $atleta = buscar(
         'atleta',
         [
             'id',
             'id_time',
             'posicao',
             'genero',
             '(select nome 
                 from time
                 where time.id = atleta.id_time) as nome_time'
         ],
         $criterio
     );
 ?>

<div class="meses">
    <div class="mes">
        <h1>JANEIRO</h1>
        <div class="jogo">
            <img src="img/time1.png">
            <span class="x">x</span>
            <img src="img/time2.png">
            <span class="data">15/01</span>
        </div>

        <div class="jogo">
            <img src="img/time2.png">
            <span class="x">x</span>
            <img src="img/time3.png">
            <span class="data">15/01</span>
        </div>
    </div>

    <div class="mes">
        <h1>FEVEREIRO</h1>
        <div class="jogo">
            <img src="img/time1.png">
            <span class="x">x</span>
            <img src="img/time3.png">
            <span class="data">10/02</span>
        </div>

        <div class="jogo">
            <img src="img/time1.png">
            <span class="x">x</span>
            <img src="img/time4.png">
            <span class="data">10/02</span>
        </div>
    </div>
</div>

<div class="mais-info">
    <?php if(($_SESSION['login']['usuario']['perfil']=='atleta')): ?>
            <a href="cronograma_detalhes_atleta.php" class="header-item">clique aqui</a> para mais informações</a>
    <?php endif ?>
    <?php if(($_SESSION['login']['usuario']['perfil']=='torcedor')): ?>
        <a href="cronograma_detalhes.php">clique aqui</a> para mais informações
    <?php endif ?>
    
    
</div>

