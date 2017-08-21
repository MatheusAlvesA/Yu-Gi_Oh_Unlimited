<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/Mobile_Detect.php");
include_once("libs/mapa_lib.php");
include_once("libs/desafio_lib.php");
$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

   $detectar = new Mobile_Detect;
   if($detectar->isMobile()) {
    header('location: home.php?mapamobile=1');
    exit();
   }
    $banco_temp = new DB();
    $banco_temp->ler($_SESSION['id']);
    if($banco_temp->deck[0] < 41) {
            header("location: ../deck.php?incompleto=1");
      exit();
    }


$db = new DB();
$db->ler($_SESSION["id"]);

$mapa = new Mapa;
$quadrante = $db->reino;
if($quadrante == 0) $quadrante = 4;

  $desafio_temp = new Desafio;
  if($desafio_temp->existe($db->nome)) $_SESSION['desafio'] = $desafio_temp->status; // essa linha garante que o desafio per,ateça constantemente atualizado
  else $_SESSION['desafio'] = 'n';

if($_GET['mapear'] == 1) {if(isset($_SESSION['quadrante'])) mapear($_SESSION['quadrante']); exit();}
if($_GET['tempo'] == 1) {echo time();exit();}
if($_GET['atualizar'] == 1) {if(isset($_SESSION['quadrante'])) atualizar($_SESSION['quadrante']); exit();}
if($_GET['status_desafio'] == 1) {status_desafio(); exit();}
if($_GET['aceitar'] == 1) {if($_SESSION['desafio'] === 'P') aceitar_desafio();}
if($_GET['cancelar'] == 1) {if($_SESSION['desafio'] === 'P') cancelar_desafio();}

//zona de comandos
if($_SESSION['desafio'] !== 'P' && $_SESSION['desafio'] !== 's') {
  if($_GET['desafiar'] != '') {
    $vetor = explode(',', $_GET['desafiar']);
    $x = (int)$vetor[0];
    $y = (int)$vetor[1];
    desafiar($x,$y);
    exit();
  }
  if(isset($_GET['mover'])) {mover((int)$_GET['mover']); exit();}
  if(isset($_GET['info'])) {infos($_GET['info']); exit();}
}
spawnar($quadrante); // expawnando o jogador

?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Mapa Yu-Gi-Oh Unlimited</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fonte.css" type="text/css" media="screen"/>
    <link href="style_PC.css" rel="stylesheet"> <!--Estilos exclusivos quando aberto no computador-->

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

<div id="tela_loading" style="width: 100%; height: 100%; background-color: black; display: none; z-index: 10;">
  <div class="row" id="tela_desafiante">

    <div role="main" class="col-md-4 col-md-push-4">
      <table class="table table-striped" cellspacing="0" cellpadding="0">
        <tr style="background-color: black;">
          <td align="center">
            <b id="nome_duelista" style="color: white;">????????</b>
          </td>
          <td align="center">
            <b style="color: red">VS</b>
          </td>
          <td align="center">
            <b id="nome_oponente" style="color: white;">???????</b>
          </td>
        </tr>
      </table>

      <img id="imagem_loading" src="../imgs/load.gif" width="60%" style = "position: relative; left: 20%;" />
      <img src="../imgs/B_aguardando.png" width="100%" />
      <img src="../imgs/B_cancelar.png" width="100%" onclick="$.ajax({type: 'get',data: 'cancelar=1', url:'mapa.php'})" style="cursor: pointer;" />
    </div>

  </div>
  <div class="row" id="tela_desafiado">
    <div role="main" class="col-md-4 col-md-push-4">
      <table class="table table-striped" cellspacing="0" cellpadding="0">
        <tr style="background-color: black;">
          <td align="center">
            <b id="nome_duelista_d" style="color: white;">????????</b>
          </td>
          <td align="center">
            <b style="color: red">VS</b>
          </td>
          <td align="center">
            <b id="nome_oponente_d" style="color: white;">???????</b>
          </td>
        </tr>
      </table>

      <img src="../imgs/B_duelar.png" width="100%" onclick="$.ajax({type: 'get',data: 'aceitar=1', url:'mapa.php'})" style="cursor: pointer;" />
      <img src="../imgs/B_recusar.png" width="100%" onclick="$.ajax({type: 'get',data: 'cancelar=1', url:'mapa.php'})" style="cursor: pointer;" />
    </div>
  </div>
</div>

