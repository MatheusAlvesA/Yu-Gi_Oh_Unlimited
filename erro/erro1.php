<?php
$arq = fopen("../SiteStatus.txt", 'r');
$x = fgets($arq);
fclose($arq);
if($x == 0) {header("location: ../index.php");exit();}
if($x == 2) {header("location: erro2.php");exit();}
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Yu-Gi-Oh Unlimited é um jogo totalmente online sem necessidade de download onde você pode formar seu deck do jeito que quiser totalmente de graça para em seguida duelar contra outros duelistas online no Brasil todo em um sistema de duelo automático. O jogo atualmente está em fase Alpha(Em teste).">
    <meta name="robots" content="index">
	<meta name="keywords" content="yugi,yugioh,jogar yugioh,yugioh grátis,grátis,yu-gi-oh,yugioh online,yugioh unlimited,yu-gi-oh game,yugioh jogo,yugioh jogo online,forbidden memories,yu-gi-oh online,yugioh rpg,duelo,duelar online"/>
    <link rel="icon" href="../imgs/favicon.png">

    <title>ERRO Yu-Gi-Oh Unlimited</title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../fonte.css" type="text/css" media="screen"/>
    <link href="../style.css" rel="stylesheet"> <!--Estilos personalizados-->

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
            <li><a href="../index.php">Principal</a></li>
            <li><a href="../tutorial.php">Aprenda a jogar</a></li>
            <li><a href="../sobre.php">Sobre</a></li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">

<div role="main" class="col-md-8 col-md-push-2">
	<h1 style="text-align: center;">ERRO FATAL</h1><hr>
	<h2>O algoritmo de detecção de erros encontrou um problema no sistema do site... O administrador já foi notificado.</h2>
    <h3>* É uma ótima oportunidade para assistir alguns episódios de Death Note.</h3>

</div>

</div>

<hr>
<footer class="row">
	<div class="col-md-4 col-md-push-4">
		<?php include '../rodape.txt';?>
	</div>
</footer>

    <script src="bootstrap/jquery-3.1.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>