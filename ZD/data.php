<?php
include_once("libs/loby_lib.php");
include_once("../libs/db_lib.php");
session_start();
if($_SESSION['logado'] != 'S') {header("location: ../logout.php");exit();}

if($_GET['chat'] || $_GET['users']) {
	$ssid = new SSID();
	$ssid->atualizar();
	if($ssid->desafiado($_SESSION['id']) === 'N') {$ssid->cancelar($_SESSION['id']);}
	elseif($ssid->desafiado($_SESSION['id'])) {echo 'r'; exit();}
}

if($_GET['desafio']) {
	$ssid = new SSID();
  $ssid->atualizar();
	if($ssid->desafiado($_SESSION['id']) === 'X') {echo 'X'; exit();}
	if($ssid->desafiado($_SESSION['id']) == 1 || $ssid->desafiado($_SESSION['id']) == 2) {echo 'P'; exit();}
	if($ssid->desafiado($_SESSION['id']) === 'S') {
	 echo 'S'; 
  	exit();
	}
	if($ssid->desafiado($_SESSION['id']) === 'N') {
	 echo 'N'; 
  	exit();
	}
}
	
if($_GET['chat']) {
	$temp = new Online(); //cololando usuario na lista
	$temp->in($_SESSION['id']);
	unset($temp);
	
$chat = new Chat();
$db = new DB();
$matriz = $chat->ler();
if($matriz[0][0] > 2) {
$var = '<table border="0" width="95%" style="position:absolute; bottom: 0%;">'."\n";
for($x = 2; $x < $matriz[0][0]; $x++) {
  $cor = '';
  $p = '';
  if(gm($db->nome_id($matriz[$x][0]))) {$cor = ' style="color: blue";'; $p = '[GM]';}
  if($db->nome_id($matriz[$x][0]) == 1) {$cor = ' style="color: red";'; $p = '[WM]';}

$var .= '<tr><td><b'.$cor.'>'.$p.$matriz[$x][0].' diz:</b></td></tr>'."\n";
$var .= '<tr><td><p'.$cor.'>'.$matriz[$x][1].'</p></td></tr>'."\n";
}
$var .= '</table>';
}
else {$var = ' ';}
echo $var;
}

if($_GET['users']) {
$temp = new Online();
$temp->in($_SESSION['id']);
$temp->atualizar(); // executar limpeza na lista
$amigos = $temp->onlines();
unset($temp);

session_start();
  if($amigos[0][0] <= 1) {echo '<table border="0" width="100%"><tr><td colspan="2"><b>Nenhum jogador disponível para duelo no momento</b></td></tr></table>';}
  if($amigos[0][0] <= 2 && $amigos[1][0] == $_SESSION['id']) {echo '<table border="0" width="100%"><tr><td colspan="2"><b>Nenhum jogador disponível para duelo no momento</b></td></tr></table>';}

   $x = 1;
   $db_temp = new DB();
  $retorno = '<table class="table table-striped" cellspacing="0" cellpadding="0">'."\n";
   while($x < $amigos[0][0]) {
	   $style = '';
	   $tag = '';
	   if($amigos[$x][0] != $_SESSION['id']) {
			if(gm($amigos[$x][0])) {$tag = '[GM]'; $style = ' style="color: blue;"';}
			if($amigos[$x][0] == 1) {$tag = '[WM]'; $style = ' style="color: red;"';}
			$db_temp->ler($amigos[$x][0]); // ler quem é o usuario
			$retorno .= "<tr><td><a href=\"desafio.php?desafiar=".$db_temp->nome."\"><b".$style.">".$tag.$db_temp->nome."</b></a></td>\n<td align=\"right\"><img src=\"../imgs/online".$amigos[$x][2].".png\" width=\"7%\" /></td></tr>\n";
     }
   $x++;
  }
   echo $retorno."\n</table>";
 }

function gm($id) {
$grav = new Gravacao();
$grav->set_caminho('../gms.txt');
$array = $grav->ler(0);
unset($grav);
$x = 1;
 while($x < $array[0]) {
	if($array[$x] == $id) {return 1;}
  $x++;
 }
return 0;
}
?>