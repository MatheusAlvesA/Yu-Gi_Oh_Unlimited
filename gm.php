<?php
include_once("libs/tools_lib.php");
include_once("libs/gravacao_lib.php");
include_once("config.php");
include_once("libs/db_lib.php");
include_once("libs/Mobile_Detect.php");

$obj = new Tools();
$obj->verificar();
$obj->verificarlog();

$m = ''; // inicializando
if(!gm()) {header("location: home.php"); exit();}
if($_POST['banir'] != '') {executar();exit();}
if($_GET['limparchat'] != '') {limpar_chat();exit();}
if($_GET['desbanir'] != '') {desexecutar();exit();}
if($_GET['bans'] == 1) {$m = '<div class="alert alert-success" role="alert"><strong>Usuário Banido!</strong></div>';}
if($_GET['desbans'] == 1) {$m = '<div class="alert alert-success" role="alert"><strong>Usuário restaurado</strong></div>';}
if($_GET['bann'] == 1) {$m = '<div class="alert alert-danger" role="alert"><strong>Usuário não encontrado!</strong></div>';}
if($_GET['bann'] == 2) {$m = '<div class="alert alert-danger" role="alert"><strong>GMs não podem ser banidos!</strong></div>';}
if($_GET['chats'] == 1) {$m = '<div class="alert alert-success" role="alert"><strong>O chat foi limpo</strong></div>';}
if($_GET['chatn'] == 1) {$m = '<div class="alert alert-danger" role="alert"><strong>Não foi possível limpar!</strong></div>';}


$dberros = 'dberros.txt';
if(!file_exists($dberros)) {fclose(fopen($dberros, 'w'));}
$n = file_get_contents($dberros);
if(!$n) {$n = 0;}
$acessos = 'acessos.txt';
if(!file_exists($acessos)) {fclose(fopen($acessos, 'w'));}
$a = file_get_contents($acessos);
if(!$a) {$a = 0;}

$db = new DB;
$db->ler($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>GM - Yu-Gi-Oh Unlimited</title>
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
      <form action="gm.php" method="post">
      	<h2>Banir duelista</h2>
        <div class="input-group h2">
            <input name="banir" class="form-control" id="search" type="text" placeholder="Nome do Duelista">
            <span class="input-group-btn">
                <button class="btn btn-danger" type="submit">
                    <span class="glyphicon glyphicon-heart"></span>
                </button>
            </span>
        </div>
      </form>
      </div>
    </div> <!-- /#topo -->
    <hr />

        <a class="btn btn-danger btn-lg" style="float: right;" href="gm.php?limparchat=1">LIMPAR O CHAT</a>

        <table class="table table-striped" cellspacing="0" cellpadding="0">
            <tbody>
            	<tr><td><b>Acessos recebidos hoje: </b><?php echo $a;?></td></tr>
            	<tr><td><b>Erros no banco: </b><?php echo $n;?></td></tr>
            </tbody>
         </table>

    <?php echo $m;?>

        <hr>
        <h1>Duelistas banidos</h1>
        <table class="table table-striped" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="actions">Ações</th>
                 </tr>
            </thead>
            <tbody>
 
                <?php echo lista();?>
 
            </tbody>
         </table>
</div>
  <aside role="complementary" class="col-md-3 col-md-push-3">
    <hr><a href="mapa.php"><img src="imgs/explorar_mapa.png" class="img-responsive" /></a><hr>
    <a href="ZD/index.php"><img src="imgs/B_duelos.png" class="img-responsive" /></a><hr>
    <a href="deck.php"><img src="imgs/B_Mdeck.png" class="img-responsive" /></a><hr>
    <a href="inventario.php"><img src="imgs/B_Minventario.png" class="img-responsive" /></a><hr>
    <a href="iedeck.php"><img src="imgs/iedeck.png" class="img-responsive" /></a><hr>
    <a href="comercio.php"><img src="imgs/B_comercio.png" class="img-responsive" /></a><hr>
    <a href="ranking.php"><img src="imgs/B_ranking.png" class="img-responsive" /></a><hr>
    <a href="amigos.php"><img src="imgs/B_amigos.png" class="img-responsive" /></a><hr>
    <a href="mensagens.php"><img src="imgs/B_<?php echo avisar_mensagem();?>mensagens.png" class="img-responsive" /></a><hr>
    <a href="perfil.php"><img src="imgs/B_meu_perfil.png" class="img-responsive" /></a>
  </aside>

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

function gm($id = -1) {
        if($id === -1) $id = $_SESSION['id'];
	$grav = new Gravacao();
	$grav->set_caminho('gms.txt');
	$array = $grav->ler(0);
	unset($grav);
	$x = 1;
	 while($x < $array[0]) {
		if($array[$x] == $id) {return true;}
	  $x++;
	 }
	return false;
}

function executar() {
	$temp = new DB;
	$id = $temp->nome_id($_POST['banir']);
	if($id === false) {header('location: gm.php?bann=1');return false;}
        if(gm($id)) {header('location: gm.php?bann=2');return false;}
        
	$temp->ler($id);
	$temp->banir();
	header('location: gm.php?bans=1');
	return true;
}


function desexecutar() {
	$temp = new DB;
	$id = $temp->nome_id($_GET['desbanir']);
	if($id === false) {header('location: gm.php?bann=1');return false;}
	$temp->ler($id);
	$temp->desbanir();
	header('location: gm.php?desbans=1');
	return true;
}

function limpar_chat() {
  $caminho = 'ZD/chat/';
  $atual = 1;
  while (file_exists($caminho.$atual.'.txt') && $atual <= 7) {
    $atual++;
  }
  if(!file_exists($caminho.'1.txt')) {
    header('location: gm.php?chatn=1');
    return false;
  }
  rename($caminho.($atual-1).'.txt', $caminho.($atual-1).'_'.uniqid().'.txt');
  header('location: gm.php?chats=1');
  return false;
}

function lista() {
	$banco = new DB;
	$lista = $banco->banlist();
	$retorno = '';
	if(count($lista) == 0) return "<tr><td><b>Nenhum duelista banido</b></td></tr>\n";
	for($x = 0; $x < count($lista); $x++) {
		      $retorno .= "<tr>
                    <td>".$lista[$x]['nome']."</td>
                    <td class=\"actions\">
                        <a class=\"btn btn-danger btn-sm\" href=\"gm.php?desbanir=".$lista[$x]['nome']."\">DESBANIR</a>
                    </td>
                </tr>\n";
	}
	return $retorno;
}
?>