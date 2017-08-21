<?php
include("../libs/tools_lib.php");
include("libs/duelo_lib.php");
include("../libs/db_lib.php");
session_start();

if($_SESSION["ban"] == 'S') {header("location: ../logout.php"); exit();}
if($_SESSION['duelando'] != 'S') {header("location: loby.php"); exit();}

$tools = new Tools(true);
$tools->verificar();

?>
<html>
<head>
 <title>Duelo Yu-Gi-Oh Unlimited</title>
 <link rel="stylesheet" href="../fonte.css" type="text/css" media="screen"/>
 <?php include '../head.txt';?>
</head>
<body onload="document.getElementById('loading').style.display = 'none';">
<div id="loading" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: black; border: 0; z-index: 100; display: block;">
<img src="../imgs/loading.jpg" width="80%" height="80%" style="position: absolute; top: 10%; left: 10%;" />
</div>
<div id="Div1" style="overflow: hidden; width: 100%; height: 10%; border: 0; position:relative; top: 0; left: 0;">
 <div id="D_Tempo" style="overflow: hidden; width: 40%; height: 100%; border: 0; position:absolute; top: 0; left: 0;" onClick="turno_off()">
  <b id="Tempo" style="position: absolute; top: 20%; left: 20%; font-size: 120%; color: red;"></b>
 </div>
 <div id="Status" style="overflow: hidden; width: 40%; height: 100%; border: 0; position:absolute; top: 0; left: 40%;" onClick="proxima_fase()">
  <img src = "../imgs/bf0.png" width="100%" height="100%" />
 </div>
 <div id="B_adv" style="overflow: hidden; width: 20%; height: 100%; border: 0; position:absolute; top: 0; left: 80%;" onClick="alterarCampo()">
  <img id="campoAtual" src = "../imgs/vs_icon.png" width="100%" height="100%" />
 </div>
</div>

<div id="DivG" style="overflow: hidden; width: 100%; height: 25%; border: 0; position:relative; top: 0; left: 0;">
 <div id="D_adv" style="overflow: hidden; width: 100%; height: 50%; border: 0; position:absolute; top: 0; left: 0%;">
  <div id="DA_LPs" style="overflow: hidden; width: 40%; height: 100%; border: 1px solid red; position:absolute; top: 0; left: 0%;">
   <img src = "../imgs/lps.png" width="100%" height="100%" style="z-index: 1;" />
   <b id="A_lps" style="font-size: 150%; position: absolute; top: 20%; left: 20%; z-index: 2;"></b>
  </div>
  <div id="DA_field" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid red; position:absolute; top: 0; left: 40%;">
   <img src = "../imgs/BackSideField.png" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
  <div id="DA_cmt" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid red; position:absolute; top: 0; left: 60%;" onClick="cemiterio(2)" >
   <img src="../imgs/grav.png" width="100%" height="100%" style="z-index: 1;" />
   <b id="A_cmt" style="font-size: 120%; position: absolute; top: 30%; left: 30%; z-index: 2; color: white;"></b>
  </div>
  <div id="DA_deck" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid red; position:absolute; top: 0; left: 80%;">
   <img src="../imgs/BackSideCard.jpg" width="100%" height="100%" style="z-index: 1;" />
   <b id="A_deck" style="font-size: 120%; position: absolute; top: 30%; left: 20%; z-index: 2; color: white;"></b>
  </div>
 </div>
 <div id="D_duelista" style="overflow: hidden; width: 100%; height: 50%; border: 0; position:absolute; top: 50%; left: 0%;">
  <div id="DD_LPs" style="overflow: hidden; width: 40%; height: 100%; border: 1px solid blue; position:absolute; top: 0; left: 0%;">
   <img src = "../imgs/lps.png" width="100%" height="100%" style="z-index: 1;" />
   <b id="D_lps" style="font-size: 150%; position: absolute; top: 20%; left: 20%; z-index: 2"></b>
  </div>
  <div id="DD_field" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid blue; position:absolute; top: 0; left: 40%;" onClick="invocar(11)">
   <img src = "../imgs/BackSideField.png" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
  <div id="DD_cmt" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid blue; position:absolute; top: 0; left: 60%;" onClick="cemiterio(1)" >
   <img src="../imgs/grav.png" width="100%" height="100%" style="z-index: 1;" />
   <b id="D_cmt" style="font-size: 120%; position: absolute; top: 30%; left: 30%; z-index: 2; color: white;"></b>
  </div>
  <div id="DD_deck" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid blue; position:absolute; top: 0; left: 80%;">
   <img src="../imgs/BackSideCard.jpg" width="100%" height="100%" style="z-index: 1;"/>
   <b id="D_deck" style="font-size: 120%; position: absolute; top: 30%; left: 30%; z-index: 2; color: white;"></b>
  </div>
 </div>
