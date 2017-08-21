<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include_once("libs/Mobile_Detect.php");
$tools = new Tools();
$tools->verificar();
$tools->verificarlog();
$db = new DB();
$db->ler($_SESSION["id"]);
$pagina = 2;
if($_GET["pagina"] != '' && $_GET["pagina"] != 2 ) {$pagina = 1;}
if($_GET["pagina"] == '') {$pagina  = 1;}

$detectar = new Mobile_Detect;
$Nlinha = 3; // numero de cartas por linha
$paginacao; // por padão não páginar
if(!$detectar->isMobile() || $detectar->isTablet()) {
  $Nlinha = 4; // numero de cartas por linha
}
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>DECK Yu-Gi-Oh Unlimited</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fonte.css" type="text/css" media="screen"/>
    <?php 
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
  <h1 style="text-align: center;" class="oficial-font">SEU DECK</h1>
  <?php echo act();?>
   <table class="table table-striped" cellspacing="0" cellpadding="0">
    <?php echo cartas($db->deck, $pagina);?>
    </table>
    <?php
      if($paginacao == 1) { // tem próxima página
        echo '<div class="row"><div class="col-md-2" style="float: right;"><a href="deck.php?pagina=2"><button class="btn btn-primary btn-lg">Próxima</button></a></div></div>';
      } elseif($paginacao == 2) { // tem página anterior
        echo '<div class="row"><div class="col-md-2" style="float: right;"><a href="deck.php"><button style="float: right;" class="btn btn-primary btn-lg">Anterior</button></a></div></div>';
      }
    ?>
  </div>

  <aside role="complementary" class="col-md-3 col-md-push-3">
    <hr><a href="mapa.php"><img src="imgs/explorar_mapa.png" class="img-responsive" /></a><hr>
    <a href="ZD/index.php"><img src="imgs/B_duelos.png" class="img-responsive" /></a><hr>
    <a href="inventario.php"><img src="imgs/B_Minventario.png" class="img-responsive" /></a><hr>
    <a href="iedeck.php"><img src="imgs/iedeck.png" class="img-responsive" /></a><hr>
    <a href="comercio.php"><img src="imgs/B_comercio.png" class="img-responsive" /></a><hr>
    <a href="ranking.php"><img src="imgs/B_ranking.png" class="img-responsive" /></a><hr>
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

<script language="JavaScript">
function abrir(URL) {
 
  var width = 350;
  var height = 500;
 
  var left = 200;
  var top = 100;
 
  window.open(URL,'Info', 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=no, fullscreen=no');
 
}
</script>
    <script src="bootstrap/jquery-3.1.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
<?php echo file_get_contents('EOP.txt');?>
  </body>
</html>
<?php
 function cor($n) {
  if($n < 40 || $n >= 50) {return 'red';}
  else {return 'blue';}
}

 function cartas($deck, $pagina) { // retorna a lista de cartas
  if($deck[0] <= 1) {return '<tr><td align="center" valign="middle"><b>Você ainda não possui cartas no seu deck. Entre no seu <a href="inventario.php">inventário</a> ou no <a href="comercio.php">comércio de cartas</a> para começar a formar seu deck.</b></td></tr>'."\n";}
  if(($deck[0] - 1) <= 25) {$pagina = 1;}
  if(($deck[0] - 1) == 1) {return '<tr><td><a href="javascript:abrir(\'info_card.php?fonte=1&id='.$deck[1].'\')"><img src="imgs/cards/pequenas/'.$deck[1].'.png" class="img-responsive" /></a></td></tr>'."\n";}
  if(($deck[0] - 1) == 2) {return '<tr><td><a href="javascript:abrir(\'info_card.php?fonte=1&id='.$deck[1].'\')"><img src="imgs/cards/pequenas/'.$deck[1].'.png" class="img-responsive" /></a></td> <td><a javascript:abrir(\'href="info_card.php?fonte=1&id='.$deck[2].'\')"><img src="imgs/cards/pequenas/'.$deck[2].'.png" class="img-responsive" /></a></td></tr>'."\n";}
 
  global $Nlinha; // o numero de imagens por linha
  global $paginacao;
  if($pagina == 1) {
   $x = 1;
   $y = 0;
   $retorno = '<tr>';
   while($x <= ($deck[0] - 1) && $x <= 25) {
    if($y == $Nlinha) {$retorno .= '</tr>'."\n".'<tr>'; $y = 0;}
    $retorno .= '<td><a href="javascript:abrir(\'info_card.php?fonte=1&id='.$deck[$x].'\')"><img src="imgs/cards/pequenas/'.$deck[$x].'.png" class="img-responsive" /></a></td>';
    $x++;
    $y++;
   }
    $retorno .= '</tr>'."\n";
    if(($deck[0] - 1) > 25) $paginacao = 1; // tem próxima
   return $retorno;
  }
  else { // se estiver na pagina 2
  if(($deck[0] - 26) == 1) {$paginacao = 2;return '<tr><td><a href="javascript:abrir(\'info_card.php?fonte=1&id='.$deck[26].'\')"><img src="imgs/cards/pequenas/'.$deck[26].'.png" class="img-responsive" /></a></td></tr>'."\n";}
  if(($deck[0] - 26) == 2) {$paginacao = 2;return '<tr><td><a href="javascript:abrir(\'info_card.php?fonte=1&id='.$deck[26].'\')"><img src="imgs/cards/pequenas/'.$deck[26].'.png" class="img-responsive" /></a></td> <td><a href="javascript:abrir(\'info_card.php?fonte=1&id='.$deck[27].'\')"><img src="imgs/cards/pequenas/'.$deck[27].'.png" class="img-responsive" /></a></td></tr>'."\n";}

    $x = 26; // a primeira pagina mostro as primeiras 25 cartas
   $y = 0;
   $retorno = '<tr>';
   while($x <= ($deck[0] - 1)) {
    if($y == $Nlinha) {$retorno .= '</tr>'."\n".'<tr>'; $y = 0;}
    $retorno .= '<td><a href="javascript:abrir(\'info_card.php?fonte=1&id='.$deck[$x].'\')"><img src="imgs/cards/pequenas/'.$deck[$x].'.png" class="img-responsive" /></a></td>';
    $x++;
    $y++;
   }
   $retorno .="</tr>\n";
  $paginacao = 2; // deve ter botão de voltar
   return $retorno;
 }
}

function act() {
if($_GET['att'] == 1) {return "<div class=\"alert alert-success\" role=\"alert\"><strong>Sucesso!</strong> Seu deck foi atualizado</div>";}
if($_GET['movida'] == 1) {return "<div class=\"alert alert-success\" role=\"alert\"><strong>Sucesso!</strong> Carta movida para o inventário</div>";}
if($_GET['incompleto'] == 1) {return "<div class=\"alert alert-danger\" role=\"alert\"><strong>Problema!</strong> Seu deck deve ter no minimo 40 cartas</div>";}
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