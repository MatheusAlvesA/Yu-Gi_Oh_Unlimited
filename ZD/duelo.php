<?php
include_once("../libs/tools_lib.php");
include_once("libs/duelo_lib.php");
include_once("../libs/db_lib.php");
session_start();
$duelo = new Duelo($_SESSION['id_duelo']);
if($_SESSION["ban"] == 'S') {header("location: ../logout.php"); exit();}
if($_SESSION['duelando'] != 'S') {header("location: loby.php"); exit();}

if(!file_exists('duelos/'.$_SESSION['id_duelo'])) {
 $_SESSION['resultado'] = file_get_contents('duelos/'.$_SESSION['id_duelo'].'.txt');
 echo 'END';
 exit();
}

//if($_GET['susp']) {$duelo->suspender_duelo(); exit();}//debug
if($_GET['f']) {finalizar(); exit();}
if($_GET['inbox']) {mostrar_chat(); exit();}
if($_GET['enviar_inbox'] != '') {enviar_msg($_GET['enviar_inbox']); exit();}
if($_GET['dados']) {mostrar_geral(); exit();}
if($_GET['campo_oponente']) {mostrar_campoOponente(); exit();}
if($_GET['info_card']) {mostrar_infoCard($_GET['info_card']); exit();}
if($_GET['acts_card']) {mostrar_actsCard($_GET['acts_card']); exit();}
if($_GET['log']) {ler_log(1); exit();}
if($_GET['logl']) {ler_logl(); exit();}
if($_GET['cmt']) {mostrar_cmt($_GET["cmt"]); exit();}

$tools = new Tools(true);
$tools->verificar();

if($_GET['rcarta'] || $_GET['rcarta'] === '0') {responder_carta($_GET['rcarta']); exit();}
if($duelo->controle_de_turno()) {
    if($_GET['turnooff']) {exit();}
    if($_GET['pfaze']) {proxima_phase(); exit();}
    if($_GET['invocar']) {invocar($_GET['invocar']); exit();}
    if($_GET['campoact']) {campoAct($_GET['campoact']); exit();}
    if($_GET['atacar']) {atacar($_GET['atacar']); exit();}
}

if($_SESSION['mobile'] === 'S') {header("location: duelo_m.php"); exit();}
?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="../imgs/favicon.png">

    <title>Yu-Gi-Oh Unlimited</title>

    <link rel="stylesheet" href="../fonte.css" type="text/css" media="screen"/>
    <style type="text/css">
    	.naoSelecionavel {
    	 -webkit-touch-callout: none;  /* iPhone OS, Safari */
    	 -webkit-user-select: none;    /* Chrome, Safari 3 */
    	 -khtml-user-select: none;     /* Safari 2 */
    	 -moz-user-select: none;       /* Firefox */
    	 -ms-user-select: none;        /* IE10+ */
   		  user-select: none;            /* Possível implementação no futuro */
    /* cursor: default; */
}
    </style>
  </head>

  <body onload="loadcache()">

<div id="loading" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: black; border: 0; z-index: 100; display: block; text-align: center;" onclick="toggleFullScreen();">
	<img src="../imgs/loading.jpg" width="30%" height="80%" style="position: absolute; top: 10%; left: 35%;" />
	<h1 id="progresso_loading" style="position: absolute; top: 92%; left: 49%; color: white; margin: 0 0 0 0;">0/6</h1>
</div>

<audio id="audio_abertura" style="display: none;">
  <source src="efeitos/abertura.mp3">
</audio>
<audio id="audio_contador_durante" style="display: none;" controls loop>
  <source src="efeitos/contador_durante.mp3">
</audio>
<audio id="audio_contador_fim" style="display: none;">
  <source src="efeitos/contador_fim.mp3">