</div>

<div id="DivCampo" style="overflow: hidden; width: 100%; height: 50%; border: 0; position:relative; top: 0; left: 0;">
 <div id="C_monstros" style="overflow: hidden; width: 100%; height: 50%; border: 0; position:absolute; top: 0; left: 0%;">
  <div id="M1" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 0%;" onClick="invocar(1)" >
   <img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
  <div id="M2" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 20%;" onClick="invocar(2)" >
   <img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
  <div id="M3" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 40%;" onClick="invocar(3)" >
   <img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
  <div id="M4" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 60%;" onClick="invocar(4)" >
   <img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
  <div id="M5" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 80%;" onClick="invocar(5)" >
   <img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
 </div>
 <div id="C_magic" style="overflow: hidden; width: 100%; height: 50%; border: 0; position:absolute; top: 50%; left: 0%;">
  <div id="E1" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 0%;" onClick="invocar(6)" >
   <img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
  <div id="E2" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 20%;" onClick="invocar(7)" >
   <img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
  <div id="E3" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 40%;" onClick="invocar(8)" >
   <img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
  <div id="E4" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 60%;" onClick="invocar(9)" >
   <img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
  <div id="E5" style="overflow: hidden; width: 20%; height: 100%; border: 1px solid white; position:absolute; top: 0; left: 80%;" onClick="invocar(10)" >
   <img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />
  </div>
 </div>
</div>

<div id="DivHand" style="overflow: hidden; width: 100%; height: 15%; border: 1px solid white; position:relative; top: 0; left: 0;">
 <div id="H_C1" style="overflow: hidden; width: 13%; height: 100%; border: 0; position:absolute; top: 0; left: 1%;"></div>
 <div id="H_C2" style="overflow: hidden; width: 13%; height: 100%; border: 0; position:absolute; top: 0; left: 15%;"></div>
 <div id="H_C3" style="overflow: hidden; width: 13%; height: 100%; border: 0; position:absolute; top: 0; left: 29%;"></div>
 <div id="H_C4" style="overflow: hidden; width: 13%; height: 100%; border: 0; position:absolute; top: 0; left: 43%;"></div>
 <div id="H_C5" style="overflow: hidden; width: 13%; height: 100%; border: 0; position:absolute; top: 0; left: 57%;"></div>
 <div id="H_C6" style="overflow: hidden; width: 13%; height: 100%; border: 0; position:absolute; top: 0; left: 71%;"></div>
 <div id="H_C7" style="overflow: hidden; width: 13%; height: 100%; border: 0; position:absolute; top: 0; left: 85%;"></div>
</div>

  <div id="inbox" style="position: absolute; top: 0px; left: 0%; width: 7%; height: 6%; overflow: hidden; background-color: white; border-radius: 10px;  z-index: 20;">
      <div id="topo_inbox" style="width: 100%; position: absolute; top: 0px; left: 0px; height: 100%; background-color: #00FFFF; text-align: center; vertical-align: middle; border-top-left-radius: 10px; border-top-right-radius: 10px; cursor: pointer; z-index: 3;" onclick="border_chat();">
          <img id="inbox_ico" src="../imgs/chat_ico.png" width="100%" height="100%" style="position: absolute; top: 3px; left: 0px"/>
      </div>
      <div id="msgs_inbox" style="width: 100%; text-align: justify; word-wrap: break-word; overflow: scroll; position: absolute; top: 10%; left: 0px; height: 75%; vertical-align: bottom; overflow: auto; z-index: 2;"></div>
      <textarea id="dialog_box" rows="2" style="width: 100%; position: absolute; bottom: 0px; left: 0px; border: 1px solid #d3d3d3;" maxlength="300"></textarea>
  </div>
    
<div id="fundo" style="position: absolute; top: 0; left: 0; opacity: 0.5; width: 100%; height: 100%; background-color: black; border: 0; z-index: 10; display: none;" onClick="removerCamada()"></div>
<div id="duelo_susp" style="position: absolute; top: 0; left: 0; opacity: 0.8; width: 100%; background-color: black; border: 0; z-index: 11; display: none; text-align: center;">
    <h1 style="color: white; background-color: black;">AGUARDANDO...</h1>