<div class="row">
  <div role="main" class="col-md-6 col col-md-push-2">
    <div id="mapa" style="width: 625px; height: 625px; position: relative; top: 5px; left: 20px; background-image: url(imgs/RPG/mundo_1_1.jpg);">
      <div id="0x0" style="border: 0; position: absolute; top: 0px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 0)"></div>
      <div id="1x0" style="border: 0; position: absolute; top: 0px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 0)"></div>
      <div id="2x0" style="border: 0; position: absolute; top: 0px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 0)"></div>
      <div id="3x0" style="border: 0; position: absolute; top: 0px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 0)"></div>
      <div id="4x0" style="border: 0; position: absolute; top: 0px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 0)"></div>
      <div id="5x0" style="border: 0; position: absolute; top: 0px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 0)"></div>
      <div id="6x0" style="border: 0; position: absolute; top: 0px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 0)"></div>
      <div id="7x0" style="border: 0; position: absolute; top: 0px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 0)"></div>
      <div id="8x0" style="border: 0; position: absolute; top: 0px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 0)"></div>
      <div id="9x0" style="border: 0; position: absolute; top: 0px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 0)"></div>
      <div id="10x0" style="border: 0; position: absolute; top: 0px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 0)"></div>
      <div id="11x0" style="border: 0; position: absolute; top: 0px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 0)"></div>
      <div id="12x0" style="border: 0; position: absolute; top: 0px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 0)"></div>
      <div id="13x0" style="border: 0; position: absolute; top: 0px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 0)"></div>
      <div id="14x0" style="border: 0; position: absolute; top: 0px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 0)"></div>
      <div id="15x0" style="border: 0; position: absolute; top: 0px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 0)"></div>
      <div id="16x0" style="border: 0; position: absolute; top: 0px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 0)"></div>
      <div id="17x0" style="border: 0; position: absolute; top: 0px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 0)"></div>
      <div id="18x0" style="border: 0; position: absolute; top: 0px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 0)"></div>
      <div id="19x0" style="border: 0; position: absolute; top: 0px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 0)"></div>
      <div id="20x0" style="border: 0; position: absolute; top: 0px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 0)"></div>
      <div id="21x0" style="border: 0; position: absolute; top: 0px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 0)"></div>
      <div id="22x0" style="border: 0; position: absolute; top: 0px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 0)"></div>
      <div id="23x0" style="border: 0; position: absolute; top: 0px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 0)"></div>
      <div id="24x0" style="border: 0; position: absolute; top: 0px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 0)"></div>
      <div id="0x1" style="border: 0; position: absolute; top: 25px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 1)"></div>
      <div id="1x1" style="border: 0; position: absolute; top: 25px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 1)"></div>
      <div id="2x1" style="border: 0; position: absolute; top: 25px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 1)"></div>
      <div id="3x1" style="border: 0; position: absolute; top: 25px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 1)"></div>
      <div id="4x1" style="border: 0; position: absolute; top: 25px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 1)"></div>
      <div id="5x1" style="border: 0; position: absolute; top: 25px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 1)"></div>
      <div id="6x1" style="border: 0; position: absolute; top: 25px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 1)"></div>
      <div id="7x1" style="border: 0; position: absolute; top: 25px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 1)"></div>
      <div id="8x1" style="border: 0; position: absolute; top: 25px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 1)"></div>
      <div id="9x1" style="border: 0; position: absolute; top: 25px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 1)"></div>
      <div id="10x1" style="border: 0; position: absolute; top: 25px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 1)"></div>
      <div id="11x1" style="border: 0; position: absolute; top: 25px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 1)"></div>
      <div id="12x1" style="border: 0; position: absolute; top: 25px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 1)"></div>
      <div id="13x1" style="border: 0; position: absolute; top: 25px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 1)"></div>
      <div id="14x1" style="border: 0; position: absolute; top: 25px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 1)"></div>
      <div id="15x1" style="border: 0; position: absolute; top: 25px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 1)"></div>
      <div id="16x1" style="border: 0; position: absolute; top: 25px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 1)"></div>
      <div id="17x1" style="border: 0; position: absolute; top: 25px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 1)"></div>
      <div id="18x1" style="border: 0; position: absolute; top: 25px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 1)"></div>
      <div id="19x1" style="border: 0; position: absolute; top: 25px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 1)"></div>
      <div id="20x1" style="border: 0; position: absolute; top: 25px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 1)"></div>
      <div id="21x1" style="border: 0; position: absolute; top: 25px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 1)"></div>
      <div id="22x1" style="border: 0; position: absolute; top: 25px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 1)"></div>
      <div id="23x1" style="border: 0; position: absolute; top: 25px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 1)"></div>
      <div id="24x1" style="border: 0; position: absolute; top: 25px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 1)"></div>
      <div id="0x2" style="border: 0; position: absolute; top: 50px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 2)"></div>
      <div id="1x2" style="border: 0; position: absolute; top: 50px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 2)"></div>
      <div id="2x2" style="border: 0; position: absolute; top: 50px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 2)"></div>
      <div id="3x2" style="border: 0; position: absolute; top: 50px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 2)"></div>
      <div id="4x2" style="border: 0; position: absolute; top: 50px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 2)"></div>
      <div id="5x2" style="border: 0; position: absolute; top: 50px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 2)"></div>
      <div id="6x2" style="border: 0; position: absolute; top: 50px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 2)"></div>
      <div id="7x2" style="border: 0; position: absolute; top: 50px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 2)"></div>
      <div id="8x2" style="border: 0; position: absolute; top: 50px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 2)"></div>
      <div id="9x2" style="border: 0; position: absolute; top: 50px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 2)"></div>
      <div id="10x2" style="border: 0; position: absolute; top: 50px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 2)"></div>
      <div id="11x2" style="border: 0; position: absolute; top: 50px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 2)"></div>
      <div id="12x2" style="border: 0; position: absolute; top: 50px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 2)"></div>
      <div id="13x2" style="border: 0; position: absolute; top: 50px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 2)"></div>
      <div id="14x2" style="border: 0; position: absolute; top: 50px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 2)"></div>
      <div id="15x2" style="border: 0; position: absolute; top: 50px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 2)"></div>
      <div id="16x2" style="border: 0; position: absolute; top: 50px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 2)"></div>
      <div id="17x2" style="border: 0; position: absolute; top: 50px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 2)"></div>
      <div id="18x2" style="border: 0; position: absolute; top: 50px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 2)"></div>
      <div id="19x2" style="border: 0; position: absolute; top: 50px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 2)"></div>
      <div id="20x2" style="border: 0; position: absolute; top: 50px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 2)"></div>
      <div id="21x2" style="border: 0; position: absolute; top: 50px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 2)"></div>
      <div id="22x2" style="border: 0; position: absolute; top: 50px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 2)"></div>
      <div id="23x2" style="border: 0; position: absolute; top: 50px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 2)"></div>
      <div id="24x2" style="border: 0; position: absolute; top: 50px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 2)"></div>
      <div id="0x3" style="border: 0; position: absolute; top: 75px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 3)"></div>
      <div id="1x3" style="border: 0; position: absolute; top: 75px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 3)"></div>
      <div id="2x3" style="border: 0; position: absolute; top: 75px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 3)"></div>
      <div id="3x3" style="border: 0; position: absolute; top: 75px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 3)"></div>
      <div id="4x3" style="border: 0; position: absolute; top: 75px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 3)"></div>
      <div id="5x3" style="border: 0; position: absolute; top: 75px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 3)"></div>
      <div id="6x3" style="border: 0; position: absolute; top: 75px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 3)"></div>
      <div id="7x3" style="border: 0; position: absolute; top: 75px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 3)"></div>
      <div id="8x3" style="border: 0; position: absolute; top: 75px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 3)"></div>
      <div id="9x3" style="border: 0; position: absolute; top: 75px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 3)"></div>
      <div id="10x3" style="border: 0; position: absolute; top: 75px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 3)"></div>
      <div id="11x3" style="border: 0; position: absolute; top: 75px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 3)"></div>
      <div id="12x3" style="border: 0; position: absolute; top: 75px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 3)"></div>
      <div id="13x3" style="border: 0; position: absolute; top: 75px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 3)"></div>
      <div id="14x3" style="border: 0; position: absolute; top: 75px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 3)"></div>
      <div id="15x3" style="border: 0; position: absolute; top: 75px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 3)"></div>
      <div id="16x3" style="border: 0; position: absolute; top: 75px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 3)"></div>
      <div id="17x3" style="border: 0; position: absolute; top: 75px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 3)"></div>
      <div id="18x3" style="border: 0; position: absolute; top: 75px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 3)"></div>
      <div id="19x3" style="border: 0; position: absolute; top: 75px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 3)"></div>
      <div id="20x3" style="border: 0; position: absolute; top: 75px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 3)"></div>
      <div id="21x3" style="border: 0; position: absolute; top: 75px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 3)"></div>
      <div id="22x3" style="border: 0; position: absolute; top: 75px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 3)"></div>
      <div id="23x3" style="border: 0; position: absolute; top: 75px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 3)"></div>
      <div id="24x3" style="border: 0; position: absolute; top: 75px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 3)"></div>
      <div id="0x4" style="border: 0; position: absolute; top: 100px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 4)"></div>
      <div id="1x4" style="border: 0; position: absolute; top: 100px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 4)"></div>
      <div id="2x4" style="border: 0; position: absolute; top: 100px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 4)"></div>
      <div id="3x4" style="border: 0; position: absolute; top: 100px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 4)"></div>
      <div id="4x4" style="border: 0; position: absolute; top: 100px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 4)"></div>
      <div id="5x4" style="border: 0; position: absolute; top: 100px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 4)"></div>
      <div id="6x4" style="border: 0; position: absolute; top: 100px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 4)"></div>
      <div id="7x4" style="border: 0; position: absolute; top: 100px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 4)"></div>
      <div id="8x4" style="border: 0; position: absolute; top: 100px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 4)"></div>
      <div id="9x4" style="border: 0; position: absolute; top: 100px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 4)"></div>
      <div id="10x4" style="border: 0; position: absolute; top: 100px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 4)"></div>
      <div id="11x4" style="border: 0; position: absolute; top: 100px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 4)"></div>
      <div id="12x4" style="border: 0; position: absolute; top: 100px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 4)"></div>
      <div id="13x4" style="border: 0; position: absolute; top: 100px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 4)"></div>
      <div id="14x4" style="border: 0; position: absolute; top: 100px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 4)"></div>
      <div id="15x4" style="border: 0; position: absolute; top: 100px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 4)"></div>
      <div id="16x4" style="border: 0; position: absolute; top: 100px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 4)"></div>
      <div id="17x4" style="border: 0; position: absolute; top: 100px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 4)"></div>
      <div id="18x4" style="border: 0; position: absolute; top: 100px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 4)"></div>
      <div id="19x4" style="border: 0; position: absolute; top: 100px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 4)"></div>
      <div id="20x4" style="border: 0; position: absolute; top: 100px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 4)"></div>
      <div id="21x4" style="border: 0; position: absolute; top: 100px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 4)"></div>
      <div id="22x4" style="border: 0; position: absolute; top: 100px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 4)"></div>
      <div id="23x4" style="border: 0; position: absolute; top: 100px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 4)"></div>
      <div id="24x4" style="border: 0; position: absolute; top: 100px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 4)"></div>
      <div id="0x5" style="border: 0; position: absolute; top: 125px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 5)"></div>
      <div id="1x5" style="border: 0; position: absolute; top: 125px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 5)"></div>
      <div id="2x5" style="border: 0; position: absolute; top: 125px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 5)"></div>
      <div id="3x5" style="border: 0; position: absolute; top: 125px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 5)"></div>
      <div id="4x5" style="border: 0; position: absolute; top: 125px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 5)"></div>
      <div id="5x5" style="border: 0; position: absolute; top: 125px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 5)"></div>
      <div id="6x5" style="border: 0; position: absolute; top: 125px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 5)"></div>
      <div id="7x5" style="border: 0; position: absolute; top: 125px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 5)"></div>
      <div id="8x5" style="border: 0; position: absolute; top: 125px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 5)"></div>
      <div id="9x5" style="border: 0; position: absolute; top: 125px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 5)"></div>
      <div id="10x5" style="border: 0; position: absolute; top: 125px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 5)"></div>
      <div id="11x5" style="border: 0; position: absolute; top: 125px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 5)"></div>
      <div id="12x5" style="border: 0; position: absolute; top: 125px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 5)"></div>
      <div id="13x5" style="border: 0; position: absolute; top: 125px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 5)"></div>
      <div id="14x5" style="border: 0; position: absolute; top: 125px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 5)"></div>
      <div id="15x5" style="border: 0; position: absolute; top: 125px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 5)"></div>
      <div id="16x5" style="border: 0; position: absolute; top: 125px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 5)"></div>
      <div id="17x5" style="border: 0; position: absolute; top: 125px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 5)"></div>
      <div id="18x5" style="border: 0; position: absolute; top: 125px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 5)"></div>
      <div id="19x5" style="border: 0; position: absolute; top: 125px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 5)"></div>
      <div id="20x5" style="border: 0; position: absolute; top: 125px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 5)"></div>
      <div id="21x5" style="border: 0; position: absolute; top: 125px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 5)"></div>
      <div id="22x5" style="border: 0; position: absolute; top: 125px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 5)"></div>
      <div id="23x5" style="border: 0; position: absolute; top: 125px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 5)"></div>
      <div id="24x5" style="border: 0; position: absolute; top: 125px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 5)"></div>
      <div id="0x6" style="border: 0; position: absolute; top: 150px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 6)"></div>
      <div id="1x6" style="border: 0; position: absolute; top: 150px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 6)"></div>
      <div id="2x6" style="border: 0; position: absolute; top: 150px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 6)"></div>
      <div id="3x6" style="border: 0; position: absolute; top: 150px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 6)"></div>
      <div id="4x6" style="border: 0; position: absolute; top: 150px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 6)"></div>
      <div id="5x6" style="border: 0; position: absolute; top: 150px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 6)"></div>
      <div id="6x6" style="border: 0; position: absolute; top: 150px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 6)"></div>
      <div id="7x6" style="border: 0; position: absolute; top: 150px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 6)"></div>
      <div id="8x6" style="border: 0; position: absolute; top: 150px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 6)"></div>
      <div id="9x6" style="border: 0; position: absolute; top: 150px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 6)"></div>
      <div id="10x6" style="border: 0; position: absolute; top: 150px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 6)"></div>
      <div id="11x6" style="border: 0; position: absolute; top: 150px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 6)"></div>
      <div id="12x6" style="border: 0; position: absolute; top: 150px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 6)"></div>
      <div id="13x6" style="border: 0; position: absolute; top: 150px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 6)"></div>
      <div id="14x6" style="border: 0; position: absolute; top: 150px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 6)"></div>
      <div id="15x6" style="border: 0; position: absolute; top: 150px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 6)"></div>
      <div id="16x6" style="border: 0; position: absolute; top: 150px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 6)"></div>
      <div id="17x6" style="border: 0; position: absolute; top: 150px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 6)"></div>
      <div id="18x6" style="border: 0; position: absolute; top: 150px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 6)"></div>
      <div id="19x6" style="border: 0; position: absolute; top: 150px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 6)"></div>
      <div id="20x6" style="border: 0; position: absolute; top: 150px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 6)"></div>
      <div id="21x6" style="border: 0; position: absolute; top: 150px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 6)"></div>
      <div id="22x6" style="border: 0; position: absolute; top: 150px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 6)"></div>
      <div id="23x6" style="border: 0; position: absolute; top: 150px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 6)"></div>
      <div id="24x6" style="border: 0; position: absolute; top: 150px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 6)"></div>
      <div id="0x7" style="border: 0; position: absolute; top: 175px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 7)"></div>
      <div id="1x7" style="border: 0; position: absolute; top: 175px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 7)"></div>
      <div id="2x7" style="border: 0; position: absolute; top: 175px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 7)"></div>
      <div id="3x7" style="border: 0; position: absolute; top: 175px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 7)"></div>
      <div id="4x7" style="border: 0; position: absolute; top: 175px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 7)"></div>
      <div id="5x7" style="border: 0; position: absolute; top: 175px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 7)"></div>
      <div id="6x7" style="border: 0; position: absolute; top: 175px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 7)"></div>
      <div id="7x7" style="border: 0; position: absolute; top: 175px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 7)"></div>
      <div id="8x7" style="border: 0; position: absolute; top: 175px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 7)"></div>
      <div id="9x7" style="border: 0; position: absolute; top: 175px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 7)"></div>
      <div id="10x7" style="border: 0; position: absolute; top: 175px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 7)"></div>
      <div id="11x7" style="border: 0; position: absolute; top: 175px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 7)"></div>
      <div id="12x7" style="border: 0; position: absolute; top: 175px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 7)"></div>
      <div id="13x7" style="border: 0; position: absolute; top: 175px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 7)"></div>
      <div id="14x7" style="border: 0; position: absolute; top: 175px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 7)"></div>
      <div id="15x7" style="border: 0; position: absolute; top: 175px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 7)"></div>
      <div id="16x7" style="border: 0; position: absolute; top: 175px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 7)"></div>
      <div id="17x7" style="border: 0; position: absolute; top: 175px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 7)"></div>
      <div id="18x7" style="border: 0; position: absolute; top: 175px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 7)"></div>
      <div id="19x7" style="border: 0; position: absolute; top: 175px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 7)"></div>
      <div id="20x7" style="border: 0; position: absolute; top: 175px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 7)"></div>
      <div id="21x7" style="border: 0; position: absolute; top: 175px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 7)"></div>
      <div id="22x7" style="border: 0; position: absolute; top: 175px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 7)"></div>
      <div id="23x7" style="border: 0; position: absolute; top: 175px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 7)"></div>
      <div id="24x7" style="border: 0; position: absolute; top: 175px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 7)"></div>
      <div id="0x8" style="border: 0; position: absolute; top: 200px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 8)"></div>
      <div id="1x8" style="border: 0; position: absolute; top: 200px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 8)"></div>
      <div id="2x8" style="border: 0; position: absolute; top: 200px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 8)"></div>
      <div id="3x8" style="border: 0; position: absolute; top: 200px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 8)"></div>
      <div id="4x8" style="border: 0; position: absolute; top: 200px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 8)"></div>
      <div id="5x8" style="border: 0; position: absolute; top: 200px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 8)"></div>
      <div id="6x8" style="border: 0; position: absolute; top: 200px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 8)"></div>
      <div id="7x8" style="border: 0; position: absolute; top: 200px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 8)"></div>
      <div id="8x8" style="border: 0; position: absolute; top: 200px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 8)"></div>
      <div id="9x8" style="border: 0; position: absolute; top: 200px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 8)"></div>
      <div id="10x8" style="border: 0; position: absolute; top: 200px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 8)"></div>
      <div id="11x8" style="border: 0; position: absolute; top: 200px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 8)"></div>
      <div id="12x8" style="border: 0; position: absolute; top: 200px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 8)"></div>
      <div id="13x8" style="border: 0; position: absolute; top: 200px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 8)"></div>
      <div id="14x8" style="border: 0; position: absolute; top: 200px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 8)"></div>
      <div id="15x8" style="border: 0; position: absolute; top: 200px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 8)"></div>
      <div id="16x8" style="border: 0; position: absolute; top: 200px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 8)"></div>
      <div id="17x8" style="border: 0; position: absolute; top: 200px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 8)"></div>
      <div id="18x8" style="border: 0; position: absolute; top: 200px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 8)"></div>
      <div id="19x8" style="border: 0; position: absolute; top: 200px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 8)"></div>
      <div id="20x8" style="border: 0; position: absolute; top: 200px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 8)"></div>
      <div id="21x8" style="border: 0; position: absolute; top: 200px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 8)"></div>
      <div id="22x8" style="border: 0; position: absolute; top: 200px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 8)"></div>
      <div id="23x8" style="border: 0; position: absolute; top: 200px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 8)"></div>
      <div id="24x8" style="border: 0; position: absolute; top: 200px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 8)"></div>
      <div id="0x9" style="border: 0; position: absolute; top: 225px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 9)"></div>
      <div id="1x9" style="border: 0; position: absolute; top: 225px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 9)"></div>
      <div id="2x9" style="border: 0; position: absolute; top: 225px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 9)"></div>
      <div id="3x9" style="border: 0; position: absolute; top: 225px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 9)"></div>
      <div id="4x9" style="border: 0; position: absolute; top: 225px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 9)"></div>
      <div id="5x9" style="border: 0; position: absolute; top: 225px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 9)"></div>
      <div id="6x9" style="border: 0; position: absolute; top: 225px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 9)"></div>
      <div id="7x9" style="border: 0; position: absolute; top: 225px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 9)"></div>
      <div id="8x9" style="border: 0; position: absolute; top: 225px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 9)"></div>
      <div id="9x9" style="border: 0; position: absolute; top: 225px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 9)"></div>
      <div id="10x9" style="border: 0; position: absolute; top: 225px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 9)"></div>
      <div id="11x9" style="border: 0; position: absolute; top: 225px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 9)"></div>
      <div id="12x9" style="border: 0; position: absolute; top: 225px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 9)"></div>
      <div id="13x9" style="border: 0; position: absolute; top: 225px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 9)"></div>
      <div id="14x9" style="border: 0; position: absolute; top: 225px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 9)"></div>
      <div id="15x9" style="border: 0; position: absolute; top: 225px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 9)"></div>
      <div id="16x9" style="border: 0; position: absolute; top: 225px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 9)"></div>
      <div id="17x9" style="border: 0; position: absolute; top: 225px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 9)"></div>
      <div id="18x9" style="border: 0; position: absolute; top: 225px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 9)"></div>
      <div id="19x9" style="border: 0; position: absolute; top: 225px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 9)"></div>
      <div id="20x9" style="border: 0; position: absolute; top: 225px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 9)"></div>
      <div id="21x9" style="border: 0; position: absolute; top: 225px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 9)"></div>
      <div id="22x9" style="border: 0; position: absolute; top: 225px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 9)"></div>
      <div id="23x9" style="border: 0; position: absolute; top: 225px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 9)"></div>
      <div id="24x9" style="border: 0; position: absolute; top: 225px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 9)"></div>
      <div id="0x10" style="border: 0; position: absolute; top: 250px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 10)"></div>
      <div id="1x10" style="border: 0; position: absolute; top: 250px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 10)"></div>
      <div id="2x10" style="border: 0; position: absolute; top: 250px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 10)"></div>
      <div id="3x10" style="border: 0; position: absolute; top: 250px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 10)"></div>
      <div id="4x10" style="border: 0; position: absolute; top: 250px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 10)"></div>
      <div id="5x10" style="border: 0; position: absolute; top: 250px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 10)"></div>
      <div id="6x10" style="border: 0; position: absolute; top: 250px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 10)"></div>
      <div id="7x10" style="border: 0; position: absolute; top: 250px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 10)"></div>
      <div id="8x10" style="border: 0; position: absolute; top: 250px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 10)"></div>
      <div id="9x10" style="border: 0; position: absolute; top: 250px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 10)"></div>
      <div id="10x10" style="border: 0; position: absolute; top: 250px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 10)"></div>
      <div id="11x10" style="border: 0; position: absolute; top: 250px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 10)"></div>
      <div id="12x10" style="border: 0; position: absolute; top: 250px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 10)"></div>
      <div id="13x10" style="border: 0; position: absolute; top: 250px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 10)"></div>
      <div id="14x10" style="border: 0; position: absolute; top: 250px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 10)"></div>
      <div id="15x10" style="border: 0; position: absolute; top: 250px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 10)"></div>
      <div id="16x10" style="border: 0; position: absolute; top: 250px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 10)"></div>
      <div id="17x10" style="border: 0; position: absolute; top: 250px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 10)"></div>
      <div id="18x10" style="border: 0; position: absolute; top: 250px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 10)"></div>
      <div id="19x10" style="border: 0; position: absolute; top: 250px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 10)"></div>
      <div id="20x10" style="border: 0; position: absolute; top: 250px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 10)"></div>
      <div id="21x10" style="border: 0; position: absolute; top: 250px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 10)"></div>
      <div id="22x10" style="border: 0; position: absolute; top: 250px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 10)"></div>
      <div id="23x10" style="border: 0; position: absolute; top: 250px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 10)"></div>
      <div id="24x10" style="border: 0; position: absolute; top: 250px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 10)"></div>
      <div id="0x11" style="border: 0; position: absolute; top: 275px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 11)"></div>
      <div id="1x11" style="border: 0; position: absolute; top: 275px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 11)"></div>
      <div id="2x11" style="border: 0; position: absolute; top: 275px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 11)"></div>
      <div id="3x11" style="border: 0; position: absolute; top: 275px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 11)"></div>
      <div id="4x11" style="border: 0; position: absolute; top: 275px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 11)"></div>
      <div id="5x11" style="border: 0; position: absolute; top: 275px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 11)"></div>
      <div id="6x11" style="border: 0; position: absolute; top: 275px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 11)"></div>
      <div id="7x11" style="border: 0; position: absolute; top: 275px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 11)"></div>
      <div id="8x11" style="border: 0; position: absolute; top: 275px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 11)"></div>
      <div id="9x11" style="border: 0; position: absolute; top: 275px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 11)"></div>
      <div id="10x11" style="border: 0; position: absolute; top: 275px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 11)"></div>
      <div id="11x11" style="border: 0; position: absolute; top: 275px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 11)"></div>
      <div id="12x11" style="border: 0; position: absolute; top: 275px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 11)"></div>
      <div id="13x11" style="border: 0; position: absolute; top: 275px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 11)"></div>
      <div id="14x11" style="border: 0; position: absolute; top: 275px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 11)"></div>
      <div id="15x11" style="border: 0; position: absolute; top: 275px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 11)"></div>
      <div id="16x11" style="border: 0; position: absolute; top: 275px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 11)"></div>
      <div id="17x11" style="border: 0; position: absolute; top: 275px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 11)"></div>
      <div id="18x11" style="border: 0; position: absolute; top: 275px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 11)"></div>
      <div id="19x11" style="border: 0; position: absolute; top: 275px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 11)"></div>
      <div id="20x11" style="border: 0; position: absolute; top: 275px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 11)"></div>
      <div id="21x11" style="border: 0; position: absolute; top: 275px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 11)"></div>
      <div id="22x11" style="border: 0; position: absolute; top: 275px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 11)"></div>
      <div id="23x11" style="border: 0; position: absolute; top: 275px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 11)"></div>
      <div id="24x11" style="border: 0; position: absolute; top: 275px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 11)"></div>
      <div id="0x12" style="border: 0; position: absolute; top: 300px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 12)"></div>
      <div id="1x12" style="border: 0; position: absolute; top: 300px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 12)"></div>
      <div id="2x12" style="border: 0; position: absolute; top: 300px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 12)"></div>
      <div id="3x12" style="border: 0; position: absolute; top: 300px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 12)"></div>
      <div id="4x12" style="border: 0; position: absolute; top: 300px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 12)"></div>
      <div id="5x12" style="border: 0; position: absolute; top: 300px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 12)"></div>
      <div id="6x12" style="border: 0; position: absolute; top: 300px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 12)"></div>
      <div id="7x12" style="border: 0; position: absolute; top: 300px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 12)"></div>
      <div id="8x12" style="border: 0; position: absolute; top: 300px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 12)"></div>
      <div id="9x12" style="border: 0; position: absolute; top: 300px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 12)"></div>
      <div id="10x12" style="border: 0; position: absolute; top: 300px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 12)"></div>
      <div id="11x12" style="border: 0; position: absolute; top: 300px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 12)"></div>
      <div id="12x12" style="border: 0; position: absolute; top: 300px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 12)"></div>
      <div id="13x12" style="border: 0; position: absolute; top: 300px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 12)"></div>
      <div id="14x12" style="border: 0; position: absolute; top: 300px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 12)"></div>
      <div id="15x12" style="border: 0; position: absolute; top: 300px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 12)"></div>
      <div id="16x12" style="border: 0; position: absolute; top: 300px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 12)"></div>
      <div id="17x12" style="border: 0; position: absolute; top: 300px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 12)"></div>
      <div id="18x12" style="border: 0; position: absolute; top: 300px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 12)"></div>
      <div id="19x12" style="border: 0; position: absolute; top: 300px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 12)"></div>
      <div id="20x12" style="border: 0; position: absolute; top: 300px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 12)"></div>
      <div id="21x12" style="border: 0; position: absolute; top: 300px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 12)"></div>
      <div id="22x12" style="border: 0; position: absolute; top: 300px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 12)"></div>
      <div id="23x12" style="border: 0; position: absolute; top: 300px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 12)"></div>
      <div id="24x12" style="border: 0; position: absolute; top: 300px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 12)"></div>
      <div id="0x13" style="border: 0; position: absolute; top: 325px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 13)"></div>
      <div id="1x13" style="border: 0; position: absolute; top: 325px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 13)"></div>
      <div id="2x13" style="border: 0; position: absolute; top: 325px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 13)"></div>
      <div id="3x13" style="border: 0; position: absolute; top: 325px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 13)"></div>
      <div id="4x13" style="border: 0; position: absolute; top: 325px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 13)"></div>
      <div id="5x13" style="border: 0; position: absolute; top: 325px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 13)"></div>
      <div id="6x13" style="border: 0; position: absolute; top: 325px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 13)"></div>
      <div id="7x13" style="border: 0; position: absolute; top: 325px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 13)"></div>
      <div id="8x13" style="border: 0; position: absolute; top: 325px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 13)"></div>
      <div id="9x13" style="border: 0; position: absolute; top: 325px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 13)"></div>
      <div id="10x13" style="border: 0; position: absolute; top: 325px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 13)"></div>
      <div id="11x13" style="border: 0; position: absolute; top: 325px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 13)"></div>
      <div id="12x13" style="border: 0; position: absolute; top: 325px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 13)"></div>
      <div id="13x13" style="border: 0; position: absolute; top: 325px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 13)"></div>
      <div id="14x13" style="border: 0; position: absolute; top: 325px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 13)"></div>
      <div id="15x13" style="border: 0; position: absolute; top: 325px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 13)"></div>
      <div id="16x13" style="border: 0; position: absolute; top: 325px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 13)"></div>
      <div id="17x13" style="border: 0; position: absolute; top: 325px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 13)"></div>
      <div id="18x13" style="border: 0; position: absolute; top: 325px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 13)"></div>
      <div id="19x13" style="border: 0; position: absolute; top: 325px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 13)"></div>
      <div id="20x13" style="border: 0; position: absolute; top: 325px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 13)"></div>
      <div id="21x13" style="border: 0; position: absolute; top: 325px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 13)"></div>
      <div id="22x13" style="border: 0; position: absolute; top: 325px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 13)"></div>
      <div id="23x13" style="border: 0; position: absolute; top: 325px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 13)"></div>
      <div id="24x13" style="border: 0; position: absolute; top: 325px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 13)"></div>
      <div id="0x14" style="border: 0; position: absolute; top: 350px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 14)"></div>
      <div id="1x14" style="border: 0; position: absolute; top: 350px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 14)"></div>
      <div id="2x14" style="border: 0; position: absolute; top: 350px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 14)"></div>
      <div id="3x14" style="border: 0; position: absolute; top: 350px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 14)"></div>
      <div id="4x14" style="border: 0; position: absolute; top: 350px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 14)"></div>
      <div id="5x14" style="border: 0; position: absolute; top: 350px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 14)"></div>
      <div id="6x14" style="border: 0; position: absolute; top: 350px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 14)"></div>
      <div id="7x14" style="border: 0; position: absolute; top: 350px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 14)"></div>
      <div id="8x14" style="border: 0; position: absolute; top: 350px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 14)"></div>
      <div id="9x14" style="border: 0; position: absolute; top: 350px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 14)"></div>
      <div id="10x14" style="border: 0; position: absolute; top: 350px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 14)"></div>
      <div id="11x14" style="border: 0; position: absolute; top: 350px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 14)"></div>
      <div id="12x14" style="border: 0; position: absolute; top: 350px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 14)"></div>
      <div id="13x14" style="border: 0; position: absolute; top: 350px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 14)"></div>
      <div id="14x14" style="border: 0; position: absolute; top: 350px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 14)"></div>
      <div id="15x14" style="border: 0; position: absolute; top: 350px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 14)"></div>
      <div id="16x14" style="border: 0; position: absolute; top: 350px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 14)"></div>
      <div id="17x14" style="border: 0; position: absolute; top: 350px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 14)"></div>
      <div id="18x14" style="border: 0; position: absolute; top: 350px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 14)"></div>
      <div id="19x14" style="border: 0; position: absolute; top: 350px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 14)"></div>
      <div id="20x14" style="border: 0; position: absolute; top: 350px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 14)"></div>
      <div id="21x14" style="border: 0; position: absolute; top: 350px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 14)"></div>
      <div id="22x14" style="border: 0; position: absolute; top: 350px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 14)"></div>
      <div id="23x14" style="border: 0; position: absolute; top: 350px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 14)"></div>
      <div id="24x14" style="border: 0; position: absolute; top: 350px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 14)"></div>
      <div id="0x15" style="border: 0; position: absolute; top: 375px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 15)"></div>
      <div id="1x15" style="border: 0; position: absolute; top: 375px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 15)"></div>
      <div id="2x15" style="border: 0; position: absolute; top: 375px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 15)"></div>
      <div id="3x15" style="border: 0; position: absolute; top: 375px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 15)"></div>
      <div id="4x15" style="border: 0; position: absolute; top: 375px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 15)"></div>
      <div id="5x15" style="border: 0; position: absolute; top: 375px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 15)"></div>
      <div id="6x15" style="border: 0; position: absolute; top: 375px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 15)"></div>
      <div id="7x15" style="border: 0; position: absolute; top: 375px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 15)"></div>
      <div id="8x15" style="border: 0; position: absolute; top: 375px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 15)"></div>
      <div id="9x15" style="border: 0; position: absolute; top: 375px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 15)"></div>
      <div id="10x15" style="border: 0; position: absolute; top: 375px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 15)"></div>
      <div id="11x15" style="border: 0; position: absolute; top: 375px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 15)"></div>
      <div id="12x15" style="border: 0; position: absolute; top: 375px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 15)"></div>
      <div id="13x15" style="border: 0; position: absolute; top: 375px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 15)"></div>
      <div id="14x15" style="border: 0; position: absolute; top: 375px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 15)"></div>
      <div id="15x15" style="border: 0; position: absolute; top: 375px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 15)"></div>
      <div id="16x15" style="border: 0; position: absolute; top: 375px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 15)"></div>
      <div id="17x15" style="border: 0; position: absolute; top: 375px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 15)"></div>
      <div id="18x15" style="border: 0; position: absolute; top: 375px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 15)"></div>
      <div id="19x15" style="border: 0; position: absolute; top: 375px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 15)"></div>
      <div id="20x15" style="border: 0; position: absolute; top: 375px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 15)"></div>
      <div id="21x15" style="border: 0; position: absolute; top: 375px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 15)"></div>
      <div id="22x15" style="border: 0; position: absolute; top: 375px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 15)"></div>
      <div id="23x15" style="border: 0; position: absolute; top: 375px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 15)"></div>
      <div id="24x15" style="border: 0; position: absolute; top: 375px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 15)"></div>
      <div id="0x16" style="border: 0; position: absolute; top: 400px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 16)"></div>
      <div id="1x16" style="border: 0; position: absolute; top: 400px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 16)"></div>
      <div id="2x16" style="border: 0; position: absolute; top: 400px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 16)"></div>
      <div id="3x16" style="border: 0; position: absolute; top: 400px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 16)"></div>
      <div id="4x16" style="border: 0; position: absolute; top: 400px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 16)"></div>
      <div id="5x16" style="border: 0; position: absolute; top: 400px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 16)"></div>
      <div id="6x16" style="border: 0; position: absolute; top: 400px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 16)"></div>
      <div id="7x16" style="border: 0; position: absolute; top: 400px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 16)"></div>
      <div id="8x16" style="border: 0; position: absolute; top: 400px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 16)"></div>
      <div id="9x16" style="border: 0; position: absolute; top: 400px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 16)"></div>
      <div id="10x16" style="border: 0; position: absolute; top: 400px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 16)"></div>
      <div id="11x16" style="border: 0; position: absolute; top: 400px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 16)"></div>
      <div id="12x16" style="border: 0; position: absolute; top: 400px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 16)"></div>
      <div id="13x16" style="border: 0; position: absolute; top: 400px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 16)"></div>
      <div id="14x16" style="border: 0; position: absolute; top: 400px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 16)"></div>
      <div id="15x16" style="border: 0; position: absolute; top: 400px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 16)"></div>
      <div id="16x16" style="border: 0; position: absolute; top: 400px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 16)"></div>
      <div id="17x16" style="border: 0; position: absolute; top: 400px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 16)"></div>
      <div id="18x16" style="border: 0; position: absolute; top: 400px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 16)"></div>
      <div id="19x16" style="border: 0; position: absolute; top: 400px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 16)"></div>
      <div id="20x16" style="border: 0; position: absolute; top: 400px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 16)"></div>
      <div id="21x16" style="border: 0; position: absolute; top: 400px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 16)"></div>
      <div id="22x16" style="border: 0; position: absolute; top: 400px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 16)"></div>
      <div id="23x16" style="border: 0; position: absolute; top: 400px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 16)"></div>
      <div id="24x16" style="border: 0; position: absolute; top: 400px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 16)"></div>
      <div id="0x17" style="border: 0; position: absolute; top: 425px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 17)"></div>
      <div id="1x17" style="border: 0; position: absolute; top: 425px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 17)"></div>
      <div id="2x17" style="border: 0; position: absolute; top: 425px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 17)"></div>
      <div id="3x17" style="border: 0; position: absolute; top: 425px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 17)"></div>
      <div id="4x17" style="border: 0; position: absolute; top: 425px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 17)"></div>
      <div id="5x17" style="border: 0; position: absolute; top: 425px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 17)"></div>
      <div id="6x17" style="border: 0; position: absolute; top: 425px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 17)"></div>
      <div id="7x17" style="border: 0; position: absolute; top: 425px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 17)"></div>
      <div id="8x17" style="border: 0; position: absolute; top: 425px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 17)"></div>
      <div id="9x17" style="border: 0; position: absolute; top: 425px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 17)"></div>
      <div id="10x17" style="border: 0; position: absolute; top: 425px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 17)"></div>
      <div id="11x17" style="border: 0; position: absolute; top: 425px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 17)"></div>
      <div id="12x17" style="border: 0; position: absolute; top: 425px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 17)"></div>
      <div id="13x17" style="border: 0; position: absolute; top: 425px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 17)"></div>
      <div id="14x17" style="border: 0; position: absolute; top: 425px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 17)"></div>
      <div id="15x17" style="border: 0; position: absolute; top: 425px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 17)"></div>
      <div id="16x17" style="border: 0; position: absolute; top: 425px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 17)"></div>
      <div id="17x17" style="border: 0; position: absolute; top: 425px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 17)"></div>
      <div id="18x17" style="border: 0; position: absolute; top: 425px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 17)"></div>
      <div id="19x17" style="border: 0; position: absolute; top: 425px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 17)"></div>
      <div id="20x17" style="border: 0; position: absolute; top: 425px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 17)"></div>
      <div id="21x17" style="border: 0; position: absolute; top: 425px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 17)"></div>
      <div id="22x17" style="border: 0; position: absolute; top: 425px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 17)"></div>
      <div id="23x17" style="border: 0; position: absolute; top: 425px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 17)"></div>
      <div id="24x17" style="border: 0; position: absolute; top: 425px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 17)"></div>
      <div id="0x18" style="border: 0; position: absolute; top: 450px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 18)"></div>
      <div id="1x18" style="border: 0; position: absolute; top: 450px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 18)"></div>
      <div id="2x18" style="border: 0; position: absolute; top: 450px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 18)"></div>
      <div id="3x18" style="border: 0; position: absolute; top: 450px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 18)"></div>
      <div id="4x18" style="border: 0; position: absolute; top: 450px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 18)"></div>
      <div id="5x18" style="border: 0; position: absolute; top: 450px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 18)"></div>
      <div id="6x18" style="border: 0; position: absolute; top: 450px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 18)"></div>
      <div id="7x18" style="border: 0; position: absolute; top: 450px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 18)"></div>
      <div id="8x18" style="border: 0; position: absolute; top: 450px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 18)"></div>
      <div id="9x18" style="border: 0; position: absolute; top: 450px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 18)"></div>
      <div id="10x18" style="border: 0; position: absolute; top: 450px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 18)"></div>
      <div id="11x18" style="border: 0; position: absolute; top: 450px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 18)"></div>
      <div id="12x18" style="border: 0; position: absolute; top: 450px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 18)"></div>
      <div id="13x18" style="border: 0; position: absolute; top: 450px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 18)"></div>
      <div id="14x18" style="border: 0; position: absolute; top: 450px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 18)"></div>
      <div id="15x18" style="border: 0; position: absolute; top: 450px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 18)"></div>
      <div id="16x18" style="border: 0; position: absolute; top: 450px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 18)"></div>
      <div id="17x18" style="border: 0; position: absolute; top: 450px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 18)"></div>
      <div id="18x18" style="border: 0; position: absolute; top: 450px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 18)"></div>
      <div id="19x18" style="border: 0; position: absolute; top: 450px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 18)"></div>
      <div id="20x18" style="border: 0; position: absolute; top: 450px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 18)"></div>
      <div id="21x18" style="border: 0; position: absolute; top: 450px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 18)"></div>
      <div id="22x18" style="border: 0; position: absolute; top: 450px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 18)"></div>
      <div id="23x18" style="border: 0; position: absolute; top: 450px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 18)"></div>
      <div id="24x18" style="border: 0; position: absolute; top: 450px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 18)"></div>
      <div id="0x19" style="border: 0; position: absolute; top: 475px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 19)"></div>
      <div id="1x19" style="border: 0; position: absolute; top: 475px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 19)"></div>
      <div id="2x19" style="border: 0; position: absolute; top: 475px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 19)"></div>
      <div id="3x19" style="border: 0; position: absolute; top: 475px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 19)"></div>
      <div id="4x19" style="border: 0; position: absolute; top: 475px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 19)"></div>
      <div id="5x19" style="border: 0; position: absolute; top: 475px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 19)"></div>
      <div id="6x19" style="border: 0; position: absolute; top: 475px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 19)"></div>
      <div id="7x19" style="border: 0; position: absolute; top: 475px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 19)"></div>
      <div id="8x19" style="border: 0; position: absolute; top: 475px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 19)"></div>
      <div id="9x19" style="border: 0; position: absolute; top: 475px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 19)"></div>
      <div id="10x19" style="border: 0; position: absolute; top: 475px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 19)"></div>
      <div id="11x19" style="border: 0; position: absolute; top: 475px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 19)"></div>
      <div id="12x19" style="border: 0; position: absolute; top: 475px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 19)"></div>
      <div id="13x19" style="border: 0; position: absolute; top: 475px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 19)"></div>
      <div id="14x19" style="border: 0; position: absolute; top: 475px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 19)"></div>
      <div id="15x19" style="border: 0; position: absolute; top: 475px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 19)"></div>
      <div id="16x19" style="border: 0; position: absolute; top: 475px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 19)"></div>
      <div id="17x19" style="border: 0; position: absolute; top: 475px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 19)"></div>
      <div id="18x19" style="border: 0; position: absolute; top: 475px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 19)"></div>
      <div id="19x19" style="border: 0; position: absolute; top: 475px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 19)"></div>
      <div id="20x19" style="border: 0; position: absolute; top: 475px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 19)"></div>
      <div id="21x19" style="border: 0; position: absolute; top: 475px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 19)"></div>
      <div id="22x19" style="border: 0; position: absolute; top: 475px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 19)"></div>
      <div id="23x19" style="border: 0; position: absolute; top: 475px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 19)"></div>
      <div id="24x19" style="border: 0; position: absolute; top: 475px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 19)"></div>
      <div id="0x20" style="border: 0; position: absolute; top: 500px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 20)"></div>
      <div id="1x20" style="border: 0; position: absolute; top: 500px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 20)"></div>
      <div id="2x20" style="border: 0; position: absolute; top: 500px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 20)"></div>
      <div id="3x20" style="border: 0; position: absolute; top: 500px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 20)"></div>
      <div id="4x20" style="border: 0; position: absolute; top: 500px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 20)"></div>
      <div id="5x20" style="border: 0; position: absolute; top: 500px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 20)"></div>
      <div id="6x20" style="border: 0; position: absolute; top: 500px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 20)"></div>
      <div id="7x20" style="border: 0; position: absolute; top: 500px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 20)"></div>
      <div id="8x20" style="border: 0; position: absolute; top: 500px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 20)"></div>
      <div id="9x20" style="border: 0; position: absolute; top: 500px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 20)"></div>
      <div id="10x20" style="border: 0; position: absolute; top: 500px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 20)"></div>
      <div id="11x20" style="border: 0; position: absolute; top: 500px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 20)"></div>
      <div id="12x20" style="border: 0; position: absolute; top: 500px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 20)"></div>
      <div id="13x20" style="border: 0; position: absolute; top: 500px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 20)"></div>
      <div id="14x20" style="border: 0; position: absolute; top: 500px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 20)"></div>
      <div id="15x20" style="border: 0; position: absolute; top: 500px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 20)"></div>
      <div id="16x20" style="border: 0; position: absolute; top: 500px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 20)"></div>
      <div id="17x20" style="border: 0; position: absolute; top: 500px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 20)"></div>
      <div id="18x20" style="border: 0; position: absolute; top: 500px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 20)"></div>
      <div id="19x20" style="border: 0; position: absolute; top: 500px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 20)"></div>
      <div id="20x20" style="border: 0; position: absolute; top: 500px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 20)"></div>
      <div id="21x20" style="border: 0; position: absolute; top: 500px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 20)"></div>
      <div id="22x20" style="border: 0; position: absolute; top: 500px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 20)"></div>
      <div id="23x20" style="border: 0; position: absolute; top: 500px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 20)"></div>
      <div id="24x20" style="border: 0; position: absolute; top: 500px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 20)"></div>
      <div id="0x21" style="border: 0; position: absolute; top: 525px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 21)"></div>
      <div id="1x21" style="border: 0; position: absolute; top: 525px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 21)"></div>
      <div id="2x21" style="border: 0; position: absolute; top: 525px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 21)"></div>
      <div id="3x21" style="border: 0; position: absolute; top: 525px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 21)"></div>
      <div id="4x21" style="border: 0; position: absolute; top: 525px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 21)"></div>
      <div id="5x21" style="border: 0; position: absolute; top: 525px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 21)"></div>
      <div id="6x21" style="border: 0; position: absolute; top: 525px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 21)"></div>
      <div id="7x21" style="border: 0; position: absolute; top: 525px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 21)"></div>
      <div id="8x21" style="border: 0; position: absolute; top: 525px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 21)"></div>
      <div id="9x21" style="border: 0; position: absolute; top: 525px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 21)"></div>
      <div id="10x21" style="border: 0; position: absolute; top: 525px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 21)"></div>
      <div id="11x21" style="border: 0; position: absolute; top: 525px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 21)"></div>
      <div id="12x21" style="border: 0; position: absolute; top: 525px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 21)"></div>
      <div id="13x21" style="border: 0; position: absolute; top: 525px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 21)"></div>
      <div id="14x21" style="border: 0; position: absolute; top: 525px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 21)"></div>
      <div id="15x21" style="border: 0; position: absolute; top: 525px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 21)"></div>
      <div id="16x21" style="border: 0; position: absolute; top: 525px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 21)"></div>
      <div id="17x21" style="border: 0; position: absolute; top: 525px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 21)"></div>
      <div id="18x21" style="border: 0; position: absolute; top: 525px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 21)"></div>
      <div id="19x21" style="border: 0; position: absolute; top: 525px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 21)"></div>
      <div id="20x21" style="border: 0; position: absolute; top: 525px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 21)"></div>
      <div id="21x21" style="border: 0; position: absolute; top: 525px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 21)"></div>
      <div id="22x21" style="border: 0; position: absolute; top: 525px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 21)"></div>
      <div id="23x21" style="border: 0; position: absolute; top: 525px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 21)"></div>
      <div id="24x21" style="border: 0; position: absolute; top: 525px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 21)"></div>
      <div id="0x22" style="border: 0; position: absolute; top: 550px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 22)"></div>
      <div id="1x22" style="border: 0; position: absolute; top: 550px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 22)"></div>
      <div id="2x22" style="border: 0; position: absolute; top: 550px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 22)"></div>
      <div id="3x22" style="border: 0; position: absolute; top: 550px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 22)"></div>
      <div id="4x22" style="border: 0; position: absolute; top: 550px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 22)"></div>
      <div id="5x22" style="border: 0; position: absolute; top: 550px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 22)"></div>
      <div id="6x22" style="border: 0; position: absolute; top: 550px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 22)"></div>
      <div id="7x22" style="border: 0; position: absolute; top: 550px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 22)"></div>
      <div id="8x22" style="border: 0; position: absolute; top: 550px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 22)"></div>
      <div id="9x22" style="border: 0; position: absolute; top: 550px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 22)"></div>
      <div id="10x22" style="border: 0; position: absolute; top: 550px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 22)"></div>
      <div id="11x22" style="border: 0; position: absolute; top: 550px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 22)"></div>
      <div id="12x22" style="border: 0; position: absolute; top: 550px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 22)"></div>
      <div id="13x22" style="border: 0; position: absolute; top: 550px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 22)"></div>
      <div id="14x22" style="border: 0; position: absolute; top: 550px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 22)"></div>
      <div id="15x22" style="border: 0; position: absolute; top: 550px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 22)"></div>
      <div id="16x22" style="border: 0; position: absolute; top: 550px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 22)"></div>
      <div id="17x22" style="border: 0; position: absolute; top: 550px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 22)"></div>
      <div id="18x22" style="border: 0; position: absolute; top: 550px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 22)"></div>
      <div id="19x22" style="border: 0; position: absolute; top: 550px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 22)"></div>
      <div id="20x22" style="border: 0; position: absolute; top: 550px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 22)"></div>
      <div id="21x22" style="border: 0; position: absolute; top: 550px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 22)"></div>
      <div id="22x22" style="border: 0; position: absolute; top: 550px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 22)"></div>
      <div id="23x22" style="border: 0; position: absolute; top: 550px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 22)"></div>
      <div id="24x22" style="border: 0; position: absolute; top: 550px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 22)"></div>
      <div id="0x23" style="border: 0; position: absolute; top: 575px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 23)"></div>
      <div id="1x23" style="border: 0; position: absolute; top: 575px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 23)"></div>
      <div id="2x23" style="border: 0; position: absolute; top: 575px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 23)"></div>
      <div id="3x23" style="border: 0; position: absolute; top: 575px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 23)"></div>
      <div id="4x23" style="border: 0; position: absolute; top: 575px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 23)"></div>
      <div id="5x23" style="border: 0; position: absolute; top: 575px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 23)"></div>
      <div id="6x23" style="border: 0; position: absolute; top: 575px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 23)"></div>
      <div id="7x23" style="border: 0; position: absolute; top: 575px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 23)"></div>
      <div id="8x23" style="border: 0; position: absolute; top: 575px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 23)"></div>
      <div id="9x23" style="border: 0; position: absolute; top: 575px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 23)"></div>
      <div id="10x23" style="border: 0; position: absolute; top: 575px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 23)"></div>
      <div id="11x23" style="border: 0; position: absolute; top: 575px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 23)"></div>
      <div id="12x23" style="border: 0; position: absolute; top: 575px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 23)"></div>
      <div id="13x23" style="border: 0; position: absolute; top: 575px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 23)"></div>
      <div id="14x23" style="border: 0; position: absolute; top: 575px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 23)"></div>
      <div id="15x23" style="border: 0; position: absolute; top: 575px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 23)"></div>
      <div id="16x23" style="border: 0; position: absolute; top: 575px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 23)"></div>
      <div id="17x23" style="border: 0; position: absolute; top: 575px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 23)"></div>
      <div id="18x23" style="border: 0; position: absolute; top: 575px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 23)"></div>
      <div id="19x23" style="border: 0; position: absolute; top: 575px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 23)"></div>
      <div id="20x23" style="border: 0; position: absolute; top: 575px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 23)"></div>
      <div id="21x23" style="border: 0; position: absolute; top: 575px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 23)"></div>
      <div id="22x23" style="border: 0; position: absolute; top: 575px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 23)"></div>
      <div id="23x23" style="border: 0; position: absolute; top: 575px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 23)"></div>
      <div id="24x23" style="border: 0; position: absolute; top: 575px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 23)"></div>
      <div id="0x24" style="border: 0; position: absolute; top: 600px; left: 0px; width: 25px; height: 25px;" onclick="usar(0, 24)"></div>
      <div id="1x24" style="border: 0; position: absolute; top: 600px; left: 25px; width: 25px; height: 25px;" onclick="usar(1, 24)"></div>
      <div id="2x24" style="border: 0; position: absolute; top: 600px; left: 50px; width: 25px; height: 25px;" onclick="usar(2, 24)"></div>
      <div id="3x24" style="border: 0; position: absolute; top: 600px; left: 75px; width: 25px; height: 25px;" onclick="usar(3, 24)"></div>
      <div id="4x24" style="border: 0; position: absolute; top: 600px; left: 100px; width: 25px; height: 25px;" onclick="usar(4, 24)"></div>
      <div id="5x24" style="border: 0; position: absolute; top: 600px; left: 125px; width: 25px; height: 25px;" onclick="usar(5, 24)"></div>
      <div id="6x24" style="border: 0; position: absolute; top: 600px; left: 150px; width: 25px; height: 25px;" onclick="usar(6, 24)"></div>
      <div id="7x24" style="border: 0; position: absolute; top: 600px; left: 175px; width: 25px; height: 25px;" onclick="usar(7, 24)"></div>
      <div id="8x24" style="border: 0; position: absolute; top: 600px; left: 200px; width: 25px; height: 25px;" onclick="usar(8, 24)"></div>
      <div id="9x24" style="border: 0; position: absolute; top: 600px; left: 225px; width: 25px; height: 25px;" onclick="usar(9, 24)"></div>
      <div id="10x24" style="border: 0; position: absolute; top: 600px; left: 250px; width: 25px; height: 25px;" onclick="usar(10, 24)"></div>
      <div id="11x24" style="border: 0; position: absolute; top: 600px; left: 275px; width: 25px; height: 25px;" onclick="usar(11, 24)"></div>
      <div id="12x24" style="border: 0; position: absolute; top: 600px; left: 300px; width: 25px; height: 25px;" onclick="usar(12, 24)"></div>
      <div id="13x24" style="border: 0; position: absolute; top: 600px; left: 325px; width: 25px; height: 25px;" onclick="usar(13, 24)"></div>
      <div id="14x24" style="border: 0; position: absolute; top: 600px; left: 350px; width: 25px; height: 25px;" onclick="usar(14, 24)"></div>
      <div id="15x24" style="border: 0; position: absolute; top: 600px; left: 375px; width: 25px; height: 25px;" onclick="usar(15, 24)"></div>
      <div id="16x24" style="border: 0; position: absolute; top: 600px; left: 400px; width: 25px; height: 25px;" onclick="usar(16, 24)"></div>
      <div id="17x24" style="border: 0; position: absolute; top: 600px; left: 425px; width: 25px; height: 25px;" onclick="usar(17, 24)"></div>
      <div id="18x24" style="border: 0; position: absolute; top: 600px; left: 450px; width: 25px; height: 25px;" onclick="usar(18, 24)"></div>
      <div id="19x24" style="border: 0; position: absolute; top: 600px; left: 475px; width: 25px; height: 25px;" onclick="usar(19, 24)"></div>
      <div id="20x24" style="border: 0; position: absolute; top: 600px; left: 500px; width: 25px; height: 25px;" onclick="usar(20, 24)"></div>
      <div id="21x24" style="border: 0; position: absolute; top: 600px; left: 525px; width: 25px; height: 25px;" onclick="usar(21, 24)"></div>
      <div id="22x24" style="border: 0; position: absolute; top: 600px; left: 550px; width: 25px; height: 25px;" onclick="usar(22, 24)"></div>
      <div id="23x24" style="border: 0; position: absolute; top: 600px; left: 575px; width: 25px; height: 25px;" onclick="usar(23, 24)"></div>
      <div id="24x24" style="border: 0; position: absolute; top: 600px; left: 600px; width: 25px; height: 25px;" onclick="usar(24, 24)"></div>

      <div id="fundo" style="width: 100%; height: 100%; display: none;">
          <div id="description_object" style="border: 3px solid black; position: absolute; top: 200px; left: 200px; width: 250px; height: 150px; background-color: white;">
          <div style="position: absolute; top: 0; left: 0; width: 250px; height: 30px; text-align: center;">
            <b id="nome_info">CARREGANDO...</b>
          </div>
          <div style="position: absolute; top: 30px; left: 0; width: 125px; height: 20px; text-align: left;">
            <b>Reino:</b>
          </div>
          <div style="position: absolute; top: 30px; left: 125px; width: 125px; height: 20px; text-align: center;">
            <b id="reino_duelista"></b>
          </div>
          <div style="position: absolute; top: 60px; left: 0; width: 125px; height: 30px; text-align: left;">
            <b>CLÃ:</b>
          </div>
          <div style="position: absolute; top: 60px; left: 125px; width: 125px; height: 30px; text-align: center;">
            <b id="clan_duelista"></b>
          </div>
          <div style="position: absolute; top: 90px; left: 0; width: 125px; height: 30px; text-align: left;">
            <b>Experiência:</b>
          </div>
          <div style="position: absolute; top: 90px; left: 125px; width: 125px; height: 20px; text-align: center;">
            <b id="xp_duelista"></b>
          </div>
          <div style="position: absolute; top: 110px; left: 0; width: 250px; height: 40px; text-align: center;">
            <button id="botao_duelar" class="btn btn-success btn-sm"><b class="oficial-font" style="color: purple;">DUELAR</b></button>
          </div>
        </div>
      </div>

    </div>
  </div>

  <aside role="complementary" class="col-md-3 col-md-push-3">
    <hr><a href="ZD/index.php"><img src="imgs/B_duelos.png" class="img-responsive" /></a><hr>
    <a href="deck.php"><img src="imgs/B_Mdeck.png" class="img-responsive" /></a><hr>
    <a href="inventario.php"><img src="imgs/B_Minventario.png" class="img-responsive" /></a><hr>
    <a href="iedeck.php"><img src="imgs/iedeck.png" class="img-responsive" /></a><hr>
    <a href="comercio.php"><img src="imgs/B_comercio.png" class="img-responsive" /></a><hr>
    <a href="ranking.php"><img src="imgs/B_ranking.png" class="img-responsive" /></a><hr>
    <a href="meu_clan.php"><img src="imgs/B_clan.png" class="img-responsive" /></a><hr>
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
    <script type="text/javascript">
    var quadrante = <?php echo $_SESSION['quadrante'];?>;
    var reino = <?php echo $quadrante;?>;
    var x = <?php echo $_SESSION['X'];?>;
    var y = <?php echo $_SESSION['Y'];?>;
    var char = <?php echo $db->char;?>;
    var nome = "<?php echo $db->nome;?>";
    var tempo = "<?php echo time();?>"
    var mapa;//guarda o mapa pra ser usando entre as funções
    var mapa_limpo; // importante para limpar todos os players do mapa
    var lendo_servidor = false;

    setFundo(quadrante); // setando a imagem de fundo de acordo com o quadrante
    mapear();
    window.setInterval('atualizar()', 500);
      function mapear() {
         $.ajax({
          dataType: "json",
          type: 'get',
          data: 'mapear=1',
          url:'mapa.php',
          success: function(retorno){
            if(retorno['erro'] !== undefined) window.location.href = "home.php?erromapa=1";
            else {
              mapa = retorno;
              mapa_limpo = JSON.parse(JSON.stringify(mapa)); // cópia por valor e não por referencia
              //limpando
              for(var i = 0;i < 25;i++) {
                for(var j = 0; j < 25;j++) {
                    mapa_limpo[i][j].lifetime = 0;
                }
              }
              //limpo
            }
          }
         });
      }
      function limpar_mapa() {mapa = JSON.parse(JSON.stringify(mapa_limpo));}
      function atualizar(lista = null) {
        if(lista === null) {
          if(lendo_servidor) return false; // verificando bloqueio
          lendo_servidor = true; // bloqueando leitura
         $.ajax({
          dataType: "json",
          type: 'get',
          data: 'atualizar=1',
          url:'mapa.php',
          success: function(retorno){
            if(retorno['erro'] !== undefined) window.location.href = "home.php?erromapa=1";
            if(retorno['redirecionar'] === 'duelo') window.location.href = "/ZD/duelo_mapa.php";
            if(retorno['desafio'] !== undefined) interface_desafio(retorno['desafio']);
            lendo_servidor = false; // desbloqueando leitura
            atualizar(retorno);
          }
         });
         //atualizando o tempo do servidor
         $.ajax({type: 'get', data: 'tempo=1', url:'mapa.php', success: function(retorno){tempo = parseInt(retorno)}});
        }
        else if(!travaMovimentacao()) { // se estiver se movendo não pode atualizar, pra evitar lags
            limpar_mapa(); // limpando antes de preencher
            for(var loop = 0; loop < lista[0];loop++) {
                mapa[lista[loop+1].y][lista[loop+1].x] = lista[loop+1];
            }
            renderizar();
          }
        }

      function renderizar() {
          for(var i = 0;i < 25;i++) {
            for(var j = 0; j < 25;j++) {
              if(mapa[i][j].tem != 0 && tempo <= mapa[i][j].lifetime) {
                $('#'+j+'x'+i).html('<img src="imgs/chars/char_'+mapa[i][j].char+'.png" class="img-responsive" />');
              }
              else
                $('#'+j+'x'+i).html('');
            }
          }
      }

      var usando_objeto = false;
      function usar(j, i, dados = null) {
        if(dados === null) { // se aconteceu um clique

          if(usando_objeto) removerCamada();
          if(mapa[i][j].tem != 0 && (j != x || i != y)) {
            $("#nome_duelista").text('CARREGANDO...');
            $("#reino_duelista").text('');
            $("#clan_duelista").text('');
            $("#xp_duelista").text('');
            $("#botao_duelar").click(function(){/*NADA*/});

            $("#fundo").css('display', 'block');
            $("#description_object").css('height', '6px');
            $("#description_object").animate({height: '150px'}, 200);
            usando_objeto = true;

            $.ajax({
              dataType: "json",
              type: 'get',
              data: 'info='+mapa[i][j].tem,
              url:'mapa.php',
                success: function(retorno){
                  if(retorno['erro'] !== undefined) removerCamada();
                  else usar(j,i,retorno);
                }
            });
          }
        }
        else { // se isso é um retorno
          $("#nome_info").text(dados.nome);
          $("#reino_duelista").text(dados.reino);
          $("#clan_duelista").text(dados.clan);
          $("#xp_duelista").text(dados.xp);
          if(distancia_valida(i,j)) {
            $("#botao_duelar").removeClass("disabled");
            $('#botao_duelar').prop('onclick',null).off('click'); // limpando funções anteriores
            $("#botao_duelar").click(function(){
              $.ajax({
              dataType: "json",
              type: 'get',
              data: 'desafiar='+j+','+i,
              url:'mapa.php',
                success: function(retorno){
                  if(retorno['erro'] !== undefined) return false;
                  else {
                    var info = arra(2);
                    info['desafiante'] = nome;
                    info['desafiado'] = dados.nome;
                    interface_desafio(info);
                    return true;
                  }
                }
              });
              removerCamada();
            });
          }
          else {
            $('#botao_duelar').prop('onclick',null).off('click');
            $("#botao_duelar").click(function(){/*NADA*/});
            $("#botao_duelar").addClass("disabled");
          }
        }
      }

      function distancia_valida(i, j) {
        var dy = y - i;
        var dx = x - j;
        if(dy < 0) dy *= (-1);
        if(dx < 0) dx *= (-1);

        if(dy <= 1 && dx <=1) return true;
        else return false;
      }

      var exibindo_desafio = false;
      var intervalo_desafio = null;
      function interface_desafio(dados) {
        // essa função trava a tela e aguarda o desafio mudar de status
        if(!exibindo_desafio) { // se não estiver exibindo o desafio
          $("#tela_loading").css('display', 'block');
          if(nome == dados['desafiante']) {$("#tela_desafiante").css("display", "block");$("#tela_desafiado").css("display", "none");}
          else                            {$("#tela_desafiante").css("display", "none");$("#tela_desafiado").css("display", "block");}
          $("#nome_duelista").text(dados['desafiante']);
          $("#nome_oponente").text(dados['desafiado']);
          $("#nome_duelista_d").text(dados['desafiante']);
          $("#nome_oponente_d").text(dados['desafiado']);
          intervalo_desafio = setInterval("status_desafio()", 1000);
          exibindo_desafio = true;
        }
      }

      function status_desafio() {
            $.ajax({
              dataType: "json",
              type: 'get',
              data: 'status_desafio=1',
              url:'mapa.php',
                success: function(retorno){
                  if(retorno['status'] === undefined) window.location.href = "home.php?erromapa=2";
                  else {
                    if(retorno['status'] === 'n') {
                      $("#tela_loading").css('display', 'none');
                      exibindo_desafio = false;
                      clearInterval(intervalo_desafio);
                    }
                  }
                }
              });
      }

      function removerCamada() {
        $("#description_object").css('height', '150px');
        $("#description_object").animate({height: '6px'}, 200, function() {$("#fundo").css('display', 'none');usando_objeto = false;});
      }

      function setFundo(q) {
         for(var i = 0;i < 25;i++) {
            for(var j = 0; j < 25;j++) {
                $('#'+j+'x'+i).html(''); // esvaziando totalmente o mapa
            }
          }
        switch (q) {
          case 1:
            $('#mapa').css('background-image', 'url(imgs/RPG/mundo_1_1.jpg)');
          break;
          case 2:
            $('#mapa').css('background-image', 'url(imgs/RPG/mundo_1_2.jpg)');
          break;
          case 3:
            $('#mapa').css('background-image', 'url(imgs/RPG/mundo_1_3.jpg)');
          break;
          case 4:
            $('#mapa').css('background-image', 'url(imgs/RPG/mundo_1_4.jpg)');
          break;
        }
        mapear();//mapear o novo quadrante
      }
  // impedir relagem da tela com as setas
  window.onkeydown = function(e) {
    var key = e.keyCode ? e.keyCode : e.which; // pegando o código
    switch(key) {
      case 38: // seta pra cima
        e.preventDefault(); // impedir rolagem da tela
      break;
      case 37: // seta pra esquerda
        e.preventDefault(); // impedir rolagem da tela
      break;
      case 39: // seda pra direita
       e.preventDefault(); // impedir rolagem da tela
      break;
      case 40: // seta pra baixo
       e.preventDefault(); // impedir rolagem da tela
      break;
    }
  }

  window.onkeyup = function(e) {
    var key = e.keyCode ? e.keyCode : e.which; // pegando o código
    e.preventDefault(); // impedir rolagem da tela
    switch(key) {
      case 38: // seta pra cima
        mover(1);
      break;
      case 37: // seta pra esquerda
        mover(2);
      break;
      case 39: // seda pra direita
       mover(3);
      break;
      case 40: // seta pra baixo
       mover(4);
      break;
    }
  }
  // funçãod e movimentação
  travarMover = 0; // impede que o duelista se movimente demais causando lag
  function mover(p) {
    //checando se é um movimento válido
    var j = x;
    var i = y;
    switch(p) {
      case 1: // seta pra cima
        i -= 1;
      break;
      case 2: // seta pra esquerda
        j -= 1;
      break;
      case 3: // seda pra direita
       j += 1;
      break;
      case 4: // seta pra baixo
       i += 1;
      break;
    }
    var d = new Date();
    if(travaMovimentacao()) return false; // aguarde 5 segundos ou até se mover
    travarMover = d.getTime(); //travando movimentação
    // decidindo se vai ou não adiantar o movimento
    if((i < 25 && j < 25 && i >= 0 && j >= 0) && // se está dentro do quadrante
      (mapa[i][j].tem == 0 || (d.getTime()/1000) > mapa[i][j].lifetime) && // se tem outro jogador
      mapa[i][j].eh == 0) // se é um lugar bloqueado
      {
          $('#'+j+'x'+i).html('<img src="imgs/chars/char_'+char+'.png" class="img-responsive" />');
          mapa[y][x].tem = 0;
          $('#'+x+'x'+y).html('');
      }
    $.ajax({
      dataType: "json",
      type: 'get',
      data: 'mover='+p,
      url:'mapa.php',
      success: function(retorno) {
        if(retorno['erro'] !== undefined) return false;
        if(retorno['quadrante'] !== quadrante) setFundo(retorno['quadrante']);
        quadrante = retorno['quadrante'];
        x = retorno['x'];
        y = retorno['y'];

          //checando status da posição
          if(mapa[y][x].zona === undefined || mapa[y][x].zona == 4 || mapa[y][x].zona == 0)
            $('#mapa').css('border', '0');
          else if(mapa[y][x].zona != reino)
            $('#mapa').css('border', '1px solid red');
          else
            $('#mapa').css('border', '1px solid blue');

        travarMover = 0; //desbloqueando movimentação
      }
    });
  }
  function travaMovimentacao() {
    var d = new Date();
    if((d.getTime() - travarMover)/1000 < 5) return true; // está travado
    return false; // não está travado
  }
    </script>
