<?php
include_once("libs/loby_lib.php");
include_once("../libs/tools_lib.php");
include_once("../libs/db_lib.php");
include_once("../libs/Mobile_Detect.php");

$tools = new Tools(true);
$tools->verificar();
$tools->verificarlog();
unset($tools);

$banco_temp = new DB();
$banco_temp->ler($_SESSION['id']);
if($banco_temp->deck[0] < 41) { // caso tente iniciar o duelo sem o numero ideal de cartas
	header("location: index.php?sair=1");
  exit();
}

session_start();
if($_SESSION['id_duelo']) {
	$detectar = new Mobile_Detect;
    if(!$detectar->isMobile() || $detectar->isTablet()) {
		header("location: duelo.php");
	} else header("location: duelo_m.php");
	exit();
}

$temp = new SSID();
if($temp->desafiado($_SESSION['id']) === 'X') {
	$_SESSION['duelando'] = 'S';
	$_SESSION['id_duelo'] = $temp->id_duelo($_SESSION['id']);

	$detectar = new Mobile_Detect;
    if(!$detectar->isMobile() || $detectar->isTablet()) {
		header("location: duelo.php");
	} else {$_SESSION['mobile'] = 'S';header("location: duelo_m.php");}
}
elseif($temp->desafiado($_SESSION['id']) === 'S') {
  $_SESSION['id_duelo']	= $temp->instanciar($_SESSION['id']);
  $_SESSION['duelando'] = 'S';

  	$detectar = new Mobile_Detect;
    if(!$detectar->isMobile() || $detectar->isTablet()) {
		header("location: duelo.php");
	} else {$_SESSION['mobile'] = 'S';header("location: duelo_m.php");}
}
else {
unset($temp);

	$detectar = new Mobile_Detect;
    if(!$detectar->isMobile() || $detectar->isTablet()) {
		header("location: duelo.php");
	} else {$_SESSION['mobile'] = 'S';header("location: duelo_m.php");}
}
?>