</div>

<div id="info_card" style="position: absolute; top: 5%; left: 10%; width: 80%; height: 80%; background-color: white; border: 1px solid green; z-index: 11; display: none;">
<img id="2_imagemCarta" src="" width="60%" height="60%" style="position: absolute; top: 0; left: 0;">
<table id="2_infoMonstro" border="0" width="40%" height="60%" style ="position: absolute; top: 0; left: 60%; display: none; z-index: 11;">
 <tr><td align="center"><b id="2_level"></b></td></tr>
 <tr><td align="center"><b id="2_atk"></b></td></tr>
 <tr><td align="center"><b id="2_def"></b></td></tr>
 <tr><td align="center"><b id="2_atributo"></b></td></tr>
 <tr><td align="center"><b id="2_especie"></b></td></tr>
</table>
<div id="2_descricao" style="overflow: auto; position: absolute; top: 60%; left: 0%; width: 100%; height: 30%; border: 1px solid black;"></div>
<img id="2_usarCarta" src="../imgs/usar_carta.png" width="100%" height="10%" style="position: absolute; top: 90%; left: 0; display: none;" onClick="usarCarta()" />
</div>

<div id="invocar_card" style="position: absolute; top: 30%; left: 25%; width: 50%; height: 40%; background-color: white; border: 1px solid green; z-index: 11; display: none;">
<b style="position: absolute; top: 0; left: 5%;">Escolha a posição</b>
 <div style="position: absolute; overflow: hidden; width: 100%; height: 50%; left: 0; top: 25%; border: 0;">
  <img id="2_cima" src="../imgs/ExemploCarta.jpg" width="50%" height="100%" onClick="invocar(1)" />
  <img id="2_baixoDef" src="../imgs/backcardDef.png" width="50%" height="50%" style="position: absolute; top: 25%; left: 50%;" onClick="invocar(4)" />
  <img id="2_baixo" src="../imgs/backcard.png" width="50%" height="100%" style="position: absolute; top: 0%; left: 50%;" onClick="invocar(3)" />
 </div>
</div>

<div id="usarCartaCampo" style="position: absolute; top: 25%; left: 20%; width: 60%; height: 50%; background-color: white; border: 1px solid green; z-index: 11; display: none;">
   <div id="2_Bativar" style="position: absolute; top: 0%; left: 0; width: 97%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(1)">
    <b style="font-family: YuGiOh; position: absolute; top: 15%; left: 30%; font-size: 120%;">Ativar</b>
   </div>
   <div id="2_Batacar" style="position: absolute; top: 17%; left: 0; width: 97%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(2)">
    <b style="font-family: YuGiOh; position: absolute; top: 15%; left: 30%; font-size: 120%;">Atacar</b>
   </div>
   <div id="2_Batk" style="position: absolute; top: 33%; left: 0; width: 97%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(3)">
    <b style="font-family: YuGiOh; position: absolute; top: 15%; left: 5%; font-size: 120%;">modo: ataque</b>
   </div>
   <div id="2_Bdef" style="position: absolute; top: 49%; left: 0; width: 97%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(4)">
    <b style="font-family: YuGiOh; position: absolute; top: 15%; left: 5%; font-size: 120%;">modo: defesa</b>
   </div>
   <div id="2_Bvirar" style="position: absolute; top: 65%; left: 0; width: 97%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(5)">
    <b style="font-family: YuGiOh; position: absolute; top: 15%; left: 30%; font-size: 120%;">flipar</b>
   </div>
   <div id="2_Bsacrificar" style="position: absolute; top: 82%; left: 0; width: 97%; height: 16%; border: 3px solid #4B0082; margin: 0px; padding: 0px; background-color: #E6E6FA; display: none;" onClick="campoAct(6)">
    <b style="font-family: YuGiOh; position: absolute; top: 15%; left: 10%; font-size: 120%;">sacrificar</b>
   </div>
</div>

<div id="ListaCartas" style="position: absolute; top: 25%; left: 0; width: 100%; height: 40%; background-color: white; border: 1px solid green; z-index: 11; display: none;">
<table border="1" width="100%" height="10%" style="position: absolute; top: 0; left: 0;"><tr><td align="center"><b id="ListaCartastxt"></b></td></tr></table>
<div id="ListaCartasimgs" style="overflow: scroll; position: absolute; top: 15%; left: 0; width: 100%; height: 90%; background-color: white; border: 0; z-index: 11;"></div>
</div>

