<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Em Manutenção</title>
<link rel="shortcut icon" href="img/logo.png" type="image/x-icon">

<style>
    body {
        margin: 0;
        padding: 0;
        background: #F4F4F8;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        text-align: center;
    }

    .container {
        background: #fff;
        padding: 40px 35px;
        border-radius: 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        max-width: 500px;
    }

    h1 {
        color: #8a4fff;
        font-size: 28px;
        margin-bottom: 15px;
    }

    p {
        font-size: 16px;
        color: #555;
        margin-bottom: 25px;
        line-height: 1.5;
    }

    .loading {
        width: 50px;
        height: 50px;
        border: 5px solid #ddd;
        border-top-color: #8a4fff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .btn {
        display: inline-block;
        margin-top: 15px;
        background: #8a4fff;
        color: #fff;
        padding: 10px 18px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 15px;
    }

    .btn:hover {
        background: #7639ff;
    }
</style>
</head>

<body>

<div class="container">
    <div class="loading"></div>

    <h1>Estamos em Manutenção</h1>

    <p>
        Esta página está passando por melhorias para oferecer uma experiência melhor.  
        Por favor, volte mais tarde!
    </p>

    <a class="btn" href="tela_principal.php">Voltar ao início</a>
</div>

</body>
</html>