<?php echo file_get_contents('EOP.txt');?>
  </body>
</html>
<?php

function infos($nome) {
  global $tools, $db;
  if(!$tools->verificarstr($nome)) {echo '{"erro": "erro"}';return false;}

  $id = $db->nome_id($nome);
  if($id === false) {echo '{"erro": "erro"}';return false;}

  $db->ler($id);

  $resposta = array();
  $resposta['nome'] = $db->nome;
  $resposta['xp'] = $db->xp;
  $resposta['reino'] = $db->nome_reino();
  if($db->status == 0) $resposta['clan'] = 'Nenhum';
  else $resposta['clan'] = $db->clan;

  echo json_encode($resposta);
  return true;
}

function mover($p) {
  if($p < 1 || $p > 4 || !isset($_SESSION['quadrante'])) {
    echo '{"erro": "mover"}';
    return false;
  }
  global $mapa, $db;
  $retorno = null;
  switch ($p) {
    case 1: // pra cima
      $retorno = $mapa->mover($db->nome, $_SESSION['X'], $_SESSION['Y']-1);
    break;
    case 2: // pra esquerda
      $retorno = $mapa->mover($db->nome, $_SESSION['X']-1, $_SESSION['Y']);
    break;
    case 3: // pra direita
      $retorno = $mapa->mover($db->nome, $_SESSION['X']+1, $_SESSION['Y']);
    break;
    case 4: // pra baixo
      $retorno = $mapa->mover($db->nome, $_SESSION['X'], $_SESSION['Y']+1);
    break;
  }
  if($retorno === false) {
    $retorno['quadrante'] = $_SESSION['quadrante'];
    $retorno['x'] = $_SESSION['X'];
    $retorno['y'] = $_SESSION['Y'];
    echo json_encode($retorno);
    return true;
  }
  $_SESSION['quadrante'] = $retorno['quadrante'];
  $_SESSION['X'] = $retorno['x'];
  $_SESSION['Y'] = $retorno['y'];
  echo json_encode($retorno);
  return true;
}

