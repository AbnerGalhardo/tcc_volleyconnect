<!DOCTYPE html>
<html>
<head>
    <title>listagem</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                    //include 'includes/valida_login.php';
                    // if($_SESSION['login']['usuario']['perfil'] !==adm){
                    //     header('Location: index.php');
                    // }
                ?>
            </div>
        </div>
        <div class="row" style="min-height: 500px;">
            <div class="col-md-12">
            </div>
            <div class="col-md-10" style="padding-top: 50px;">
                <h2>Jogos</h2>
                <?php
                    require_once 'includes/funcoes.php';
                    require_once 'core/conexao.php';
                    require_once 'core/sql.php';
                    require_once 'core/mysql.php';

                    foreach($_GET as $indice => $dado){
                        $$indice = limparDados($dado);
                    }

                    $data_atual = date('Y-m-d H:i:s');

                    $criterio = [];

                    if(!empty($busca)){
                        $criterio[] = ['nome', 'like', "%{$busca}%"];
                    }
                    $result = buscar (
                        'jogo',
                        [
                            'id',
                            'data',
                            '(select nome from campeonato where campeonato.id = jogo.id_campeonato) as campeonato',
                            '(select nome from time where time.id = jogo.id_time1) as time1',
                            '(select nome from time where time.id = jogo.id_time2) as time2',
                        ],
                        // $criterio,
                        // 'data_criacao DESC, nome ASC'
                    );
                    //print_r($result);
                ?>
                <table class="table table-bordered table-hover table-striped
                              table-responsive{-sm|-md|-lg|-xl}">
                    <thead>
                        <tr>
                            <td>Data</td>
                            <td>Cmapeonato</td>
                            <td>Time 1</td>
                            <td>Time 2</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($result as $entidade):
                            $data = date_create($entidade['data']);
                            $data = date_format($data, 'd/m/Y H:i:s');
                        ?>
                        <tr>
                            <td><?php echo $data ?></td>    
                            <td><?php echo $entidade['campeonato'] ?></td>
                            <td><?php echo $entidade['time1'] ?></td>
                            <td><?php echo $entidade['time2'] ?></td>
                            
                        </tr>
                        <?php endforeach; ?> 
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            <h2>
        <button class="btn-edit" id="retorno"><a href="tela_principal.php">VOLTAR</a></button>
      </h2>
                <?php
                    include 'includes/rodape.php';
                ?>
            </div>
        </div>
    </div>
</body>
</html>
