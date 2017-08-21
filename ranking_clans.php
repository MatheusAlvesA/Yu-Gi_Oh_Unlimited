<?php
include("libs/tools_lib.php");
include_once 'libs/gravacao_lib.php';
include_once 'libs/db_lib.php';
include("libs/Mobile_Detect.php");
$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$ranking; //declarando aqui apenas para tornar global
if(!file_exists('ranking_clan.txt')) {atualizar();header('location: ranking_clans.php');exit();}
else {
    $grav = new Gravacao();
    $grav->set_caminho('ranking_clan.txt');
    $ranking = $grav->ler(0);
    unset($grav);
    if(time() - $ranking[1] > 1*60*60) {
        atualizar();
        $grav = new Gravacao();
        $grav->set_caminho('ranking_clan.txt');
        $ranking = $grav->ler(0);
        unset($grav);
        header('location: ranking_clans.php');
        exit();
    }
}
 $db = new DB();
 $db->ler($_SESSION['id']);
 $mostrar = lista($ranking);

?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>RANKING de Clãs Yu-Gi-Oh Unlimited</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fonte.css" type="text/css" media="screen"/>
    <?php 
      $detectar = new Mobile_Detect;
      if(!$detectar->isMobile() || $detectar->isTablet()) echo '<link href="style_PC.css" rel="stylesheet"> <!--Estilos exclusivos quando aberto no computador-->';
      else echo '<link href="style.css" rel="stylesheet"> <!--Estilos personalizados-->';
    ?>
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand oficial-font" href="#">Yu-Gi-Oh Unlimited</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="home.php">Principal</a></li>
            <li><a href="perfil.php">Minha conta</a></li>
            <li><a href="tutorial.php">Aprenda a jogar</a></li>
            <li><a href="desenvolvimento.php">Desenvolvimento</a></li>
            <li id="caixa_user_data_nxp">
              <span id="user_data_nxp"><b><?php echo $db->nome;?></b> <?php echo $db->xp;?>XP</span>
              <a href="logout.php" id="BNTdeslogar_ancora"><span id="BNTdeslogar" class="glyphicon glyphicon-off" aria-hidden="true"></span></a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">

  <div role="main" class="col-md-6 col-md-push-3">

    <h1 style="text-align: center;" class="oficial-font">R A N K I N G</h1>
    <hr />

    <div id="list" class="row">
    <div class="table-responsive col-md-12">
      <ul class="nav nav-tabs">
        <li><a href="ranking.php">Duelistas</a></li>
        <li class="active"><a href="#">Clãs</a></li>
      </ul>
        <table class="table table-striped" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Nome</th>
                    <th>Pontos</th>
                 </tr>
            </thead>
            <tbody>
 
                <?php echo $mostrar;?>
 
            </tbody>
         </table>
    </div>
    </div>
</div>

  <aside role="complementary" class="col-md-3 col-md-push-3">
    <hr><a href="mapa.php"><img src="imgs/explorar_mapa.png" class="img-responsive" /></a><hr>
    <a href="ZD/index.php"><img src="imgs/B_duelos.png" class="img-responsive" /></a><hr>
    <a href="deck.php"><img src="imgs/B_Mdeck.png" class="img-responsive" /></a><hr>
    <a href="inventario.php"><img src="imgs/B_Minventario.png" class="img-responsive" /></a><hr>
    <a href="iedeck.php"><img src="imgs/iedeck.png" class="img-responsive" /></a><hr>
    <a href="comercio.php"><img src="imgs/B_comercio.png" class="img-responsive" /></a><hr>
    <a href="meu_clan.php"><img src="imgs/B_clan.png" class="img-responsive" /></a><hr>
    <a href="amigos.php"><img src="imgs/B_amigos.png" class="img-responsive" /></a><hr>
    <a href="mensagens.php"><img src="imgs/B_<?php echo avisar_mensagem();?>mensagens.png" class="img-responsive" /></a><hr>
    <a href="perfil.php"><img src="imgs/B_meu_perfil.png" class="img-responsive" /></a>
  </aside>

    <div role="left" class="col-md-3 col-md-pull-9">
        <?php // esse código insere propaganda na página
            global $G_PROPAGANDA;
            if($G_PROPAGANDA) echo '
        <script type="text/javascript">
          ( function() {
            if (window.CHITIKA === undefined) { window.CHITIKA = { \'units\' : [] }; };
            var unit = {"calltype":"async[2]","publisher":"doutorx","width":300,"height":600,"sid":"Chitika Default"};
            var placement_id = window.CHITIKA.units.length;
            window.CHITIKA.units.push(unit);
            document.write(\'<div id="chitikaAdBlock-\' + placement_id + \'"></div>\');
        }());
        </script>
        <script type="text/javascript" src="//cdn.chitika.net/getads.js" async></script>
                ';
        ?>
    </div>
    
</div>

<hr>
<footer class="row">
  <div class="col-md-4 col-md-push-4">
    <?php include 'rodape.txt';?>
  </div>
</footer>
    <script src="bootstrap/jquery-3.1.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
<?php echo file_get_contents('EOP.txt');?>
  </body>
</html>
<?php

function lista($ranking) {
    $banco = new DB_clan();
 if($ranking[0]-2 == 0) {return '<tr><td colspan="2"><b>Nenhum clã entrou no Ranking ainda</b></td></tr>';}
 for($x = 2;$x < $ranking[0];$x++) {
    $banco->ler($ranking[$x]);
    if($banco->total_pts() != 0) $total_pts = $banco->total_pts();
    else $total_pts = '???';
   $retorno .= '<tr><td align="center"><b>'.($x-1).'°</b></td><td align="right"><a href="info_clan.php?nome='.$ranking[$x].'"><b>'.$ranking[$x].'</b></a></td><td><b>'.$total_pts.'</b></td></tr>'."\n";
 }
 
 return $retorno;
}

function atualizar() {
    $db = new DB_clan();
    $array = $db->ranking();
    $texto = time()."\n";
    $tamanho = count($array);
    for($x = 0; $x < $tamanho; $x++)
     $texto .= $array[$x]."\n";
    file_put_contents('ranking_clan.txt', substr($texto, 0, -1));
}

 function avisar_mensagem() {
 if(!file_exists("msgs/_".$_SESSION["id"].".txt")) {
  $arq = fopen("msgs/_".$_SESSION["id"].".txt", 'w');
  fwrite($arq, 0);
  fclose($arq);
  }
 $arq = fopen("msgs/_".$_SESSION["id"].".txt", 'r');
 $r = fgets($arq);
 fclose($arq);
 if($r == 0) {return '';}
 else {return "T";} //isso fara então receber uma imagem diferente
 }

?>