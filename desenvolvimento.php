<?php
include_once("libs/tools_lib.php");
include_once("libs/gravacao_lib.php");
include_once("libs/cards_lib.php");
require_once 'libs/Mobile_Detect.php';

$tools = new Tools();
$tools->verificar();

$vetor = listar();
$N_cartas = $vetor[0]-1;
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Desenvolvimento Yu-Gi-Oh Unlimited</title>
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
            <li><a href="index.php">Principal</a></li>
            <li><a href="tutorial.php">Aprenda a jogar</a></li>
            <li class="active"><a href="desenvolvimento.php">Desenvolvimento</a></li>
            <li><a href="sobre.php">Sobre</a></li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">
    <div role="main" class="col-md-6 col-md-push-3">
          <h1 class="oficial-font">DESENVOLVIMENTO</h1>
          <table class="table table-striped" cellspacing="0" cellpadding="0">
              <tr><td><b>Nova interface do site</b></td><td><b style="color: orange;">pendente</b></td></tr>
              <tr><td><b>Campeonatos</b></td><td><b style="color: orange;">pendente</b></td></tr>
              <tr><td><b>Novo mapa maior</b></td><td><b style="color: orange;">pendente</b></td></tr>
              <tr><td><b>Desafio entre clãs</b></td><td><b style="color: orange;">pendente</b></td></tr>
              <tr><td><b>Dominação mundial</b></td><td><b style="color: orange;">pendente</b></td></tr>
         </table>
         <h1>Cartas na fila: <?php echo $N_cartas;?></h1><hr>
        <?php echo cartas();?>
    </div>
    <div role="left" class="col-md-3 col-md-pull-6">
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

function cartas() {
  $lista = listar();
  $carta = new DB_cards;
  $retorno = '';

  for($x = 1;$x < $lista[0];$x++) {
    $carta->ler($lista[$x]);
    $retorno .= '         <div class="media">
          <div class="media-left">
              <img class="media-object" src="imgs/cards/pequenas/'.$carta->id.'.png">
          </div>
          <div class="media-body">
            <h4 class="media-heading">'.$lista[$x].'</h4>
            '.$carta->descricao.'
          </div>
          </div>'."\n";
  }
  return $retorno;
}

function listar() {
$temp = new Gravacao();
$temp->set_caminho('bd_cards.txt');
$matriz = $temp->ler(1);
unset($temp);
$temp = new DB_cards();
$x = 1;
$array[0] = '.....';
 while($x < $matriz[0][0]) {
    $temp->ler_id($matriz[$x][0]);
    if($temp->id != 13 && !($temp->filtro() || $temp->tipo == 'fusion' || $temp->tipo == 'fusion-effect')) {$array[$x] = $matriz[$x][2];}
    $x++;
 }
sort($array);
$array[0] = count($array);

return $array;
}

?>