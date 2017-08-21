<?php
include("libs/tools_lib.php");
include("libs/Mobile_Detect.php");

$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

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
            <li><a href="desenvolvimento.php">Desenvolvimento</a></li>
            <li class="active"><a href="#">Sobre</a></li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">
<div class="container col-md-8 col-md-push-4">
	<h1 style="text-align: center;">Fase Beta</h1><hr>
	<p style="font-size: 15pt; text-align: justify;">O jogo está atualmente em sua fase beta(Ainda em desenvolvimento). Várias funções ainda não estão disponíveis como a recuperação da senha do usuário em caso de perda. É importante curtir a <a href="https://www.facebook.com/yugiohunlimited">página oficial no Facebook</a> pois é onde eu aviso sobre novas atualizações e correções de bugs.
	</p>
	<b style="text-align: right;">Matheus Alves</b>
	<br><br>
	<a href="home.php" style="text-align: center;"><button class="btn btn-primary">COMEÇAR</button></a>
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
<?php echo file_get_contents('EOP.txt');?>
  </body>
</html>