</audio>

  <div id="Infos_Gerais" style="position: absolute; top: 2px; left: 2px; width: 59%; height: 10%; overflow: auto;" class="naoSelecionavel">
  	<div style="position: absolute; top: 0px; left: 0px; width: 20%; height: 100%; overflow: auto;">
  		<h1 id="Tempo" style="margin: 0 0 0 0; position: absolute; top: 0px; left: 5%; font-size: 35pt;">--:--</h1>
  	</div>
  	<div style="position: absolute; top: 0px; left: 25%; width: 70%; height: 100%; overflow: auto; cursor: pointer;">
  		<div id="Status" style="overflow: hidden; width: 100%; height: 100%; position:absolute; top: 0; left: 0px;" onClick="proxima_fase()">
 			<img id="F_2" src = "../imgs/bf2.png" width="15%" height="80%" style="position: absolute; top: 10%; left: 0%; opacity: 0.5" />
 			<img id="F_3" src = "../imgs/bf3.png" width="15%" height="80%" style="position: absolute; top: 10%; left: 20%; opacity: 0.5" />
 			<img id="F_4" src = "../imgs/bf4.png" width="15%" height="80%" style="position: absolute; top: 10%; left: 40%; opacity: 0.5" />
 			<img id="F_5" src = "../imgs/bf5.png" width="15%" height="80%" style="position: absolute; top: 10%; left: 60%; opacity: 0.5" />
 			<img id="F_6" src = "../imgs/bf6.png" width="15%" height="80%" style="position: absolute; top: 10%; left: 80%; opacity: 0.5" />
 		</div>
  	</div>
  </div>

  <div id="Infos_Adversario" style="position: absolute; top: 2px; left: 60%; width: 39%; height: 10%; overflow: auto;">
	<div id="DA_deck" title="Cartas na mão|deck do adversário" style="overflow: hidden; width: 15%; height: 95%; position:absolute; top: 2%; left: 2%; background-image: url(../imgs/BackSideCard.jpg); background-size: 100% 100%;">
		<b id="A_deck" style="font-size: 20pt; position: absolute; top: 20%; left: 20%; z-index: 2; color: white;">00</b>
	</div>
	<div id="DA_cmt" title="Cartas no cemitério do adversário" style="overflow: hidden; width: 15%; height: 95%; position:absolute; top: 2%; left: 19%; background-image: url(../imgs/grav.png); background-size: 100% 100%;" onclick="cemiterio(2)">
		<b id="A_cmt" style="font-size: 20pt; position: absolute; top: 30%; left: 30%; z-index: 2; color: white;">00</b>
	</div>
        <b style="position: absolute; top: 30%; left: 41%; font-size: 18pt;" class="oficial-font"><?php if($_SESSION['logado'] === 'S'){$db = new DB(); $db->ler($duelo->oponente($_SESSION['id'])); echo $db->nome;}else{echo 'oponente';}?></b>
  </div>

  <div id="CAMPOS" style="position: absolute; top: 11%; left: 2px; width: 70%; height: 73%; overflow: auto;">
  	<div id="campo_adv" style="position: absolute; top: 0px; left: 0px; width: 80%; height: 49%; overflow: auto;">
		<div id="A_C_magic" style="overflow: hidden; width: 100%; height: 50%; border: 0; position:absolute; top: 0; left: 0%;">
			<div id="A_E1" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 0%;">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="A_E2" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 20%;">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="A_E3" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 40%;">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="A_E4" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 60%;">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="A_E5" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 80%;">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
		</div>
  		<div id="A_C_monstros" style="overflow: hidden; width: 100%; height: 50%; border: 0; position:absolute; top: 50%; left: 0%;">
			<div id="A_M1" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 0%;">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="A_M2" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 20%;">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="A_M3" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 40%;">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="A_M4" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 60%;">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="A_M5" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 80%;">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
		</div>
	</div>

	<div id="campo_duelista" style="position: absolute; bottom: 0px; left: 0px; width: 80%; height: 49%; overflow: auto;">
		<div id="D_C_monstros" style="overflow: hidden; width: 100%; height: 50%; border: 0; position:absolute; top: 0; left: 0%;">
			<div id="D_M1" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 0%;" onClick="invocar(1)" >
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="D_M2" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 20%;" onClick="invocar(2)">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="D_M3" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 40%;" onClick="invocar(3)">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="D_M4" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 60%;" onClick="invocar(4)">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="D_M5" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 80%;" onClick="invocar(5)">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
		</div>

		<div id="D_C_magic" style="overflow: hidden; width: 100%; height: 50%; border: 0; position:absolute; top: 50%; left: 0%;">
			<div id="D_E1" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 0%;" onClick="invocar(6)">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="D_E2" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 20%;" onClick="invocar(7)">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="D_E3" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 40%;" onClick="invocar(8)">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="D_E4" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 60%;" onClick="invocar(9)">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
			<div id="D_E5" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 80%;" onClick="invocar(10)">
				<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
			</div>
		</div>
	</div>

	<div style="overflow: hidden; width: 19%; height: 100%; position:absolute; top: 0; left: 81%;">
		<div style="overflow: auto; width: 100%; height: 20%; position:absolute; top: 5%; left: 0px;">
			<h1 id="A_lps" style="text-align: center; font-size: 40pt; margin: 0 0 0 0;">8000</h1>
		</div>
		<div id="D_field" style="overflow: hidden; width: 90%; height: 40%; position:absolute; top: 30%; left: 4%;" onClick="invocar(11)">
				<img src = "../imgs/BackSideField.png" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
		</div>
		<div style="overflow: auto; width: 100%; height: 20%; position:absolute; top: 80%; left: 0px;">
			<h1 id="D_lps" style="text-align: center; font-size: 40pt; margin: 0 0 0 0;">8000</h1>
		</div>
	</div>
  </div>

  <div id="info_card" style="position: absolute; top: 12%; left: 73%; width: 25%; height: 71%; overflow: auto;">
  	<div id="info_imagemCarta" style="width: 60%; height: 60%; position: absolute; top: 0; left: 0; border: 0;">
  		<img src="../imgs/ExemploCarta.jpg" width="100%" height="100%" style="position: absolute; top: 0; left: 0;">
  	</div>
	<table id="info_infoMonstro" border="0" width="40%" height="60%" style ="position: absolute; top: 0; left: 60%; display: none; z-index: 11;">
		<tr><td align="center"><b id="info_level"></b></td></tr>
		<tr><td align="center"><b id="info_atk"></b></td></tr>
		<tr><td align="center"><b id="info_def"></b></td></tr>
		<tr><td align="center"><b id="info_atributo"></b></td></tr>
		<tr><td align="center"><b id="info_especie"></b></td></tr>
	</table>
	<div id="info_descricao" style="overflow: auto; position: absolute; top: 60%; left: 0%; width: 100%; height: 30%;"></div>
	<img id="info_usarCarta" src="../imgs/usar_carta.png" width="100%" height="10%" style="position: absolute; top: 90%; left: 0; display: none; cursor: pointer;" onClick="usarCarta()" />
  </div>

  <div id="DivHand" style="position: absolute; bottom: 2px; left: 2px; width: 59%; height: 15%; overflow: auto;">
		<div id="H_C1" style="overflow: hidden; width: 10%; height: 100%; border: 0; position:absolute; top: 0; left: 5%;"></div>
		<div id="H_C2" style="overflow: hidden; width: 10%; height: 100%; border: 0; position:absolute; top: 0; left: 16%;"></div>
		<div id="H_C3" style="overflow: hidden; width: 10%; height: 100%; border: 0; position:absolute; top: 0; left: 27%;"></div>
		<div id="H_C4" style="overflow: hidden; width: 10%; height: 100%; border: 0; position:absolute; top: 0; left: 38%;"></div>
		<div id="H_C5" style="overflow: hidden; width: 10%; height: 100%; border: 0; position:absolute; top: 0; left: 49%;"></div>
		<div id="H_C6" style="overflow: hidden; width: 10%; height: 100%; border: 0; position:absolute; top: 0; left: 60%;"></div>
		<div id="H_C7" style="overflow: hidden; width: 10%; height: 100%; border: 0; position:absolute; top: 0; left: 71%;"></div>
  </div>

  <div id="Infos_Duelista" style="position: absolute; bottom: 2px; left: 60%; width: 39%; height: 15%; overflow: auto;">
  	<div id="DD_deck" style="overflow: hidden; width: 15%; height: 95%; position:absolute; top: 2%; left: 2%; background-image: url(../imgs/BackSideCard.jpg); background-size: 100% 100%;">
		<b id="D_deck" style="font-size: 20pt; position: absolute; top: 30%; left: 30%; z-index: 2; color: white;">00</b>
	</div>
	<div id="DD_cmt" style="overflow: hidden; width: 15%; height: 95%; position:absolute; top: 2%; left: 19%; background-image: url(../imgs/grav.png); background-size: 100% 100%;" onclick="cemiterio(1)">
		<b id="D_cmt" style="font-size: 20pt; position: absolute; top: 30%; left: 30%; z-index: 2; color: white;">00</b>
	</div>
        <b style="position: absolute; top: 35%; left: 41%;  font-size: 18pt;" class="oficial-font"><?php if($_SESSION['logado'] === 'S'){$db = new DB(); $db->ler($_SESSION['id']); echo $db->nome;}else {echo 'duelista';}?></b>
  </div>
      
  <div id="inbox" style="position: absolute; bottom: 0px; left: 80%; width: 20%; height: 25px; overflow: hidden; background-color: white; border-top-left-radius: 10px; border-top-right-radius: 10px;  z-index: 20;">
      <div id="topo_inbox" style="width: 100%; position: absolute; top: 0px; left: 0px; height: 100%; background-color: #00FFFF; text-align: center; vertical-align: middle; border-top-left-radius: 10px; border-top-right-radius: 10px; cursor: pointer; z-index: 3;" onclick="border_chat();">
          <b style="color: white; font-size: 20px;">CHAT</b>
          <div id="aviso_inbox" style="border-radius: 100%; position: absolute; right: 5px; top: 15%; background-color: red; width: 7%; height: 70%; display: none;"></div>
      </div>
      <div id="msgs_inbox" style="width: 100%; text-align: justify; word-wrap: break-word; overflow: scroll; position: absolute; top: 10%; left: 0px; height: 75%; vertical-align: bottom; overflow: auto; z-index: 2;"></div>
      <textarea id="dialog_box" rows="2" style="width: 100%; position: absolute; bottom: 0px; left: 0px;" maxlength="300"></textarea>
  </div>

<!--INICIO DA ZODA DE ELEMENTOS INVISÍVEIS-->

<div id="fundo" style="position: absolute; top: 0; left: 0; opacity: 0.5; width: 100%; height: 100%; background-color: black; border: 0; z-index: 10; display: none;" onClick="removerCamada()"></div>
<div id="duelo_susp" style="position: absolute; top: 0; left: 0; opacity: 0.8; width: 100%; background-color: black; border: 0; z-index: 11; display: none; text-align: center;">
    <h1 style="color: white; background-color: black;">DUELO SUSPENSO! AGUARDANDO AÇÃO DO DUELISTA</h1>
</div>

<div id="invocar_card" style="position: absolute; top: 30%; left: 25%; width: 20%; height: 40%; background-color: white; border: 1px solid green; text-align: center; z-index: 11; display: none;">
	<b>Escolha a posição</b>
	<div style="position: absolute; overflow: hidden; width: 100%; height: 50%; left: 0; top: 25%; border: 0;">
		<img id="2_cima" src="../imgs/ExemploCarta.jpg" width="50%" height="100%" onClick="invocarC = 1; invocar(1);" />
		<img id="2_baixoDef" src="../imgs/backcardDef.png" width="50%" height="50%" style="position: absolute; top: 25%; left: 50%;" onClick="invocarC = 1; invocar(4);" />
		<img id="2_baixo" src="../imgs/backcard.png" width="50%" height="100%" style="position: absolute; top: 0%; left: 50%;" onClick="invocarC = 1; invocar(3);" />
	</div>
</div>