<div id="DivLog" style="overflow: scroll; position: absolute; top: 25%; left: 0; width: 100%; height: 50%; background-color: white; border: 1px solid green; z-index: 11; display: none;">
</div>

<script src="../bootstrap/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="libs/php.js"></script>
<script type="text/javascript">
var campo_na_tela = 'duelista';
var fase = 0;
var link = '<?php echo $tools->http.$_SERVER['SERVER_NAME'];?>';
var dados;
var especial = 0;
var invocarC = 0;
var info_card_tipo = 0;
var info_card_local = 0;
var info_card_posicao = 0;
var logs_salvos = '';

exibir();
window.setInterval("exibir()", 1000);

function exibir() {
dados = ler_servidor();
ler_chat();
set_hand(dados[0]);
if(campo_na_tela == 'duelista') {
 set_Campo(dados[1], dados[2]);
}
else {
 var campo_oponente = ler_campoOponente();
 set_Campo(campo_oponente[0], campo_oponente[1]);
}
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
}

function set_status(status) {
if(status == 1) {status = 2;}
if(fase == 0 && status != 0) {window.navigator.vibrate(2000);}
fase = status;
 if(status == 0) {
	document.getElementById("Status").innerHTML = '<img src = "../imgs/bf0.png" width="100%" height="100%" />';
	}
	else if(status < 6) {
	 document.getElementById("Status").innerHTML = '<img src = "../imgs/bf' + status + '.png" width="50%" height="100%" /> <img src = "../imgs/bf' + (status + 1) + '.png" width="50%" height="100%" style="position: absolute; left: 50%; opacity: 0.5;" />';
	}
	else {
	 document.getElementById("Status").innerHTML = '<img src = "../imgs/bf5.png" width="50%" height="100%" style="opacity: 0.5;" /> <img src = "../imgs/bf6.png" width="50%" height="100%" style="position: absolute; left: 50%;" />';
	}
}

function set_dadosGerais(duelista, adversario) {
if(duelista[0] == '0' || adversario[0] == '0') {
file_get_contents(link + "/ZD/duelo.php?f=1");
window.location.href = link + "/ZD/end.php";
}

	var fundo = '<img src = "../imgs/BackSideField.png" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />\n';
 document.getElementById("D_lps").innerText = duelista[0];
if(duelista[1] != 0) {document.getElementById("DD_field").innerHTML = fundo + '<img src="../imgs/cards/pequenas/' + duelista[1] + '.png" width="80%" height="100%" style="position: absolute; top: 0; left: 10%; z-index: 2;" onClick="info_card(1, 11)" />';}
else {document.getElementById("DD_field").innerHTML = fundo;}
 document.getElementById("D_cmt").innerText = duelista[2];
 document.getElementById("D_deck").innerText = duelista[3];

 document.getElementById("A_lps").innerText = adversario[0];
 if(adversario[1] != 0) {document.getElementById("DA_field").innerHTML = fundo + '<img src="../imgs/cards/pequenas/' + adversario[1] + '.png" width="80%" height="100%" style="position: absolute; top: 0; left: 10%; -webkit-transform:rotate(180deg); -moz-transform:rotate(180deg);" onClick="info_card(1, 12)" />';}
 else {document.getElementById("DA_field").innerHTML = fundo;}
 document.getElementById("A_cmt").innerText = adversario[2];
 document.getElementById("A_deck").innerText = adversario[3];
}