function atualizar($quadrante) {
  $lista = array();
  if($_SESSION['desafio'] === 's') {
    echo '{"redirecionar": "duelo"}';
    return false;
  }
  global $mapa, $db;
  switch ((int)$quadrante) {
    case 1:
      $lista  = $mapa->atualizar(0,0, 25, 25); // atualizar primeiro quadrante
    break;
    case 2:
      $lista  = $mapa->atualizar(25,0, 50, 25); // atualizar primeiro quadrante
    break;
    case 3:
      $lista  = $mapa->atualizar(0,25, 25, 50); // atualizar primeiro quadrante
    break;
    case 4:
      $lista  = $mapa->atualizar(25,25, 50, 50); // atualizar primeiro quadrante
    break;
    
    default:
      exit();
      break;
  }
  if(!$mapa->keepAlive($db->nome)) {echo '{"erro": "keepalive"}'; exit();}

  $banco = new DB;// banco temporario
  for($loop = 0; $loop < $lista[0]; $loop++) { // percorrento toda a lista
    $banco->ler($banco->nome_id($lista[$loop+1]['tem']));
    $lista[$loop+1]['char'] = $banco->char; // colocando o char do jogador
  }

  $desafio = new Desafio;
  if($desafio->existe($db->nome)) {
    $vetor['desafiante'] = $desafio->desafiante;
    $vetor['desafiado'] = $desafio->desafiado;
    $lista['desafio'] = $vetor;
    $_SESSION['desafio'] = $desafio->status;
  } else $_SESSION['desafio'] = 'n';
  echo json_encode($lista);
}