<div id="usarCartaCampo" style="position: absolute; top: 25%; left: 25%; width: 20%; height: 50%; background-color: white; border: 1px solid green; z-index: 11; display: none;">
	<div id="2_Bativar" style="position: absolute; top: 0%; left: 0; width: 100%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(1)">
		<b style="font-family: YuGiOh; position: absolute; top: 15%; left: 30%; font-size: 120%;">Ativar</b>
	</div>
	<div id="2_Batacar" style="position: absolute; top: 17%; left: 0; width: 100%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(2)">
		<b style="font-family: YuGiOh; position: absolute; top: 15%; left: 30%; font-size: 120%;">Atacar</b>
	</div>
	<div id="2_Batk" style="position: absolute; top: 33%; left: 0; width: 100%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(3)">
		<b style="font-family: YuGiOh; position: absolute; top: 15%; left: 5%; font-size: 120%;">modo: ataque</b>
	</div>
	<div id="2_Bdef" style="position: absolute; top: 49%; left: 0; width: 100%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(4)">
		<b style="font-family: YuGiOh; position: absolute; top: 15%; left: 5%; font-size: 120%;">modo: defesa</b>
	</div>
	<div id="2_Bvirar" style="position: absolute; top: 65%; left: 0; width: 100%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(5)">
		<b style="font-family: YuGiOh; position: absolute; top: 15%; left: 30%; font-size: 120%;">flipar</b>
	</div>
	<div id="2_Bsacrificar" style="position: absolute; top: 82%; left: 0; width: 100%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(6)">
		<b style="font-family: YuGiOh; position: absolute; top: 15%; left: 10%; font-size: 120%;">sacrificar</b>
	</div>
</div>

<div id="ListaCartas" style="position: absolute; top: 25%; left: 25%; width: 35%; height: 40%; background-color: white; border: 1px solid green; z-index: 11; display: none;">
	<table border="1" width="100%" height="10%" style="position: absolute; top: 0; left: 0;"><tr><td align="center"><b id="ListaCartastxt"></b></td></tr></table>
	<div id="ListaCartasimgs" style="overflow: scroll; position: absolute; top: 15%; left: 0; width: 100%; height: 90%; background-color: white; border: 0; z-index: 11;"></div>
</div>

<div id="DivLog" style="overflow: scroll; position: absolute; top: 25%; left: 20%; width: 25%; height: 50%; background-color: white; border: 1px solid green; z-index: 11; display: none;"></div>

<!--INICIO DA ZODA DE CÓDIGOS-->

	<script src="../bootstrap/jquery-3.1.1.min.js"></script>
	<script src="libs/playSound.js"></script>
<script type="text/javascript">
var fase = 1;
var dados = null;
var especial = 0;
var invocarC = 0;
var info_card_tipo = 0;
var info_card_local = 0;
var info_card_posicao = 0;
var logs_salvos = '';
var momentSomCarta_sacada = 0;
var Dlps = 8000; // lp do duelista
var Alps = 8000; // lp do adversário

$(function(){adicionarListeners()}); // adicionando os escutadores de eventos

window.setInterval("exibir()", 1000);

function exibir(retornado = null) {
	if(retornado === null) {ler_servidor(); return;}
	else dados = retornado;
	set_hand(dados[0]);
	set_Campo(dados[1], dados[2]);
	ler_campoOponente();
	set_dadosGerais(dados[3], dados[4]);
        if(dados[5] === 'S') {
            $('#duelo_susp').css({"display": "block"});
            $('#Tempo').text(seg_min(0));
        }
	else {
            $('#duelo_susp').css({"display": "none"});
            $('#Tempo').text(seg_min(parseInt(dados[5])));
        }
	set_status(parseInt(dados[6]));
        ler_chat();
}

function set_status(status) {
	if(status == 1) {status = 2;}
	if(fase == 0 && status != 0) {tocar('inicio_turno');}
	fase = status;
	$('#Status > img').css({"opacity": "0.5"});
	if(status < 6) {
		$("#F_"+status).css({"opacity": "1.0"});
	}
	else {
		$("#F_6").css({"opacity": "1.0"});
	}
}

function set_dadosGerais(duelista, adversario) {
	if(duelista[0] == '0' || adversario[0] == '0') {
		$.ajax({type: 'get', data: 'f=1', url:'duelo.php',async: false}).
		done(function() {
        	window.location.href = "/ZD/end.php";
        });
	}

	var fundo = '<img src = "../imgs/BackSideField.png" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />\n';
	//setando elementos do duelista
	alterarLPS(parseInt(duelista[0]), 1); // animação da alteração dos lps do duelista
        
	if(duelista[1] != 0) {
		$('#D_field').html(fundo + '<img src="../imgs/cards/pequenas/' + duelista[1] + '.png" width="75%" height="90%" style="position: absolute; top: 5%; left: 12%;  z-index: 2;" onClick="info_card(1, 11)" />');
	}
        else if(adversario[1] != 0) {
		$('#D_field').html(fundo + '<img src="../imgs/cards/pequenas/' + adversario[1] + '.png" width="75%" height="90%" style="position: absolute; top: 5%; left: 12%;  z-index: 2; border: 2px solid red" onClick="info_card(1, 12)" />');
	}
	else {$('#D_field').html(fundo);}
        
	$('#D_cmt').text(duelista[2]);
	$('#D_deck').text(duelista[3]);
	// setando elementos do adversário
	alterarLPS(parseInt(adversario[0]), 2); // animação da alteração dos lps do adversário

	$('#A_cmt').text(adversario[2]);
	$('#A_deck').text(adversario[3]);
}

function set_Campo(monstros, especiais) {
	var fundo = '<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />\n';
	for(var x = 0; x <= 4; x++) {
		if(monstros[x] != 0) {
			switch (monstros[x].substr(0, 1)) {
			case '1':
			 	$('#D_M'+(x+1)).html(fundo + '<img id="card' + (x+1) + '" src="../imgs/cards/pequenas/' + monstros[x].substr(1) + '.png" width="60%" height="85%" style="position: absolute; top: 7%; left: 20%; z-index: 2;" onClick="info_card(1, ' + (x+1) + ')" ondblclick="info_card(1, ' + (x+1) + ');usarCarta();" />');
			break;
			case '2':
				$('#D_M'+(x+1)).html(fundo + '<img id="card' + (x+1) + '" src="../imgs/cards/pequenas/' + monstros[x].substr(1) + '.png" width="50%" height="100%" style="position: absolute; top: 0%; left: 25%; z-index: 2; -webkit-transform:rotate(90deg); -moz-transform:rotate(90deg); -ms-transform: rotate(90deg); -o-transform: rotate(90deg);" onClick="info_card(1, ' + (x+1) + ')" ondblclick="info_card(1, ' + (x+1) + ');usarCarta();" />');
			break;
			case '3':
				$('#D_M'+(x+1)).html(fundo + '<img id="card' + (x+1) + '" src="../imgs/backcard.png" width="60%" height="85%" style="position: absolute; top: 7%; left: 20%; z-index: 2;" onClick="info_card(1, ' + (x+1) + ')" ondblclick="info_card(1, ' + (x+1) + ');usarCarta();" />');
			break;
			case '4':
				$('#D_M'+(x+1)).html(fundo + '<img id="card' + (x+1) + '" src="../imgs/backcard.png" width="50%" height="100%" style="position: absolute; top: 0%; left: 25%; z-index: 2; -webkit-transform:rotate(90deg); -moz-transform:rotate(90deg); -ms-transform: rotate(90deg); -o-transform: rotate(90deg);" onClick="info_card(1, ' + (x+1) + ')" ondblclick="info_card(1, ' + (x+1) + ');usarCarta();" />');
			break;
			}
		}
		else {
			$('#D_M'+(x+1)).html(fundo);
		}
		if(especiais[x] != 0) {
			switch (especiais[x].substr(0, 1)) {
				case '1':
				$('#D_E'+(x+1)).html(fundo + '<img id="card' + (x+6) + '" src="../imgs/cards/pequenas/' + especiais[x].substr(1) + '.png" width="60%" height="85%" style="position: absolute; top: 7%; left: 20%; z-index: 2;" onClick="info_card(1, ' + (x+6) + ')" ondblclick="info_card(1, ' + (x+6) + ');usarCarta();" />');
				break;
				case '3':
					$('#D_E'+(x+1)).html(fundo + '<img id="card' + (x+6) + '" src="../imgs/backcard.png" width="60%" height="85%" style="position: absolute; top: 7%; left: 20%; z-index: 2;" onClick="info_card(1, ' + (x+6) + ')" ondblclick="info_card(1, ' + (x+6) + ');usarCarta();" />');
				break;
			}
		}
		else {
			$('#D_E'+(x+1)).html(fundo);
		}
	}
}