function set_Campo(monstros, especiais) {
	var fundo = '<img src = "../imgs/BackSideCard.jpg" width="100%" height="100%" style="opacity: 0.7; z-index: 1;" />\n';
 for(var x = 0; x <= 4; x++) {
	if(monstros[x] != 0) {
		switch (monstros[x].substr(0, 1)) {
		 case '1':
		  document.getElementById("M" + (x+1)).innerHTML = fundo + '<img id="card' + (x+1) + '" src="../imgs/cards/pequenas/' + monstros[x].substr(1) + '.png" width="90%" height="80%" style="position: absolute; top: 10%; left: 5%; z-index: 2;" onClick="info_card(1, ' + (x+1) + ')" />';
		 break;
		 case '2':
		  document.getElementById("M" + (x+1)).innerHTML = fundo + '<img id="card' + (x+1) + '" src="../imgs/cards/pequenas/' + monstros[x].substr(1) + '.png" width="60%" height="50%" style="position: absolute; top: 20%; left: 20%; z-index: 2; -webkit-transform:rotate(90deg); -moz-transform:rotate(90deg)" onClick="info_card(1, ' + (x+1) + ')" />';
		 break;
		 case '3':
	 	  document.getElementById("M" + (x+1)).innerHTML = fundo + '<img id="card' + (x+1) + '" src="../imgs/backcard.png" width="90%" height="80%" style="position: absolute; top: 10%; left: 5%; z-index: 2;" onClick="info_card(1, ' + (x+1) + ')" />';
		 break;
 		case '4':
		 document.getElementById("M" + (x+1)).innerHTML = fundo + '<img id="card' + (x+1) + '" src="../imgs/backcard.png" width="60%" height="50%" style="position: absolute; top: 20%; left: 20%; z-index: 2; -webkit-transform:rotate(90deg); -moz-transform:rotate(90deg)" onClick="info_card(1, ' + (x+1) + ')" />';
		break;
   }
	}
	else {
	 document.getElementById("M" + (x+1)).innerHTML = fundo;
	}
	if(especiais[x] != 0) {
 	 switch (especiais[x].substr(0, 1)) {
		 case '1':
		  document.getElementById("E" + (x+1)).innerHTML = fundo + '<img id="card' + (x+6) + '" src="../imgs/cards/pequenas/' + especiais[x].substr(1) + '.png" width="90%" height="80%" style="position: absolute; top: 10%; left: 5%; z-index: 2;" onClick="info_card(1, ' + (x+6) + ')" />';
		 break;
		 case '3':
	 	  document.getElementById("E" + (x+1)).innerHTML = fundo + '<img id="card' + (x+6) + '" src="../imgs/backcard.png" width="90%" height="80%" style="position: absolute; top: 10%; left: 5%; z-index: 2;" onClick="info_card(1, ' + (x+6) + ')" />';
		 break;
   }
	}
	 	else {
	 document.getElementById("E" + (x+1)).innerHTML = fundo;
	}
 }
}

function set_hand(hand) {
 for(var x = 0; x <= 6; x++) {
	if(hand[x] != 0) {
		 document.getElementById("H_C" + (x+1)).innerHTML = '<img src="../imgs/cards/pequenas/' + hand[x] + '.png" width="100%" height="100%" onClick="info_card(0, ' + (x+1) + ')" />';
	}
	else {document.getElementById("H_C" + (x+1)).innerHTML = '';}
 }
}

function alterarCampo() {
 if(campo_na_tela == "duelista") {
	 document.getElementById('campoAtual').src = "../imgs/home_icon.png";
	 document.getElementById('M1').style.border = "1px solid red";
	 document.getElementById('M2').style.border = "1px solid red";
	 document.getElementById('M3').style.border = "1px solid red";
	 document.getElementById('M4').style.border = "1px solid red";
	 document.getElementById('M5').style.border = "1px solid red";
	 document.getElementById('E1').style.border = "1px solid red";
	 document.getElementById('E2').style.border = "1px solid red";
	 document.getElementById('E3').style.border = "1px solid red";
	 document.getElementById('E4').style.border = "1px solid red";
	 document.getElementById('E5').style.border = "1px solid red";
	 campo_na_tela = 'oponente';
	}
	else {
  	document.getElementById('campoAtual').src = "../imgs/vs_icon.png";
	 document.getElementById('M1').style.border = "1px solid white";
	 document.getElementById('M2').style.border = "1px solid white";
	 document.getElementById('M3').style.border = "1px solid white";
	 document.getElementById('M4').style.border = "1px solid white";
	 document.getElementById('M5').style.border = "1px solid white";
	 document.getElementById('E1').style.border = "1px solid white";
	 document.getElementById('E2').style.border = "1px solid white";
	 document.getElementById('E3').style.border = "1px solid white";
	 document.getElementById('E4').style.border = "1px solid white";
	 document.getElementById('E5').style.border = "1px solid white";
	 campo_na_tela = 'duelista';
	}
}