function mapear($quadrante) {
  $matriz = array();
  global $mapa, $db;
  switch ((int)$quadrante) {
    case 1:
      $matriz  = $mapa->mapear(0,0, 25, 25); // mapear primeiro quadrante
    break;
    case 2:
      $matriz  = $mapa->mapear(25,0, 50, 25); // mapear primeiro quadrante
    break;
    case 3:
      $matriz  = $mapa->mapear(0,25, 25, 50); // mapear primeiro quadrante
    break;
    case 4:
      $matriz  = $mapa->mapear(25,25, 50, 50); // mapear primeiro quadrante
    break;
    
    default:
      exit();
      break;
  }
  echo json_encode($matriz);
}

function spawnar($quadrante) {
  global $mapa;
  global $db;
  $retorno = $mapa->spawnar($quadrante, $db->nome);
  if($retorno === false) {header("location: home.php?mapal=1");exit();}

  $_SESSION['quadrante'] = $retorno['quadrante'];
  $_SESSION['X'] = $retorno['x'];
  $_SESSION['Y'] =  $retorno['y'];
}

function desafiar($x, $y) {
  // calculando a distancia entre eles
  session_start();
  global $db;
  if(!isset($_SESSION['quadrante'])) {echo '{"erro": "spaw"}'; return false;}
  $dy = $y - $_SESSION['Y'];
  $dx = $x - $_SESSION['X'];
  if($dy < 0) $dy *= (-1);
  if($dx < 0) $dx *= (-1);
  if($dx > 1 || $dy > 1) {echo '{"erro": "distancia"}'; return false;}
  //checagem feita, a distancia entre ele e o alvo é válida
    $matriz = array();
  global $mapa, $db;
  switch ((int)$_SESSION['quadrante']) {
    case 1:
      $matriz  = $mapa->mapear(0,0, 25, 25); // mapear primeiro quadrante
    break;
    case 2:
      $matriz  = $mapa->mapear(25,0, 50, 25); // mapear primeiro quadrante
    break;
    case 3:
      $matriz  = $mapa->mapear(0,25, 25, 50); // mapear primeiro quadrante
    break;
    case 4:
      $matriz  = $mapa->mapear(25,25, 50, 50); // mapear primeiro quadrante
    break;
    
    default:
      echo '{"erro": "quadrante"}'; 
      exit();
      return false;
  }
  // checando o local alvo
  $local = $matriz[$y][$x];
  $duelistas_local = $matriz[$_SESSION['Y']][$_SESSION['X']];
  if($local['tem'] === 0 || $local['tem'] === $db->nome || $local['lifetime'] <= time()) {
    echo '{"erro": "local"}';
    return false;
  }
  // chegando aqui a distância do desaficante está OK e o alvo do desafio tbm está OK
  //testando disponibilidade do alvo
  $desafio = new Desafio;
  if($desafio->ocupado($local['tem'])) {
    echo '{"erro": "ocupado"}'; 
    return false;
  }
  $oponente = new DB;
  $oponente->ler($oponente->nome_id($local['tem']));
  // se chegou aqui é hora de iniciar o desafio, mas antes descobrir qual a situação dos duelistas
  $reino_d = (int)$db->reino;
  $campo_d = (int)$duelistas_local['zona'];
  $reino_o = (int)$oponente->reino;
  $campo_o = (int)$local['zona'];
  if(duelar($reino_d, $campo_d, $reino_o, $campo_o)) {
    $desafio->desafiante = $db->nome;
    $desafio->desafiado = $local['tem'];
    $desafio->honra = 1;// honrra ativada
    if($desafio->criar()) { // um desafio foi criado
      $_SESSION['desafio'] = 'P'; // desafio pendente]
      $desafio->aceitar(); // aceitandom imediatamentes
    }
  }
  else {
    $desafio->desafiante = $db->nome;
    $desafio->desafiado = $local['tem'];
    if($campo_d === $campo_o && ($reino_d === $campo_d || $reino_o === $campo_o) && $reino_o !== $reino_d)
      $desafio->honra = 1; // duelo de honra
    else $desafio->honra = 0;// não é um duelo de honra
    if($desafio->criar()) // um desafio foi criado
      $_SESSION['desafio'] = 'P'; // desafio pendente
  }
}

 function duelar($rd, $cd, $ro, $co) {
  if($cd === 0 || $co === 0 || $rd === 0 || $ro === 0) return false;

  if($cd == $co && $rd == $cd && $rd != $ro) return true;
  else return false;
 }

function status_desafio() {
  global $db;
  $desafio = new Desafio;
  $vetor['status'] = 'n';
  if($desafio->existe($db->nome)) {
    $vetor['status'] = $desafio->status;
  }
  echo json_encode($vetor);
}

function aceitar_desafio() {
  global $db;
  $desafio = new Desafio;
  if(!$desafio->existe($db->nome)) return false;
  if($desafio->desafiado === $db->nome) {
    $desafio->aceitar();
  } else return false;
  return true;
}
function cancelar_desafio() {
  global $db;
  $desafio = new Desafio;
  if(!$desafio->existe($db->nome)) return false;
  $desafio->remover();
  return true;
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