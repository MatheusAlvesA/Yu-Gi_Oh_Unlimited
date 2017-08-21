<?php
require_once 'libs/tools_lib.php';
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
            <li><a href="desenvolvimento.php">Desenvolvimento</a></li>
            <li class="active"><a href="#">Sobre</a></li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">

<aside role="complementary" class="col-md-3">
	<form action="log.php?referencia=sobre" method="post" role="form">
		<h2 style="text-align: center; color: #535b82;">Faça Login</h2><hr>
  	<div class="form-group">
  	<?php echo errologin();?>
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

<div role="main" class="col-md-6">
	<h1 style="text-align: center;">Sobre o Jogo</h1><hr>
	<p style="font-size: 15pt; text-align: justify;">
Yu-Gi-Oh Unlimited é um jogo desenvolvido e baseado no trading card game Yugioh, atualmente mantido pela Konami Corporation.<br><br>
No jogo, após se cadastrar, você pode começar a formar seu deck dentre mais de 800 cartas, em seguida entrar no chat com os outros duelistas para então começar um duelo com algum deles.<br><br>
O sistema de duelo é totalmente automático o que garante que não ocorrerá trapaças durante as partidas. O que diferencia esse site dos outros jogos de Yugioh online é o fato de tudo isso ser totalmente gratuito.<br><br>
Este site foi desenvolvido inteiramente por um único desenvolvedor Matheus Alves, um detalhe desnecessário porém interessante é que sua fase alpha foi totalmente desenvolvida apenas usando um Smartphone Android, sem auxílio de qualquer computador.<br><br>
	</p>
	<b style="text-align: right;">Matheus Alves(O criador dessa bagunça que eu chamo de jogo)</b>

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
<?php
 function errologin() {
  $retorno = '';
  if($_GET["erro"] == '') {return '';} // caso nao aja erro
  if($_GET["erro"] == 13) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> Todos os campos devem ser reenchidos
	</div>";
  }
  if($_GET["erro"] == 14) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> Nome de usuário ou senha inválido
	</div>";
  }
  return $retorno;
 }
?>