function ler_servidor() {
 var retorno = new Array(7);
 
 retorno[0] = new Array(7);
 retorno[1] = new Array(5);
 retorno[2] = new Array(5);
 retorno[3] = new Array(4);
 retorno[4] = new Array(4);

 var bruto  = file_get_contents(link + "/ZD/duelo.php?dados=1");
 if(bruto === 'END') {
     file_get_contents(link + "/ZD/duelo.php?f=1");
     window.location.href = link + "/ZD/end.php";
 }

 var array = bruto.split("-");
 for(var x = 0; x <= 4; x++) {
  retorno[x] = array[x].split(";");
 }
 retorno[5] = array[5];
 retorno[6] = array[6];

if(array[7] == 'l') {Log();}
if(array[7] == 'c') {Slista();}

return retorno;
}

function ler_campoOponente() {
 var retorno = new Array(2);
 retorno[0] = new Array(5);
 retorno[1] = new Array(5);

 var bruto  = file_get_contents(link + "/ZD/duelo.php?campo_oponente=1");

 var array = bruto.split("-");
 retorno[0] = array[0].split(";");
 retorno[1] = array[1].split(";");
return retorno;
}

function ler_infoCard(local, posicao) {
local = local.toString();
posicao = posicao.toString();
var bruto = file_get_contents(link + '/ZD/duelo.php?info_card=' + local + posicao);
if(bruto == '0') {return 'x';}
var array = new Array(8);
array =  bruto.split(';');
return array;
}

function turno_off() {
file_get_contents(link + '/ZD/duelo.php?turnooff=1');
}

function proxima_fase() {
 set_status(fase + 1);
 file_get_contents(link + '/ZD/duelo.php?pfaze=1');
}

function info_card(local, posicao) {
 if(local == 1 && campo_na_tela == 'oponente') {
	 local = 2;
	}
var infos = ler_infoCard(local, posicao);
if(infos == 'x') {return 0;}
document.getElementById("fundo").style.display = "block";
document.getElementById("info_card").style.display = "block";
document.getElementById("2_imagemCarta").src = '../imgs/cards/' + infos[0] + '.png';
 if(((posicao < 6 && posicao > 0) && (local == 1 || local == 2)) || infos[1] != 0) {
  document.getElementById("2_imagemCarta").style.left = '0%';
  document.getElementById("2_infoMonstro").style.display = 'block';
  document.getElementById("2_level").innerText = 'Level: ' + infos[1];
  document.getElementById("2_atk").innerText = 'Ataque: ' + infos[2];
  document.getElementById("2_def").innerText = 'Defesa: ' + infos[3];
  document.getElementById("2_atributo").innerText = 'Atributo: ' + infos[4];
  document.getElementById("2_especie").innerText = 'Espécie: ' + infos[5];
 }
else {
	 document.getElementById("2_infoMonstro").style.display = 'none';
  document.getElementById("2_imagemCarta").style.left = '20%';
 }
 document.getElementById("2_descricao").innerText = infos[6];
if(fase == 0 || local == 2) {
   document.getElementById("2_descricao").style.height = "40%";
 }
else {
	 document.getElementById("2_descricao").style.height = "30%";
	 document.getElementById("2_usarCarta").style.display = "block";
	 info_card_tipo = infos[7];
	 info_card_local = local;
	 info_card_posicao = posicao;
	}
}

