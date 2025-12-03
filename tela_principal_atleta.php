<?php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolleyConnect - Início</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
</head>
<body>
    <header class="main-header">
        <?php        
            session_start();
            include 'includes/valida_login.php';
        ?>

        <div class="header-content">
            <div class="logo">
                <img src="./img/logo.png" alt="VolleyConnect">
            </div>
            <div class="header-menu">
                <div class="header-option">
                    <a href="config_atleta.php" class="header-item">
                        <img src="./img/config.png"   alt="Ícone Config">
                    </a>
                    <a href="notif.php" class="header-item">
                        <img src="./img/notif.png" alt="Ícone Notificações">
                    </a>
                    <?php if(($_SESSION['login']['usuario']['perfil']=='atleta')): ?>
                        <a href="perfil_atleta.php" class="header-item">
                            <img src="./img/perfil.png" alt="Ícone Perfil">
                        </a>
                    <?php endif ?>
                    <?php if(($_SESSION['login']['usuario']['perfil']=='torcedor')): ?>
                        <a href="perfil.php" class="header-item">
                            <img src="./img/perfil.png" alt="Ícone Perfil">
                        </a>
                    <?php endif ?>

                    <?php if(($_SESSION['login']['usuario']['perfil']=='adm')): ?>
                        <a href="perfil_adm.php" class="header-item">
                            <img src="./img/perfil.png" alt="Ícone Perfil">
                        </a>
                    <?php endif ?>

                    <?php if(isset($_SESSION['login'])): ?>
                    <div class="card-body text-right">Olá <?php echo $_SESSION['login']['usuario']['nome'] ?>!
                        <a href="core/usuario_repositorio.php?acao=logout" class="btn btn-link btn-sm" role="button">Sair</a>
                    </div>
                    <?php endif ?>
            </div>
                
        </div>
        </div>
    </header>

    <section class="call-to-action">
        </section>
    <main class="main-content">
        <section class="call-to-action">
            <h2></h2>
        </section>

        <section class="carousel-section">
            <div class="carousel-container">
                <div class="carousel-slides">
                    <div class="carousel-slide active">
                        <img src="./img/carrosel1.png" alt="Imagem do Carrossel 1">
                    </div>
                    <div class="carousel-slide">
                        <img src="./img/carrosel2.png" alt="Imagem do Carrossel 2">
                    </div>
                    <div class="carousel-slide">
                        <img src="./img/carrosel3.png" alt="Imagem do Carrossel 3">
                    </div>
                </div>
                <div class="carousel-nav left" onclick="previousSlide()">&lt;</div>
                <div class="carousel-nav right" onclick="nextSlide()">&gt;</div>
                <div class="carousel-indicators">
                    <span class="indicator active" onclick="currentSlide(1)"></span>
                    <span class="indicator" onclick="currentSlide(2)"></span>
                    <span class="indicator" onclick="currentSlide(3)"></span>
                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="footer-options">
            <a href="cronograma_atleta.php" class="footer-item">
                <img src="./img/cronograma.png" alt="Ícone Cronograma">
                
            </a>
            <a href="classificacoes.php" class="footer-item">
                <img src="./img/classificacoes.png" alt="Ícone Classificações">
                
            </a>
            <a href="resultados.php" class="footer-item">
                <img src="./img/resultados.png" alt="Ícone Resultados">
                
            </a>
        </div>
    </footer>

    <script>
        let currentSlideIndex = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const indicators = document.querySelectorAll('.indicator');
        const totalSlides = slides.length;

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(indicator => indicator.classList.remove('active'));
            
            slides[index].classList.add('active');
            indicators[index].classList.add('active');
        }

        function nextSlide() {
            currentSlideIndex = (currentSlideIndex + 1) % totalSlides;
            showSlide(currentSlideIndex);
        }

        function previousSlide() {
            currentSlideIndex = (currentSlideIndex - 1 + totalSlides) % totalSlides;
            showSlide(currentSlideIndex);
        }

        function currentSlide(index) {
            currentSlideIndex = index - 1;
            showSlide(currentSlideIndex);
        }

        // Auto-play do carrossel (opcional)
        setInterval(nextSlide, 5000); // Muda slide a cada 5 segundos
    </script>
</body>
</html>