function set_Campo_oponente(monstros, especiais) {
	var fundo = '<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />\n';
	for(var x = 0; x <= 4; x++) {
		if(monstros[x] != 0) {
			switch (monstros[x].substr(0, 1)) {
			case '1':
			 	$('#A_M'+(x+1)).html(fundo + '<img id="card' + (x+1) + '" src="../imgs/cards/pequenas/' + monstros[x].substr(1) + '.png" width="60%" height="85%" style="position: absolute; top: 7%; left: 20%; z-index: 2; -webkit-transform:rotate(180deg); -moz-transform:rotate(180deg); -ms-transform: rotate(180deg); -o-transform: rotate(180deg);" onClick="info_card(2, ' + (x+1) + ')" />');
			break;
			case '2':
				$('#A_M'+(x+1)).html(fundo + '<img id="card' + (x+1) + '" src="../imgs/cards/pequenas/' + monstros[x].substr(1) + '.png" width="50%" height="100%" style="position: absolute; top: 0%; left: 25%; z-index: 2; -webkit-transform:rotate(270deg); -moz-transform:rotate(270deg); -ms-transform: rotate(270deg); -o-transform: rotate(270deg);" onClick="info_card(2, ' + (x+1) + ')" />');
			break;
			case '3':
				$('#A_M'+(x+1)).html(fundo + '<img id="card' + (x+1) + '" src="../imgs/backcard.png" width="60%" height="85%" style="position: absolute; top: 7%; left: 20%; z-index: 2; -webkit-transform:rotate(180deg); -moz-transform:rotate(180deg); -ms-transform: rotate(180deg); -o-transform: rotate(180deg);" onClick="info_card(2, ' + (x+1) + ')" />');
			break;
			case '4':
				$('#A_M'+(x+1)).html(fundo + '<img id="card' + (x+1) + '" src="../imgs/backcard.png" width="50%" height="100%" style="position: absolute; top: 0%; left: 25%; z-index: 2; -webkit-transform:rotate(270deg); -moz-transform:rotate(270deg); -ms-transform: rotate(270deg); -o-transform: rotate(270deg);" onClick="info_card(2, ' + (x+1) + ')" />');
			break;
			}
		}
		else {
			$('#A_M'+(x+1)).html(fundo);
		}
		if(especiais[x] != 0) {
			switch (especiais[x].substr(0, 1)) {
				case '1':
				$('#A_E'+(x+1)).html(fundo + '<img id="card' + (x+6) + '" src="../imgs/cards/pequenas/' + especiais[x].substr(1) + '.png" width="60%" height="85%" style="position: absolute; top: 7%; left: 20%; z-index: 2; -webkit-transform:rotate(180deg); -moz-transform:rotate(180deg); -ms-transform: rotate(180deg); -o-transform: rotate(180deg);" onClick="info_card(2, ' + (x+6) + ')" />');
				break;
				case '3':
					$('#A_E'+(x+1)).html(fundo + '<img id="card' + (x+6) + '" src="../imgs/backcard.png" width="60%" height="85%" style="position: absolute; top: 7%; left: 20%; z-index: 2; -webkit-transform:rotate(180deg); -moz-transform:rotate(180deg); -ms-transform: rotate(180deg); -o-transform: rotate(180deg);" onClick="info_card(2, ' + (x+6) + ')" />');
				break;
			}
		}
		else {
			$('#A_E'+(x+1)).html(fundo);
		}
	}
}

function set_hand(hand) {
	for(var x = 0; x <= 6; x++) {
		if(hand[x] != 0) {
			$("#H_C" + (x+1)).html('<img src="../imgs/cards/pequenas/' + hand[x] + '.png" width="100%" height="100%" />');
		}
		else {$("#H_C" + (x+1)).html('');}
	}
}

function ler_servidor() {
	var retorno = new Array(7);
 
	retorno[0] = new Array(7);
	retorno[1] = new Array(5);
	retorno[2] = new Array(5);
	retorno[3] = new Array(4);
	retorno[4] = new Array(4);

	var bruto;
	$.ajax({
        type: 'get',
        data: 'dados=1',
        url:'duelo.php',
        success: function(bruto){
          	if(bruto === 'END') {
          		$.ajax({
        			type: 'get',
        			data: 'f=1',
       			 	url:'duelo.php',
        			async: false}).done(function() {
        				window.location.href = '<?php echo $tools->http.$_SERVER['SERVER_NAME'];?>' + "/ZD/end.php";
        			});
			}

			var array = bruto.split("-");
			for(var x = 0; x <= 4; x++) {
				retorno[x] = array[x].split(";");
			}
			retorno[5] = array[5];
			retorno[6] = array[6];

			if(array[7] == 'l') {Log();}
			if(array[7] == 'c' && !telaOcupada) {Slista();}

			exibir(retorno);
		}
    });
}

function ler_campoOponente(asincronico = true) {
	var retorno = new Array(2);
	retorno[0] = new Array(5);
	retorno[1] = new Array(5);

	$.ajax({
        type: 'get',
        data: 'campo_oponente=1',
        url:'duelo.php',
        async: asincronico,
        success: function(bruto){

			var array = bruto.split("-");
			retorno[0] = array[0].split(";");
			retorno[1] = array[1].split(";");

			set_Campo_oponente(retorno[0], retorno[1]);
		}
    });
    return retorno;
}

// variavel local: 0 mão. 1 campo incluindo field. 2 campo adversario. 3 cemitério. 4 cemitério do adversário
function ler_infoCard(local, posicao) {
	local = local.toString();
	posicao = posicao.toString();

	$.ajax({
        type: 'get',
        data: 'info_card='+local+posicao,
        url:'duelo.php',
        success: function(bruto){
          	if(bruto === '0') return;

			var array = new Array(8);
			array =  bruto.split(';');

			info_card(parseInt(local), parseInt(posicao), array);
		}
    });
}

function turno_off() {
	$.ajax({
        type: 'get',
        data: 'turnooff=1',
        url:'duelo.php'
    });
}

function proxima_fase() {
	if(fase === 0) return;
	set_status(fase + 1);
	$.ajax({
        type: 'get',
        data: 'pfaze=1',
        url:'duelo.php'
    });
}

var local_anterior = null;
var posicao_anterior = null;
function info_card(local, posicao, infos = null) {
	if(local === 0 && posicao === 0) {
		local_anterior = null;
		posicao_anterior = null;

		$("#info_imagemCarta").html('<img src="../imgs/ExemploCarta.jpg" width="100%" height="100%" style="position: absolute; top: 0; left: 0;">');
		$("#info_imagemCarta").css({'left': '20%'});
		$("#info_infoMonstro").css({'display': 'none'});
		$("#info_usarCarta").css({'display': 'none'});
		$("#info_descricao").text('');
		return;
	}
	if(local === local_anterior && posicao === posicao_anterior) return false; // é a mesma carta
	if(infos === null) {ler_infoCard(local, posicao); return;}

	local_anterior = local;
	posicao_anterior = posicao;

	if((window.performance.now() - momentSomCarta_sacada) > 200) { // só executa o som cada 200mm pra evitar poluição sonora
		momentSomCarta_sacada = window.performance.now(); // guardando o momento que a reprodução começou
		tocar('sacar_carta'); // efeito sonoro de carta arrastada
	}

	$("#info_imagemCarta").html('<img src="../imgs/cards/' + infos[0] + '.png" width="100%" height="100%" style="position: absolute; top: 0; left: 0;">');
	if(parseInt(infos[1]) !== 0) {
		$("#info_imagemCarta").css({'left': '0%'});
		$("#info_infoMonstro").css({'display': 'block'});
		$("#info_level").text('Level: ' + infos[1]);
		$("#info_atk").text('Ataque: ' + infos[2]);
		$("#info_def").text('Atributo: ' + infos[4]);
		$("#info_atributo").text('Defesa: ' + infos[3]);
		$("#info_especie").text('Espécie: ' + infos[5]);
	}
	else {
		$("#info_imagemCarta").css({'left': '20%'});
		$("#info_infoMonstro").css({'display': 'none'});
	}
	$("#info_descricao").text(infos[6]);
	if(fase == 0 || local == 2) {
		$("#info_descricao").css({'height': '40%'});
		$("#info_usarCarta").css({'display': 'none'});
	}
	else {
		$("#info_descricao").css({'height': '30%'});
		$("#info_usarCarta").css({'display': 'block'});
		info_card_tipo = infos[7];
		info_card_local = local;
		info_card_posicao = posicao;
	}
}

