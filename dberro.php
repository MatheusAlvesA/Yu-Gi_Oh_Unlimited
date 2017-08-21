<?php
require 'libs/tools_lib.php';
require_once 'libs/Mobile_Detect.php';

$obj = new Tools();
$obj->verificar();

//verificar se o usuário está logado
session_start();
if($_SESSION["logado"] == 'S') {header("location: home.php");}
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Yu-Gi-Oh Unlimited é um jogo totalmente online sem necessidade de download onde você pode formar seu deck do jeito que quiser totalmente de graça para em seguida duelar contra outros duelistas online no Brasil todo em um sistema de duelo automático. O jogo atualmente está em fase Alpha(Em teste).">
    <meta name="robots" content="index">
	<meta name="keywords" content="yugi,yugioh,jogar yugioh,yugioh grátis,grátis,yu-gi-oh,yugioh online,yugioh unlimited,yu-gi-oh game,yugioh jogo,yugioh jogo online,forbidden memories,yu-gi-oh online,yugioh rpg,duelo,duelar online"/>
    <link rel="icon" href="imgs/favicon.png">

    <title>Yu-Gi-Oh Unlimited</title>

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fonte.css" type="text/css" media="screen"/>
    <link href="style.css" rel="stylesheet"> <!--Estilos personalizados-->
    <?php 
    	$detectar = new Mobile_Detect;
    	if(!$detectar->isMobile() || $detectar->isTablet()) echo '<link href="style_PC.css" rel="stylesheet"> <!--Estilos exclusivos quando aberto no computador-->';
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
            <li><a href="index.php">Principal</a></li>
            <li><a href="tutorial.php">Aprenda a jogar</a></li>
            <li class="active"><a href="#">Sobre</a></li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">

<aside role="complementary" class="col-md-3">
	<form action="log.php" method="post" role="form">
		<h2 style="text-align: center; color: #535b82;">Faça Login</h2><hr>
  	<div class="form-group">
	    <label for="textNome" class="control-label">Nome</label>
    	<input id="textNome" class="form-control" placeholder="Digite seu Nome..." type="text" name="nome">
  	</div>
  
  	<div class="form-group">
	    <label for="inputPassword" class="control-label">Senha</label>
    	<input type="password" class="form-control" id="inputPassword" placeholder="Digite sua Senha..." name="senha">
  	</div>

  	<button type="submit" class="btn btn-primary">Logar</button>

	</form>    
</aside>

<div role="main" class="col-md-9">
	<h1 style="text-align: center; color: red;">Erro ao acessar o banco de dados</h1><hr>
  <h2 style="text-align: center;">Por favor tente novamente</h2>
</div>

</div>

<hr>
<footer class="row">
	<div class="col-md-4 col-md-push-4">
		<?php include 'rodape.txt';?>
	</div>
</footer>

    <script src="bootstrap/jquery-3.1.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>