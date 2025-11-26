<?php
session_start();
require_once '../includes/funcoes.php';
require_once 'conexao.php';
require_once 'sql.php';
require_once 'mysql.php';
$salt = '2025';
unset($_SESSION['ERRO_LOGIN']);
foreach ($_POST as $indice => $dado) {
    $$indice = limparDados($dado);
}

foreach ($_GET as $indice => $dado) {
    $$indice = limparDados($dado);
}

switch ($acao) {
    case 'insert':
        $dados = [
            'cpf' => $cpf,
            'nome'  => $nome,            
            'email' => $email,
            'senha' => crypt($senha, $salt),
            'perfil' => $perfil,
        ];

        $id_usuario = insere(
            'usuario',
            $dados
        );

        if($perfil == 'atleta')
        {
            $dados = [
                'id_usuario'  => $id_usuario,            
                'id_time' => $time,
                'posicao' => $posicao,
                'genero' => $genero,
                'idade' => $idade,
            ];
            

            insere(
                'atleta',
                $dados
            );
    
        }

        header('Location: ../login.php');
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

    case 'login':
        
        $criterio = [

            ['email', '=', $email]         
        ];

        $retorno = buscar(
            'usuario',
            ['id', 'nome', 'email', 'senha', 'perfil'],
            $criterio
        );
        //print_r($retorno);
        
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
                    if(($_SESSION['login']['usuario']['perfil']=='adm')) {
                        header('Location: ../tela_principal_adm.php');
                    
                    } elseif  (($_SESSION['login']['usuario']['perfil']=='atleta')) {
                        header('Location: ../tela_principal_atleta.php');
                    }else{
                        header('Location: ../tela_principal.php');
                    }                    
                    exit;
                }
            }
        }
        $_SESSION['ERRO_LOGIN'] = 'E-mail e/ou senha incorreto(s)!';
        header('Location: ../login.php');
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
    