function usarCarta() {
        if(fase == 0) return false;
	$('#fundo').css({'display': 'block'});
	if(info_card_local == 0) {
		$('#invocar_card').css({'display': 'block'});
		$('#2_cima').css({'display': 'block'});
		if(info_card_tipo !== '0') {
			$('#2_baixo').css({'display': 'none'});
			$('#2_baixoDef').css({'display': 'block'});
		}
		else {
			$('#2_baixoDef').css({'display': 'none'});
			$('#2_baixo').css({'display': 'block'});
		}
	}
	if(info_card_local == 1) {
		$('#usarCartaCampo').css({'display': 'block'});
		if(info_card_tipo != 0) {
			var corBorda = "#696969";
			var corFundo = "#DCDCDC";
			var array = new Array(6);
			$.ajax({ // requisição ajax sincrona pois precisa ser
		        type: 'get',
		        data: 'acts_card=' + info_card_posicao,
		        url:'duelo.php',
		        async: false,
		        success: function(bruto){
					array =  bruto.split('-');
				}
		    });

			$('#2_Bativar').css({'display': 'block'});
			if(array[0] == '0') {
				$('#2_Bativar').css({'border': "3px solid "+corBorda, 'background-color': corFundo,  'cursor': 'not-allowed'});
			}
			$('#2_Batacar').css({'display': 'block'});
			if(array[1] == '0') {
				$('#2_Batacar').css({'border': "3px solid "+corBorda, 'background-color': corFundo,  'cursor': 'not-allowed'});
			}
			$('#2_Batk').css({'display': 'block'});
			if(array[2] == '0') {
				$('#2_Batk').css({'border': "3px solid "+corBorda, 'background-color': corFundo,  'cursor': 'not-allowed'});
			}
			$('#2_Bdef').css({'display': 'block'});
			if(array[3] == '0') {
				$('#2_Bdef').css({'border': "3px solid "+corBorda, 'background-color': corFundo,  'cursor': 'not-allowed'});
			}
			$('#2_Bvirar').css({'display': 'block'});
			if(array[4] == '0') {
				$('#2_Bvirar').css({'border': "3px solid "+corBorda, 'background-color': corFundo,  'cursor': 'not-allowed'});
			}
			$('#2_Bsacrificar').css({'display': 'block'});
			if(array[5] == '0') {
				$('#2_Bsacrificar').css({'border': "3px solid "+corBorda, 'background-color': corFundo,  'cursor': 'not-allowed'});
			}
		}
		else {
			$('#2_Bativar').css({'display': 'block'});
			$('#2_Batacar').css({'display': 'none'});
			$('#2_Batk').css({'display': 'none'});
			$('#2_Bdef').css({'display': 'none'});
			$('#2_Bvirar').css({'display': 'none'});
			$('#2_Bsacrificar').css({'display': 'none'});
		}          
	}
}

function removerCamada() {
	$('#DivLog').css({'display': 'none'});
	$('#fundo').css({'display': 'none'});
	$('#invocar_card').css({'display': 'none'});
	$('#usarCartaCampo').css({'display': 'none'});
	$('#ListaCartas').css({'display': 'none'});
	logs_salvos = '';
	telaOcupada = false;

        for(var x = 0; x <= 4; x++) {
            $('#D_M' + (x+1)).css({'zIndex': '1'});
            $('#D_E' + (x+1)).css({'zIndex': '1'});
	}
	especial = 0;
	invocarC = 0;
        
	// agora fazendo as cores voltarem ao normal
	var corBordaNormal = "#4B0082";
	var corFundoNormal = "#E6E6FA";
	$('#2_Bativar').css({'border': "3px solid "+corBordaNormal, 'background-color': corFundoNormal,  'cursor': 'pointer'});
	$('#2_Batacar').css({'border': "3px solid "+corBordaNormal, 'background-color': corFundoNormal,  'cursor': 'pointer'});
	$('#2_Batk').css({'border': "3px solid "+corBordaNormal, 'background-color': corFundoNormal,  'cursor': 'pointer'});
	$('#2_Batk').css({'border': "3px solid "+corBordaNormal, 'background-color': corFundoNormal,  'cursor': 'pointer'});
	$('#2_Bdef').css({'border': "3px solid "+corBordaNormal, 'background-color': corFundoNormal,  'cursor': 'pointer'});
	$('#2_Bvirar').css({'border': "3px solid "+corBordaNormal, 'background-color': corFundoNormal,  'cursor': 'pointer'});
	$('#2_Bsacrificar').css({'border': "3px solid "+corBordaNormal, 'background-color': corFundoNormal,  'cursor': 'pointer'});

	return true;
}

function campoAct(act) {
	especial = 0;
	removerCamada();
	if(act == 2) {
		var cards = ler_campoOponente(false); //Não executar assincronicamente
		var quantas = 0;
		for(var x = 0; x < 5; x++) {
			if(cards[0][x] != 0) {quantas++;}
		}
		var alvos = new Array(quantas + 1);
		var links = new Array(quantas + 1);
		var y = 1;
		alvos[0] = 'atkd';
		links[0] = "auxiliarCampoact("+ info_card_posicao + ",'d');";
		for(var x = 0; x < 5; x++) {
			if(cards[0][x] != 0) {
				if(cards[0][x].substr(0, 1) == '4') {alvos[y] = 'db';}
				else if(cards[0][x].substr(0, 1) == 2) {alvos[y] = 'd' + cards[0][x].substr(1);}
				else {alvos[y] = cards[0][x].substr(1);}
				links[y] = "auxiliarCampoact("+ info_card_posicao + ',' + (x + 1) +");";
				y++;
			}
		}
		lista_cartas('Selecionar alvo', alvos, links);
		return 0;
	}
		$.ajax({
        	type: 'get',
        	data: 'campoact=' + info_card_posicao + ',' + act,
        	url:'duelo.php'
    	});
}
function auxiliarCampoact(info_card_posicao, x) {
	$.ajax({type: 'get', data: 'atacar='+info_card_posicao+','+x, url:'duelo.php'});
	removerCamada();
}

var telaOcupada = false;
function Log() {
	var log = '<b>ERRO NA LEITURA DO LOG</b>';
	$.ajax({ // requisição ajax sincrona pois precisa ser
		type: 'get',
		data: 'log=1',
		url:'duelo.php',
		async: false,
		success: function(bruto){
			log = bruto;
		}
	});
	if(log == 0) {return 0;}
	$('#DivLog').css({'display': 'block'});
	logs_salvos += log;
	$('#DivLog').html('<h1 class="oficial-font" style="position: absolute; bottom: 0; right: 0; color: red; margin: 0 0 0 0; padding: 0 0 0 0; font-size: 30px; z-index: 10; cursor: pointer;" onclick="removerCamada()">X</h1>'+logs_salvos);
	telaOcupada = true;
}

function invocar(onde) {
	if(invocarC == 0) {return;}
	if(especial == 0) {
                var backup = invocarC;
                removerCamada();
                invocarC = backup;
		especial = onde;
		$('#fundo').css({'display': 'block'});
		if(info_card_tipo != 0) {
			for(var x = 0; x <= 4; x++) {
				if(dados[1][x] == 0) {$('#D_M' + (x+1)).css({'zIndex': '11'});}
			}
		}
		else {
			for(var x = 0; x <= 4; x++) {
				if(dados[2][x] == 0) {$('#D_E' + (x+1)).css({'zIndex': '11'});}
			}
			$('#D_field').css({'zIndex': '11'});
		}
	}
	else {
	$.ajax({
        	type: 'get',
        	data: 'invocar=' + info_card_posicao + ',' + especial + ',' + onde,
        	url:'duelo.php'
    	});
    	$('#fundo').css({'display': 'none'});
		for(var x = 0; x <= 4; x++) {
			$('#D_M' + (x+1)).css({'zIndex': '1'});
			$('#D_E' + (x+1)).css({'zIndex': '1'});
		}
		especial = 0;
		invocarC = 0;
		info_card(0,0); // limpando
	}
}

