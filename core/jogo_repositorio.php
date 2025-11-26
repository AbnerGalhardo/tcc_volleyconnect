<?php
session_start();
require_once '../includes/funcoes.php';
require_once 'conexao.php';
require_once 'sql.php';
require_once 'mysql.php';
$salt = '2025';

foreach ($_POST as $indice => $dado) {
    $$indice = limparDados($dado);
}

foreach ($_GET as $indice => $dado) {
    $$indice = limparDados($dado);
}

switch ($acao) {
    case 'insert':
        $dados = [
            'id' => $id,
            'local'  => $local,            
            'data' => $data,
            'id_campeonato' => $campeonato,
            'id_time1' => $time1,
            'id_time2' => $time2,
        ];

        $id_jogo = insere(
            'jogo',
            $dados
        );

        header('Location: ../listagem_jogos.php');
        break;
        
    case 'update':
        $id = (int)$id;
        $dados = [
            'nome'  => $nome,
            'email' => $email
        ];

        $criterio = [
            ['id', '=', $id]
        ];

        atualiza(
            'usuario',
            $dados,
            $criterio
        );

        break;

        $retorno = buscar(
            'usuario',
            ['id', 'nome', 'email', 'senha', 'perfil'],
            $criterio
        );
        print_r($retorno);
        
        if (count($retorno) > 0) {
            if (crypt($senha, $salt) == $retorno[0]['senha']) {
                
                $_SESSION['login']['usuario'] = $retorno[0];
                
                if (!empty($_SESSION['url_retorno'])) {
                    header('Location: ' . $_SESSION['url_retorno']);
                    $_SESSION['url_retorno'] = '';
                    exit;
                }
                else
                {
                    header('Location: ../tela_principal.php');
                    exit;
                }
            }
        }
       
        break;

    case 'logout':
        session_destroy();
        header('Location: ../index.php');
        break;


    case 'status':
        $id = (int)$id;
        $valor = (int)$valor;
    
        $dados = [
            'ativo' => $valor
        ];
    
        $criterio = [
            ['id', '=', $id]
        ];
    
        atualiza(
            'usuario',
            $dados,
            $criterio
        );
    
        header('Location: ../usuarios.php');
        exit;
        break;
    
    case 'adm':
        $id = (int)$id;
        $valor = (int)$valor;
    
        $dados = [
            'adm' => $valor
        ];
    
        $criterio = [
            ['id', '=', $id]
        ];
    
        atualiza(
            'usuario',
            $dados,
            $criterio
        );
    
        header('Location: ../usuarios.php');
        exit;
        break;
    }
    //header('Location: ../tela_principal.php');
    ?>
    