function usarCarta() {
 document.getElementById("info_card").style.display = "none";
 if(info_card_local == 0) {
  document.getElementById("invocar_card").style.display = "block";
	 invocarC = 1;
         document.getElementById("2_cima").style.display = "block";
  if(info_card_tipo !== '0') {
         document.getElementById("2_baixo").style.display = "none";
	 document.getElementById("2_baixoDef").style.display = "block";
	}
	else {
         document.getElementById("2_baixoDef").style.display = "none";
         document.getElementById("2_baixo").style.display = "block";
	}
 }
 if(info_card_local == 1) {
	 document.getElementById("usarCartaCampo").style.display = "block";
	 if(info_card_tipo != 0) {
          var corBorda = "#696969";
          var corFundo = "#DCDCDC";
          
          var bruto = file_get_contents(link + '/ZD/duelo.php?acts_card=' + info_card_posicao);
          var array = new Array(6);
          array =  bruto.split('-');
	  document.getElementById("2_Bativar").style.display = "block";
          if(array[0] == '0') {
              document.getElementById("2_Bativar").style.border = "3px solid "+corBorda;
              document.getElementById("2_Bativar").style.backgroundColor = corFundo;
          }
	  document.getElementById("2_Batacar").style.display = "block";
          if(array[1] == '0') {
              document.getElementById("2_Batacar").style.border = "3px solid "+corBorda;
              document.getElementById("2_Batacar").style.backgroundColor = corFundo;
          }
	  document.getElementById("2_Batk").style.display = "block";
          if(array[2] == '0') {
              document.getElementById("2_Batk").style.border = "3px solid "+corBorda;
              document.getElementById("2_Batk").style.backgroundColor = corFundo;
          }
	  document.getElementById("2_Bdef").style.display = "block";
          if(array[3] == '0') {
              document.getElementById("2_Bdef").style.border = "3px solid "+corBorda;
              document.getElementById("2_Bdef").style.backgroundColor = corFundo;
          }
	  document.getElementById("2_Bvirar").style.display = "block";
          if(array[4] == '0') {
              document.getElementById("2_Bvirar").style.border = "3px solid "+corBorda;
              document.getElementById("2_Bvirar").style.backgroundColor = corFundo;
          }
	  document.getElementById("2_Bsacrificar").style.display = "block";
          if(array[5] == '0') {
              document.getElementById("2_Bsacrificar").style.border = "3px solid "+corBorda;
              document.getElementById("2_Bsacrificar").style.backgroundColor = corFundo;
          }
	}
	else {
	 document.getElementById("2_Bativar").style.display = "block";
	 document.getElementById("2_Batacar").style.display = "none";
	 document.getElementById("2_Batk").style.display = "none";
	 document.getElementById("2_Bdef").style.display = "none";
	 document.getElementById("2_Bvirar").style.display = "none";
	 document.getElementById("2_Bsacrificar").style.display = "none";
	}          
 }
}

function removerCamada() {
document.getElementById("DivLog").style.display = "none";
	if(especial != 0) {return 0;}
document.getElementById("fundo").style.display = "none";
document.getElementById("info_card").style.display = "none";
document.getElementById("invocar_card").style.display = "none";
document.getElementById("2_usarCarta").style.display = "none";
document.getElementById("usarCartaCampo").style.display = "none";
document.getElementById("ListaCartas").style.display = "none";
logs_salvos = '';

// agora fazendo as cores voltarem ao normal
          var corBordaNormal = "#4B0082";
          var corFundoNormal = "#E6E6FA";
              document.getElementById("2_Bativar").style.border = "3px solid "+corBordaNormal;
              document.getElementById("2_Bativar").style.backgroundColor = corFundoNormal;
              document.getElementById("2_Batacar").style.border = "3px solid "+corBordaNormal;
              document.getElementById("2_Batacar").style.backgroundColor = corFundoNormal;
              document.getElementById("2_Batk").style.border = "3px solid "+corBordaNormal;
              document.getElementById("2_Batk").style.backgroundColor = corFundoNormal;
              document.getElementById("2_Bdef").style.border = "3px solid "+corBordaNormal;
              document.getElementById("2_Bdef").style.backgroundColor = corFundoNormal;
              document.getElementById("2_Bvirar").style.border = "3px solid "+corBordaNormal;
              document.getElementById("2_Bvirar").style.backgroundColor = corFundoNormal;
              document.getElementById("2_Bsacrificar").style.border = "3px solid "+corBordaNormal;
              document.getElementById("2_Bsacrificar").style.backgroundColor = corFundoNormal;

}

function campoAct(act) {
	especial = 0;
removerCamada();
	if(act == 2) {
	var cards = ler_campoOponente();
	var quantas = 0;
	 for(var x = 0; x < 5; x++) {
	  if(cards[0][x] != 0) {quantas++;}
	}
	var alvos = new Array(quantas + 1);
	var links = new Array(quantas + 1);
	var y = 1;
	 alvos[0] = 'atkd';
	 links[0] = "file_get_contents(link + '/ZD/duelo.php?atacar=" + info_card_posicao + ",d'); removerCamada();";
	for(var x = 0; x < 5; x++) {
	  if(cards[0][x] != 0) {
		if(cards[0][x].substr(0, 1) == '4') {alvos[y] = 'db';}
		else if(cards[0][x].substr(0, 1) == 2) {alvos[y] = 'd' + cards[0][x].substr(1);}
		else {alvos[y] = cards[0][x].substr(1);}
	   links[y] = "file_get_contents(link + '/ZD/duelo.php?atacar=" + info_card_posicao + ',' + (x + 1) + "'); removerCamada();";
	   y++;
	  }
	 }
	 lista_cartas('Selecionar alvo', alvos, links);
return 0;
		}
file_get_contents(link + '/ZD/duelo.php?campoact=' + info_card_posicao + ',' + act);
}