function Slista(retornado = null) {
	if(document.getElementById("fundo").style.display === 'block') {return false;}
	$.ajax({
		type: 'get',
		data: 'logl=1',
		url:'duelo.php',
		success: function(bruto){
			Slista(bruto);
		}
	});
	if(retornado === null) return false;
	var array = retornado.split('-');
	var txt = array[0];
	var imgs = array[1].split(';');
	var links = array[2].split(';');
	for(var y = 0; y < links.length; y++) {links[y] = 'responder_carta('+links[y]+')';}
	lista_cartas(txt, imgs, links);
}

function cemiterio(qual) {
	var bruto = 0;
	$.ajax({ // requisição ajax sincrona pois precisa ser
		type: 'get',
		data: 'cmt=' + qual,
		url:'duelo.php',
		async: false,
		success: function(r){
			bruto = r;
		}
	});
	if(bruto == 0) {return 0;}
	var lista = bruto.split(";");
	var lk = new Array(1);
	if(qual == 1) {var txt = 'Seu cemitério';}
	else {var txt = 'Cemitério do oponente';}
	lista_cartas(txt, lista, lk);
}

function responder_carta(x) {
	$.ajax({
		type: 'get',
		data: 'rcarta='+x,
		url:'duelo.php',
		success: function(r){
			removerCamada();
		}
	});
}

function lista_cartas(txt, imgs, links) {
	var html = '';
	var espaco = 0;
	for(var x = 0; x < imgs.length; x++) {
		if(imgs[x] == 'atkd') {
			html += '<img src="../imgs/atkd.png" width="30%" height="100%" style="position: absolute; top: 0; left: ' + espaco + '%;" onClick="'+links[x]+'" /> ';
		}
		else if(imgs[x] == 'db') {
			html += '<img src="../imgs/backcardDef.png" width="30%" height="40%" style="position: absolute; top: 30%; left: ' + espaco + '%;" onClick="'+links[x]+'" /> ';
		}
		else if(imgs[x] == 'ub') {
			html += '<img src="../imgs/backcard.png" width="30%" height="100%" style="position: absolute; top: 0; left: ' + espaco + '%;" onClick="'+links[x]+'" /> ';
		}
		else if(imgs[x] == 'divisor') {
			html += '<img src="../imgs/divisor.jpg" width="1%" height="100%" style="position: absolute; top: 0; left: ' + (espaco+14) + '%;" /> ';
		}
		else if(imgs[x] == 'up') {
			html += '<img src="../imgs/up.png" width="30%" height="70%" style="position: absolute; top: 30%; left: ' + espaco + '%;" onClick="'+links[x]+'" /> ';
		}
		else if(imgs[x] == 'down') {
			html += '<img src="../imgs/down.png" width="30%" height="70%" style="position: absolute; top: 30%; left: ' + espaco + '%;" onClick="'+links[x]+'" /> ';
		}
		else if(imgs[x].substr(0, 1) == 'd') {
			html += '<div style="width: 30%; height: 100%; overflow: hidden; position: absolute; top: 0;  left: ' + espaco + '%;"><img src="../imgs/cards/pequenas/' + imgs[x].substr(1) + '.png" width="70%" height="60%" style="position: absolute; left: 15%; top: 20%; margin: 0; padding: 0; -webkit-transform:rotate(90deg); -moz-transform:rotate(90deg);" onClick="'+links[x]+'" /></div> ';
		}
		else {
			html += '<img src="../imgs/cards/pequenas/' + imgs[x] + '.png" width="30%" height="100%" style="position: absolute; top: 0; left: ' + espaco + '%;" onClick="'+links[x]+'" /> ';
		}
		espaco += 30;
	}
	$('#fundo').css({'display': 'block'});
	$('#ListaCartas').css({'display': 'block'});
	$('#ListaCartastxt').text(txt);
	$('#ListaCartasimgs').html(html);
}

function seg_min(segundos) {
	var minutos = 0;
	while(segundos >= 60) {
		minutos++;
		segundos = segundos - 60;
	}
	segundos = segundos.toString();
	if(segundos.length < 2) {segundos = '0' + segundos;}
	return minutos + ":" + segundos;
}

function tocar(som) { // toca o som passado por parametro
	var url = "/ZD/efeitos/" + som;
	$.playSound(url);
}

function adicionarListeners() {
	// passando o mouse nas cartas da mão
    $("#H_C1").mouseover(function(){
        if($(this).attr('html') !== '') info_card(0, 1);
    });
    $("#H_C1").dblclick(function(){
        if($(this).attr('html') !== '') {info_card(0, 1);usarCarta();}
    });

    $("#H_C2").mouseover(function(){
        if($(this).attr('html') !== '') info_card(0, 2);
    });
    $("#H_C2").dblclick(function(){
        if($(this).attr('html') !== '') {info_card(0, 2);usarCarta();}
    });

    $("#H_C3").mouseover(function(){
        if($(this).attr('html') !== '') info_card(0, 3);
    });
    $("#H_C3").dblclick(function(){
        if($(this).attr('html') !== '') {info_card(0, 3);usarCarta();}
    });

    $("#H_C4").mouseover(function(){
        if($(this).attr('html') !== '') info_card(0, 4);
    });
    $("#H_C4").dblclick(function(){
        if($(this).attr('html') !== '') {info_card(0, 4);usarCarta();}
    });

    $("#H_C5").mouseover(function(){
        if($(this).attr('html') !== '') info_card(0, 5);
    });
    $("#H_C5").dblclick(function(){
        if($(this).attr('html') !== '') {info_card(0, 5);usarCarta();}
    });

    $("#H_C6").mouseover(function(){
        if($(this).attr('html') !== '') info_card(0, 6);
    });
    $("#H_C6").dblclick(function(){
        if($(this).attr('html') !== '') {info_card(0, 6);usarCarta();}
    });

    $("#H_C7").mouseover(function(){
        if($(this).attr('html') !== '') info_card(0, 7);
    });
    $("#H_C7").dblclick(function(){
        if($(this).attr('html') !== '') {info_card(0, 7);usarCarta();}
    });
    $('#dialog_box').keypress(function (e){
        if(e.keyCode == 13){
            e.preventDefault();
            $.ajax({
                    type: 'get',
                    data: 'enviar_inbox='+$('#dialog_box').val(),
                    url:'duelo.php',
            });
            $('#dialog_box').val('');
        }
    });
}

var trava = false;
function alterarLPS(quanto, quem) { //quem: 1 = duelista; 2 = adversário
	if(!trava) trava = true; // se não estiver travado agora está
	else return;
	if(quem === 1) {
		var anterior = Dlps;
		var elemento = $('#D_lps')
	}
	else  {
		var anterior = Alps;
		var elemento = $('#A_lps')
	}
	if(anterior == quanto) {
		trava = false; //destravando
		return;
	}

	var distancia = anterior-quanto;
	if(distancia > 0) { // é pra diminuir
		if(distancia < 100) passo = 2;
		else {
			var	passos = 3000/50;
			var passo = parseInt(distancia/passos)+2;
		}
		document.getElementById('audio_contador_durante').play();
		elemento.css({'color': 'red'});
		var intervalo = window.setInterval(
			function(){
				if(anterior < quanto+passo) {
					clearInterval(intervalo);
					document.getElementById('audio_contador_durante').pause();
					document.getElementById('audio_contador_durante').currentTime = 0;
					document.getElementById('audio_contador_fim').play();
					elemento.text(quanto);
					elemento.css({'color': 'black'});
					if(quem === 1) {
						Dlps = quanto;
					}
					else  {
						Alps = quanto;
					}
					trava = false; //destravando
				} else {
					anterior -= passo;
					elemento.text(anterior);
				}
			}
		,50);
	}
	else { // é pra aumentar
		if(distancia > -100) passo = (-2);
		else {
			var	passos = 3000/50;
			var passo = parseInt(distancia/passos)-2;
		}
		document.getElementById('audio_contador_durante').play();
		elemento.css({'color': 'blue'});
		var intervalo = window.setInterval(
			function(){
				if(anterior > quanto+passo) {
					clearInterval(intervalo);
					document.getElementById('audio_contador_durante').pause();
					document.getElementById('audio_contador_durante').currentTime = 0;
					document.getElementById('audio_contador_fim').play();
					elemento.text(quanto);
					elemento.css({'color': 'black'});
					if(quem === 1) {
						Dlps = quanto;
					}
					else  {
						Alps = quanto;
					}
					trava = false; //destravando
				} else {
					anterior -= passo;
					elemento.text(anterior);
				}
			}
		,50);
	}
}

