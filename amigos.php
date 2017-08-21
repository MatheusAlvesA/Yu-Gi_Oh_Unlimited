<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/Mobile_Detect.php");

$tools = new Tools();
$tools->verificar();
$tools->verificarlog();
$db = new DB();
$db->ler($_SESSION["id"]);
if(!$tools->verificaridade($_GET["pagina"]) || $db->amigos[0] <= ($_GET["pagina"] * 10) + 1) {$pagina = '';}
else {$pagina  = $_GET["pagina"];}
if($pagina == 0) {$pagina = '';}

$paginacao = 0;

$erro = '';
if($_GET['serverl']) {$erro = "<div class=\"alert alert-danger\" role=\"alert\">
      <strong>Problema!</strong> O servidor está cheio
  </div>";}
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Amigos Yu-Gi-Oh Unlimited</title>
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
    <div id="top" class="row"> 
      <div class="col-md-9 col-md-push-3">
      <form action="buscar_user.php" method="get">
        <div class="input-group h2">
            <input name="nome" class="form-control" id="search" type="text" placeholder="Pesquisar Duelistas">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit">
                    <span class="glyphicon glyphicon-search"></span>
                </button>
            </span>
        </div>
      </form>
      </div>
    </div> <!-- /#topo -->
    <hr />

    <div id="list" class="row">
    <div class="table-responsive col-md-12">
    <?php 
      if($_GET['usernf'] == 1) {
        echo '<div class="alert alert-danger" role="alert"><strong>ERRO!</strong> Usuário não encontrado</div>';
      }
    ?>
        <table class="table table-striped" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="actions"><b style="float: right;">Ações</b></th>
                 </tr>
            </thead>
            <tbody>
 
                <?php echo amigos($db->amigos, $pagina);?>
 
            </tbody>
         </table>
     </div>
      <?php
      if($paginacao == 1) { // tem próxima página
        echo '<div class="row"><div class="col-md-2" style="float: right;"><a href="amigos.php?pagina='.((int)$pagina+1).'"><button class="btn btn-success btn-lg">Próxima</button></a></div></div>';
      } elseif($paginacao == 2) { // tem página anterior
        echo '<div class="row"><div class="col-md-2" style="float: left;"><a href="amigos.php?pagina='.((int)$pagina-1).'"><button class="btn btn-success btn-lg">Anterior</button></a></div></div>';
      } elseif($paginacao == 3) { // tem página anterior e próxima
        echo '<div class="row">
        <div class="col-md-2" style="float: left;"><a href="amigos.php?pagina='.((int)$pagina-1).'"><button class="btn btn-success btn-lg">Anterior</button></a></div>
        <div class="col-md-2" style="float: right;"><a href="amigos.php?pagina='.((int)$pagina+1).'"><button class="btn btn-success btn-lg">Próxima</button></a></div>
        </div>';
      }
    ?>
    </div> <!-- /#lista -->

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
  </body>
<?php echo file_get_contents('EOP.txt');?>
</html>
<?php
 function amigos($amigos, $pagina) { // retorna a lista de amigos
  global $paginacao;

  if($amigos[0] <= 1) {$retorno = "<tr><td colspan=\"2\"><b>Nenhum amigo</b></td></tr>";}
  if($amigos[0] <= 11) {
   $x = 1;
   $db_temp = new DB();
   while($x < $amigos[0]) {
    $db_temp->ler($amigos[$x]); // ler quem e o amigo
    $retorno = $retorno."<tr><td><a href=\"user.php?nome=".$db_temp->nome."\"><b>".$db_temp->nome."</b></a></td>
                        <td class=\"actions\">
                        <a style=\"float: right;\" class=\"btn btn-primary btn-sm\" href=\"javascript:abrir('enviarmsg.php?nome=".$db_temp->nome."')\">MENSAGEM</a>
                    </td>
                    </tr>
    ";
    $x++;
   }
   return $retorno;
  }
  elseif($pagina == '') { // se tiver mais de 10 amigos
   $x = 1;
   $db_temp = new DB();
   while($x < 11) { // loop que gera a lista de amigos
    $db_temp->ler($amigos[$x]); // ler quem e o amigo
    $retorno = $retorno."<tr><td><a href=\"user.php?nome=".$db_temp->nome."\"><b>".$db_temp->nome."</b></a></td>
          <td class=\"actions\">
              <a style=\"float: right;\" class=\"btn btn-primary btn-sm\" href=\"javascript:abrir('enviarmsg.php?nome=".$db_temp->nome."')\">MENSAGEM</a>
          </td>
          </tr>\n";
    $x = $x + 1;
   }
   $x = $pagina + 1;
   $paginacao = 1; // tem outra página
  }
  if($pagina >= 1) {
   $x = ($pagina * 10) + 1;
   $db_temp = new DB();
   for($y = 0;$y < 10;$y++) {
    if($x < $amigos[0]) {
     $db_temp->ler($amigos[$x]); // ler quem e o amigo
     $retorno = $retorno."<tr><td><a href=\"user.php?nome=".$db_temp->nome."\"><b>".$db_temp->nome."</b></a></td>
          <td class=\"actions\">
              <a style=\"float: right;\" class=\"btn btn-primary btn-sm\" href=\"javascript:abrir('enviarmsg.php?nome=".$db_temp->nome."')\">MENSAGEM</a>
          </td></tr>\n";
     $x++;
   }
   }
   if($x < $amigos[0]) {
    $x = $pagina + 1; //debug
    $y = $pagina - 1; //debug
    $paginacao = 3;
  }
   else {$y = $pagina - 1;$paginacao = 2;}
  }
  return $retorno;
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