function Log() {
var log = file_get_contents(link + '/ZD/duelo.php?log=1');
if(log == 0) {return 0;}
document.getElementById("fundo").style.display = "block";
document.getElementById("DivLog").style.display = "block";
logs_salvos += log;
document.getElementById("DivLog").innerHTML = logs_salvos;
}

function invocar(onde) {
if(invocarC == 0) {return 0;}
	removerCamada();
 if(especial == 0) {
  	especial = onde;
	 if(campo_na_tela != 'duelista') {alterarCampo();}
	 document.getElementById("fundo").style.display = "block";
	 if(info_card_tipo != 0) {
		 for(var x = 0; x <= 4; x++) {
		  if(dados[1][x] == 0) {document.getElementById('M' + (x+1)).style.zIndex = 11;}
                 }
	 }
  	else {
	 for(var x = 0; x <= 4; x++) {
	  if(dados[2][x] == 0) {document.getElementById('E' + (x+1)).style.zIndex = 11;}
          }
          document.getElementById('DD_field').style.zIndex = 11;
	}
      }
 else {
  file_get_contents(link + '/ZD/duelo.php?invocar=' + info_card_posicao + ',' + especial + ',' + onde);
  document.getElementById("fundo").style.display = "none";
  for(var x = 0; x <= 4; x++) {
  	document.getElementById('M' + (x+1)).style.zIndex = 1;
	  document.getElementById('E' + (x+1)).style.zIndex = 1;
   }
  especial = 0;
  invocarC = 0;
 }
}

function Slista() {
    if(document.getElementById("fundo").style.display === 'block') {return false;}
var bruto = file_get_contents(link + '/ZD/duelo.php?logl=1');
var array = bruto.split('-');
var txt = array[0];
var imgs = array[1].split(';');
var links = array[2].split(';');
for(var y = 0; y < links.length; y++) {links[y] = 'responder_carta('+links[y]+')';}
lista_cartas(txt, imgs, links);
}

function cemiterio(qual) {
var bruto = file_get_contents(link + '/ZD/duelo.php?cmt=' + qual);
if(bruto == 0) {return 0;}
var lista = bruto.split(";");
var lk = new Array(1);
if(qual == 1) {var txt = 'Seu cemitério';}
else {var txt = 'Cemitério do oponente';}
lista_cartas(txt, lista, lk);
}

function responder_carta(x) {
    file_get_contents(link + '/ZD/duelo.php?rcarta=' + x);
    removerCamada();
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
		html += '<div style="position: absolute; top: 0; left: ' + espaco + '%; width: 30%; height: 100%; overflow: hidden;"><img src="../imgs/cards/pequenas/' + imgs[x].substr(1) + '.png" width="60%" height="50%" style="position: absolute; top: 20%; left: 11%; -webkit-transform:rotate(90deg); -moz-transform:rotate(90deg);" onClick="'+links[x]+'" /></div> ';
		}
	else {
   html += '<img src="../imgs/cards/pequenas/' + imgs[x] + '.png" width="30%" height="100%" style="position: absolute; top: 0; left: ' + espaco + '%;" onClick="'+links[x]+'" /> ';
  }
  espaco += 30;
 }
document.getElementById("fundo").style.display = "block";
document.getElementById("ListaCartas").style.display = "block";
document.getElementById("ListaCartastxt").innerText = txt;
document.getElementById("ListaCartasimgs").innerHTML = html;
}

var janela_chat_show = false;
function border_chat() {
    if(!janela_chat_show) {
        janela_chat_show = true;
        $('#inbox').height('100%');
        $('#inbox').width('100%');
        $('#topo_inbox').height('8%');
        $('#topo_inbox').css("background-color","#00FFFF");
        $('#inbox_ico').width('8%');
        
        $("#msgs_inbox").animate({scrollTop: $("#msgs_inbox").get(0).scrollHeight}, 1000);
    }
    else {
        janela_chat_show = false;
        $('#inbox').height('6%');
        $('#inbox').width('7%');
        $('#inbox_ico').width('100%');
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
              if(!janela_chat_show && conteudo_inbox !== false) $('#topo_inbox').css("background-color","red");
              conteudo_inbox = texto;
              $('#msgs_inbox').html(texto);
              $("#msgs_inbox").animate({scrollTop: $("#msgs_inbox").get(0).scrollHeight}, 100);
          }
        }
      });
}
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
</script>
</body>
</html>