var trava_musica = true;
var trava_card1 = true;
var trava_card2 = true;
var trava_card3 = true;
var trava_card4 = true;
var trava_card5 = true;
var load_requisitado = false;
var listenners_nao_criados = true;
var progresso = 0;
var load_i = window.setInterval("loadcache()", 500);
function loadcache() {
	if(load_requisitado) {
		if(!trava_musica && !trava_card1 && !trava_card2 && !trava_card3 && !trava_card4 && !trava_card5) {
			$('#progresso_loading').text('6/6');
                        clearInterval(load_i);
			abertura();
		}
		else { // toda vez que entra aqui carregou mais alguma coisa, mas não terminou
			progresso = 0;
			if(!trava_musica) progresso++;
			if(!trava_card1) progresso++;
			if(!trava_card2) progresso++;
			if(!trava_card3) progresso++;
			if(!trava_card4) progresso++;
			if(!trava_card5) progresso++;
			$('#progresso_loading').text(progresso+'/6');
		}
                return;
	} else load_requisitado = true;
        
    var audio1 = new Audio();
    audio1.src = '/ZD/efeitos/abertura.mp3';
	audio1.load();

	var audio2 = new Audio();
	audio2.src = '/ZD/efeitos/sacar_carta.mp3';
	audio2.load();

	var audio3 = new Audio();
	audio3.src = '/ZD/efeitos/contador_durante.mp3';
	audio3.load();
	
	audio1.addEventListener('canplaythrough', function(){
         trava_musica = false;
         loadcache();
    });

	exibir(); // lendo o servidor pela primeira vez
	var i = setInterval(function(){
		if(dados !== null && listenners_nao_criados) {
			if(dados[0][0] != 0) {
				var imagem1 = new Image();
				imagem1.src = '/imgs/cards/' + dados[0][0] + '.png';
				imagem1.addEventListener('load', function(){
		         	trava_card1 = false;
		         	loadcache();
		    	});
			} else trava_card1 = false;
			if(dados[0][1] != 0) {
				var imagem2 = new Image();
				imagem2.src = '/imgs/cards/' + dados[0][1] + '.png';
				imagem2.addEventListener('load', function(){
		         	trava_card2 = false;
		         	loadcache();
		    	});
			} else trava_card2 = false;
			if(dados[0][2] != 0) {
				var imagem3 = new Image();
				imagem3.src = '/imgs/cards/' + dados[0][2] + '.png';
				imagem3.addEventListener('load', function(){
		         		trava_card3 = false;
		         		loadcache();
		    	});
			} else trava_card3 = false;
			if(dados[0][3] != 0) {
				var imagem4 = new Image();
				imagem4.src = '/imgs/cards/' + dados[0][3] + '.png';
				imagem4.addEventListener('load', function(){
		         		trava_card4 = false;
		         		loadcache();
		    	});
			} else trava_card4 = false;
			if(dados[0][4] != 0) {
				var imagem5 = new Image();
				imagem5.src = '/imgs/cards/' + dados[0][4] + '.png';
				imagem5.addEventListener('load', function(){
		         		trava_card5 = false;
		         		loadcache();
		    	});
			} else trava_card5 = false;
			clearInterval(i);
			listenners_nao_criados = false;
		}
	}, 200);
}

var janela_chat_show = false;
function border_chat() {
    if(!janela_chat_show) {
        janela_chat_show = true;
        $('#aviso_inbox').css({'display': 'none'});
        $('#inbox').height('50%');
        $('#topo_inbox').height('8%');
        $("#msgs_inbox").animate({scrollTop: $("#msgs_inbox").get(0).scrollHeight}, 1000);
    }
    else {
        janela_chat_show = false;
        $('#inbox').height('25px');
        $('#topo_inbox').height('100%');
    }
}
var conteudo_inbox = false;
function ler_chat() {
      $.ajax({
        dataType: "json",
        type: 'get',
        data: 'inbox=1',
        url: 'duelo.php',
        success: function(retorno){
          var texto = '<p>';
          for(var x = 0; x < retorno.length; x++) {
              texto += '<b>'+retorno[x].quem+'</b><br>'+retorno[x].msg+'<br>';
          }
          texto += '</p>'
          if(conteudo_inbox != texto) { //mensagens nova
              if(!janela_chat_show && conteudo_inbox !== false) $('#aviso_inbox').css({'display': 'block'});
              conteudo_inbox = texto;
              $('#msgs_inbox').html(texto);
              $("#msgs_inbox").animate({scrollTop: $("#msgs_inbox").get(0).scrollHeight}, 100);
          }
        }
      });
}

function abertura() {
	tocar('abertura');
	$("#loading").animate({
        opacity: '0',
    }, 4000, function() {$("#loading").css({'display': 'none'});});
}
function toggleFullScreen() {
  if (!document.fullscreenElement &&    // alternative standard method
      !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
      document.documentElement.msRequestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
  }
}
</script>

</body>
</html>
<?php
function mostrar_geral() {
global $duelo;
$array_hand = $duelo->ler_mao($_SESSION['id'], 0);
$array_campo = $duelo->ler_campo($_SESSION['id']);
$array_campoo = $duelo->ler_campo($duelo->oponente($_SESSION['id']));
$tempo = $duelo->controle_de_turno();
if(file_exists($duelo->dir_duelo.'STOP.txt')) $tempo = 'S';
if($array_hand === false || $array_campo === false || $array_campoo === false || $duelo->ler_lps($_SESSION['id']) == 0 || $duelo->ler_lps($duelo->oponente($_SESSION['id'])) == 0) {echo 'END'; return false;}
echo converter_hand($array_hand).'-'.converter_campo($array_campo).'-'.$duelo->ler_lps($_SESSION['id']).';'.$array_campo[11][1].';'.$duelo->ler_Ncmt($_SESSION['id']).';'.$duelo->ler_Ndeck($_SESSION['id']).'-'.$duelo->ler_lps($duelo->oponente($_SESSION['id'])).';'.$array_campoo[11][1].';'.$duelo->ler_Ncmt($duelo->oponente($_SESSION['id'])).';'.$duelo->ler_Nhand($duelo->oponente($_SESSION['id'])).'|'.$duelo->ler_Ndeck($duelo->oponente($_SESSION['id'])).'-'.$tempo.'-'.$duelo->ler_phase($_SESSION['id']).verificarlog();
}

function mostrar_campoOponente() {
global $duelo;
$array_campoo = $duelo->ler_campo($duelo->oponente($_SESSION['id']));
echo converter_campo($array_campoo);
}

function mostrar_infoCard($str) {
$local = substr($str, 0, 1); // 0 mão. 1 campo incluindo field. 2 campo adversario. 3 cemitério. 4 cemitério do adversário
$posicao = substr($str, 1, 99);
// a partir do local e da posição o algoritmo deve descobrir o id
global $duelo;
$id = $duelo->local_id($local, $posicao);
if(!(int)$id) {echo '0'; return 0;}
if($local < 2) {$usavel = 1;}
else {$usavel = 0;}

$carta = new DB_cards();
$carta->ler_id($id);

 if($carta->categoria == 'monster') {
     if($local == 1) {
      $ataque = $duelo->get_atk($_SESSION['id'], $posicao);
      $defesa = $duelo->get_def($_SESSION['id'], $posicao);
      $lv = $duelo->get_lv($_SESSION['id'], $posicao);
     }
     elseif($local == 2) {
      $ataque = $duelo->get_atk($duelo->oponente($_SESSION['id']), $posicao);
      $defesa = $duelo->get_def($duelo->oponente($_SESSION['id']), $posicao);
      $lv = $duelo->get_lv($duelo->oponente($_SESSION['id']), $posicao);
     }
     else {
     $defesa = $carta->def;
     $ataque = $carta->atk;
     $lv = $carta->lv;
     }
  echo $carta->id.';'.$lv.';'.$ataque.';'.$defesa.';'.$carta->atributo.';'.$carta->specie.';'.$carta->descricao.';'.$usavel;
 }
 else {
  echo $carta->id.';0;0;0;0;0;'.$carta->descricao.';0';
	}
unset($carta);
}

