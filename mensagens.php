<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/msg_lib.php");
include("libs/Mobile_Detect.php");
$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$arq = fopen("msgs/_".$_SESSION["id"].".txt", 'w');
fwrite($arq, 0);
fclose($arq);

$db = new DB();
$db->ler($_SESSION['id']);
$erro = '';
if($_GET['serverl']) {$erro = "<div class=\"alert alert-danger\" role=\"alert\">
      <strong>Problema!</strong> O servidor está cheio
  </div>";}
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Mensagens Yu-Gi-Oh Unlimited</title>
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
    <div id="list" class="row">
    <div class="table-responsive col-md-12">
    <?php if($_GET['apagada'] == 1) {echo "<div class=\"alert alert-success\" role=\"alert\"><strong>Sucesso!</strong> A mensagem foi apagada</div>";}?>
        <table class="table table-striped" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>Mensagem</th>
                    <th>Nome</th>
                 </tr>
            </thead>
            <tbody>
 
                <?php echo mensagens($_SESSION['id']);?>
 
            </tbody>
         </table>
    </div>
    </div>
</div>

  <aside role="complementary" class="col-md-3 col-md-push-3">
  <?php echo $erro;?>
    <hr><a href="mapa.php"><img src="imgs/explorar_mapa.png" class="img-responsive" /></a><hr>
    <a href="ZD/index.php"><img src="imgs/B_duelos.png" class="img-responsive" /></a><hr>
    <a href="deck.php"><img src="imgs/B_Mdeck.png" class="img-responsive" /></a><hr>
    <a href="inventario.php"><img src="imgs/B_Minventario.png" class="img-responsive" /></a><hr>
    <a href="iedeck.php"><img src="imgs/iedeck.png" class="img-responsive" /></a><hr>
    <a href="comercio.php"><img src="imgs/B_comercio.png" class="img-responsive" /></a><hr>
    <a href="ranking.php"><img src="imgs/B_ranking.png" class="img-responsive" /></a><hr>
    <a href="meu_clan.php"><img src="imgs/B_clan.png" class="img-responsive" /></a><hr>
    <a href="amigos.php"><img src="imgs/B_amigos.png" class="img-responsive" /></a><hr>
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
<script type="text/javascript">
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
 function mensagens($id) { // retorna a lista de mensagens
  $msg = new MSG();
  $msgs = $msg->ler($id);
  unset($msg);
  if($msgs[0][0] <= 0) {$retorno = "<tr><td colspan=\"2\"><b>Nenhuma mensagem</b></td></tr>";}
  else {
   $array = previas($msgs);

   $x = 1;
   while($x <= $msgs[0][0]) {
    $array_retorno[$x - 1] = "<tr><td><a href=\"javascript:abrir('lermsg.php?id=".$x."')\">".$array[$x]."</a></td><td>".usuario($msgs[$x][1])."</tr>\n";
    $x++;
   }

  $x = 0;
  while($x < count($array_retorno)) {
   $retorno = $retorno.$array_retorno[$x];
   $x++;
  }
 }
   
  return $retorno;
 }

 function previas($msgs) {
  $retorno[0] = 'x';
  $x = 1;
  while($x <= $msgs[0][0]) {
   $retorno[$x] = htmlentities(
       substr( html_entity_decode($msgs[$x][2]), 0, 20 )."..."
       );
   $x++;
  }
  $retorno[0] = count($retorno) - 1;
  return $retorno;
 }
 
 function usuario($id) {
  $bd = new DB();
  $bd->ler($id);
  return "<a href=\"user.php?nome=".$bd->nome."\"><b>".$bd->nome."</b></a>";
 }
?>