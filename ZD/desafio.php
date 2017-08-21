<?php
include_once("../libs/tools_lib.php");
include_once("libs/loby_lib.php");
include_once("../libs/db_lib.php");
$tools = new Tools(true);
$tools->verificar();
$tools->verificarlog();

session_start();
$ssid = new SSID();
if($_GET['aceitar'] && !$ssid->desafiado($_SESSION['id'])) {header ("location: loby.php?duelc=1"); exit();}
if($_GET['recusar'] && !$ssid->desafiado($_SESSION['id'])) {header ("location: loby.php?duelc=1"); exit();}
if(!$ssid->desafiado($_SESSION['id']) && !$_GET['desafiar']) {header("location: loby.php?ndes=1"); exit();}
if($ssid->desafiado($_SESSION['id']) === 'N') {header("location: loby.php?desr=1"); exit();}
if($ssid->desafiado($_SESSION['id']) === 'S' || $ssid->desafiado($_SESSION['id']) === 'X') {header("location: start.php"); exit();}

if($_GET['desafiar'] && !$ssid->desafiado($_SESSION['id'])) {desafiar($_GET['desafiar']);}
if($_GET['aceitar'] && $ssid->desafiado($_SESSION['id']) == 2) {
	$ssid->aceitar($_SESSION['id']);
	header ("location: start.php");
	exit(); 
	}
if($_GET['recusar'] && $ssid->desafiado($_SESSION['id']) == 2) {
	$ssid->recusar($_SESSION['id']);
	
 	$temp = new Online();
	$temp->disponivel($_SESSION['id']);
	$temp->disponivel($ssid->adversario($_SESSION['id']));
 	unset($temp);

	header ("location: loby.php?desr=1");
	exit(); 
}
if($_GET['cancelar'] && $ssid->desafiado($_SESSION['id']) == 1) {	
	$ssid->cancelar($_SESSION['id']);
	header ("location: loby.php?duelc=1");
	exit(); 
}

unset($ssid);
$nomes = nomes();
$html = html();
?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Desafio Yu-Gi-Oh Unlimited</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../fonte.css" type="text/css" media="screen"/>
    <link href="../style.css" rel="stylesheet"> <!--Estilos personalizados-->

  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand oficial-font" href="#">Yu-Gi-Oh Unlimited</a>
        </div>
      </div>
    </nav>

<div class="row">

  <div role="main" class="col-md-4 col-md-push-4">
    <table class="table table-striped" cellspacing="0" cellpadding="0">
    	<tr>
    		<td align="center">
    			<b><?php echo $nomes[1];?></b>
    		</td>
    		<td align="center">
    			<b style="color: red">VS</b>
    		</td>
    		<td align="center">
    			<b><?php echo $nomes[2];?></b>
    		</td>
    	</tr>
    </table>

    <?php echo $html;?>
  </div>
    
</div>
    <script src="../bootstrap/jquery-3.1.1.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
<?php echo file_get_contents('../EOP.txt');?>
  </body>
</html>
<?php
function html() {
global $tools;
$ssid = new SSID ();
if($ssid->desafiado($_SESSION['id']) == 2) {
	return '<audio style="display: none;" controls autoplay><source src="efeitos/aviso_desafio.mp3"></audio>
	<script>alert("Desafio recebido!");</script>
<a href="desafio.php?aceitar=1"><img src="../imgs/B_duelar.png" height="'.$tools->rbotao.'" width="100%" /></a>
	 <a href="desafio.php?recusar=1"><img src="../imgs/B_recusar.png" height="'.$tools->rbotao.'" width="100%" /></a>';
	}
	else {
	 	return '<img id="img" src="../imgs/load.gif" width="60%" style = "position: relative; left: 20%;" />
<script type="text/javascript" src="libs/php.js"></script>
<script type="text/javascript">
window.setInterval("mostrar()", 1000);
function mostrar() {
	var temp = file_get_contents("'.$tools->http.$_SERVER['SERVER_NAME'].'/ZD/data.php?desafio=1");
	 var img = document.getElementById("img");
    if(temp == "X") {window.location.href = "'.$tools->http.$_SERVER['SERVER_NAME'].'/ZD/start.php";}
    if(temp == "S") {img.src = "../imgs/S.png"; window.setTimeout("redirecionars()", 2000);}
    if(temp == "N") {img.src = "../imgs/N.png"; window.setTimeout("redirecionarn()", 2000);}
}
function redirecionarn() {window.location.href = "'.$tools->http.$_SERVER['SERVER_NAME'].'/ZD/loby.php?desr=1";}
function redirecionars() {window.location.href = "'.$tools->http.$_SERVER['SERVER_NAME'].'/ZD/start.php";}
</script>
		<img src="../imgs/B_aguardando.png" height="'.$tools->rbotao.'" width="100%" />
	 <a href="desafio.php?cancelar=1"><img src="../imgs/B_cancelar.png" height="'.$tools->rbotao.'" width="100%" /></a>';
	}
}

function nomes() {
$ssid = new SSID();
$db_temp = new DB();
$adv = $ssid->adversario($_SESSION['id']);
if($adv === $_SESSION['id']) exit();

$db_temp->ler($_SESSION['id']);
$retorno[1] = $db_temp->nome;

$db_temp->ler($adv);
$retorno[2] = $db_temp->nome;

unset($db_temp);
unset($ssid);
return $retorno;
}

 function desafiar($nome) {
 $db_temp = new DB();
 if(!$db_temp->user_existe($nome)) {header("location: loby.php?usern=1"); exit();}
 $id = $db_temp->nome_id($nome);
 unset($db_temp);
 $ssid = new SSID();
 if($ssid->desafiado($id)) {header("location: loby.php?usero=1"); exit();}

 $temp = new Online();
 $temp->disponivel($_SESSION['id'], 0);
 $temp->disponivel($id, 0);
 unset($temp);

 $ssid->desafiar($id);
 unset($ssid);
 header("location: desafio.php");
 exit();
 }
?>