function mostrar_actsCard($str) {
$local = (int)$str; // 0 mão. 1 campo incluindo field. 2 campo adversario. 3 cemitério. 4 cemitério do adversário
if($local == 0) { // mostrar nada é possivel
    echo '0-0-0-0-0-0';
    exit();
}

global $duelo;
$acts = $duelo->acts_card($_SESSION['id'], $local);
if($acts === false) {
    echo '0-0-0-0-0-0';
    exit();
}

if($acts['ativar'] == true) echo '1-'; // pode at~ivar
else echo '0-'; // não pode ativar
if($acts['atacar'] == true) echo '1-'; // pode atacar
else echo '0-'; // não pode atacar
if($acts['posição_ataque'] == true) echo '1-'; // pode mudar pra ataque
else echo '0-'; // não pode mudar para ataque
if($acts['posição_defesa'] == true) echo '1-'; // pode mudar para defesa
else echo '0-'; // não pode mudar para defesa
if($acts['flipar'] == true) echo '1-'; // pode flipar
else echo '0-'; // não pode flipar
if($acts['sacrificar'] == true) echo '1'; // pode ser sacrificado
else echo '0'; // não pode ser sacrificado

}

function invocar($str) {
$temp = explode(',', $str);
$local1 = (int)$temp[0];
$posicao = (int)$temp[1];
$local = (int)$temp[2];
global $duelo;
$array_hand = $duelo->ler_mao($_SESSION['id'], 0);
if($local1 < 1 || $local1 > 7 || !isset($array_hand[$local1])) {return 0;}
if($local < 1 || $local > 11) {return 0;}
if($posicao < 1 || $posicao > 4) {return 0;}
$duelo->apagar_carta_hand($_SESSION['id'], $local1); // apagando incodicionalmente
if(!$duelo->invocar($_SESSION['id'], $local, $posicao, $array_hand[$local1])) {
    $duelo->colocar_carta_hand($array_hand[$local1], $_SESSION['id']); // colocando de volta caso não dê certo
    return 0;
}
}
function campoAct($str) {
$temp = explode(',', $str);
$posicao = $temp[0];
$act = $temp[1];
global $duelo;
switch ($act) {
case '1':
$duelo->ativar_efeito($_SESSION['id'], $posicao);
break;
case '2':
break;
case '3':
$duelo->mudar_modo($_SESSION['id'], $posicao, 1);
break;
case '4':
$duelo->mudar_modo($_SESSION['id'], $posicao, 2);
break;
case '5':
$duelo->mudar_modo($_SESSION['id'], $posicao, 1); //flipar
break;
case '6':
$duelo->sacrificar($_SESSION['id'], $posicao);
break;
default:
return 0;
break;
}
}
function atacar($str) {
$temp = explode(',', $str);
$local1 = $temp[0];
$local2 = $temp[1];
global $duelo;
$duelo->atacar($_SESSION['id'], $local1, $local2);
}
function proxima_phase() {
global $duelo;
switch ($duelo->ler_phase($_SESSION['id'])) {
case '3':
$duelo->m1_phase($_SESSION['id'], 1);
break;
case '4':
$duelo->battle_phase($_SESSION['id'], 1);
break;
case '5':
$duelo->m2_phase($_SESSION['id'], 1);
break;
default:
return 0;
break;
}
}
function ler_log($apagar = 0) {
if(file_exists('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/log.txt')) {
	if($apagar) {
	 echo str_replace("\n", '<br><hr>', file_get_contents('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/log.txt'));
	 @unlink('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/log.txt');
	}
  return 1;
}
elseif(file_exists('duelos/'.$_SESSION['id_duelo'].'/log.txt')) {
	$grav = new Gravacao();
	$grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/log.txt');
	$array = $grav->ler(0);
	if($array[1] != $_SESSION['id']) {
        if($apagar) {echo tratar_log(file_get_contents('duelos/'.$_SESSION['id_duelo'].'/log.txt'));}
	} else {return 0;}
	if($apagar) {
	 if($array[1] != 0 && $array[1] != $_SESSION['id']) {
	  @unlink('duelos/'.$_SESSION['id_duelo'].'/log.txt');
	 }
	else {
		$array[1] = $_SESSION['id'];
		$grav->set_array($array);
		$grav->gravar();
		unset($grav);
		}
	}
	return 1;
	}
        elseif(file_exists('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/logc.txt')) {return 2;}
	return 0;
}
function ler_logl() {
    if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/logc.txt')) return false;
 $vetor = explode("\n", file_get_contents('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/logc.txt'));
 $string = $vetor[1].'-';
 for($x = 2;$x < count($vetor); $x++) {
     $string .= $vetor[$x].";";
     $links .= ($x-2).";";
 }
 if(time() > (int)$vetor[0]) @unlink('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/logc.txt');
 echo substr($string, 0 , -1).'-'.substr($links, 0 , -1);
}

function responder_carta($x) {
    global $duelo;
    $duelo->responder_carta($x, $_SESSION['id']);
}

function verificarlog() {
if(ler_log() == 1) {return '-l';}
elseif(ler_log() == 2) {return '-c';}
}

function mostrar_cmt($qual) {
global $duelo;
if($qual == 1) {$qual = $_SESSION['id'];}
else {$qual = $duelo->oponente($_SESSION['id']);}
if(!$duelo->ler_Ncmt((int)$qual)) {echo '0';return 0;}
$array = $duelo->ler_cmt($qual);
for($x = 1; $x < $array[0]; $x++) {
$retorno .= $array[$x].';';
}
echo substr($retorno, 0, -1);
}
function converter_hand($array) {
$retorno = '';
 for($x = 1; $x <= 7; $x++) {
	if(!$array[$x]) {$array[$x] = 0;}
  $retorno .= $array[$x].';';
 }
return substr($retorno, 0, -1);
}
function converter_campo($array) {
$retorno = '';
 for($x = 1; $x <= 11; $x++) {
	if($array[$x][0] > 2) {$array[$x][1] = 0;}
  $retorno .= $array[$x][0].$array[$x][1].';';
  if($x == 5) {$retorno = substr($retorno, 0, -1).'-';}
 }
return substr($retorno, 0, -1);
}

function tratar_log($txt) { // testar pra ver se funfa
    $array = explode("\n", $txt);
   for($x = 1; $x < count($array)-1; $x++) {$retorno .= $array[$x].'<br><hr>';}
    return $retorno;
}

function mostrar_chat() {
    if(!isset($_SESSION['id_duelo']) || $_SESSION['id_duelo'] === '') return false;
    $chat = new Inbox($_SESSION['id_duelo']);
    $msgs = $chat->read_file();
    for($x = 0; $x < count($msgs); $x++) {
        if($msgs[$x]['quem'] == $_SESSION['id']) $msgs[$x]['quem'] = 'Você';
        else $msgs[$x]['quem'] = 'Oponente';
    }
    echo json_encode($msgs);
    return true;
}
function enviar_msg($msg) {
    if(!isset($_SESSION['id_duelo']) || $_SESSION['id_duelo'] === '') return false;
    $chat = new Inbox($_SESSION['id_duelo']);
    $chat->enviar($_SESSION['id'], $msg);
    return true;
}

function finalizar() {
 if($_SESSION['resultado'] != '') {exit();}
 $grav = new Gravacao();
 $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/metadata.txt');
 $matriz = $grav->ler(1);
 unset($grav);
 $lp1 = file_get_contents('duelos/'.$_SESSION['id_duelo'].'/'.$matriz[2][0].'/lps.txt');
 $lp2 = file_get_contents('duelos/'.$_SESSION['id_duelo'].'/'.$matriz[2][1].'/lps.txt');
 if($lp1 == 0) {$_SESSION['resultado'] = $matriz[2][1].'/'.$matriz[2][0]; return 1;}
 if($lp2 == 0) {$_SESSION['resultado'] = $matriz[2][0].'/'.$matriz[2][1]; return 1;}
 return 0;
}
?>