<?php
require 'libs/tools_lib.php';
require_once 'libs/Mobile_Detect.php';

$obj = new Tools();
$obj->verificar();
//contador de acessos
if(!file_exists('acessos.txt')) {fclose(fopen('acessos.txt', 'w'));}
$acessos = file_get_contents('acessos.txt');
file_put_contents('acessos.txt', $acessos+1);
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

    <div role="main" class="col-md-9" style="background-color: white;">
	<h1 style="text-align: center;">Termos de uso</h1><hr>
<p style="font-size: 12pt;">
<b>Termos de uso:</b><br>
Ao cadastrar-se neste site você concorda com todos os termos de uso citados nesta página.<br>
<b>Propriedade da conta:</b>
<br>
O portador do endereço de email registrado em uma conta é considerado o único e exclusivo dono dessa mesma.
<br>
É de fundamental importância que como titular de uma conta você não transfira ou informe sua senha para ninguém. Você é o único responsável por portar sua senha. Em momento algum a administração irá requisitar sua senha seja por email ou qualquer outro meio.
<br>
Uma vez que uma conta quebre as regras do jogo a mesma está sujeita a banimento da conta ou do ip associado a ela sem aviso prévio, isso será feito mesmo se o dono da conta alegar que foi hackeado uma vez que como citado você é o único responsável por sua senha.<br>
<b>Regras:</b>
<br>
Os duelos devem ser disputados por dois jogadores de acordo com as regras originais do fã game Yu-Gi-Oh.
<br>
Qualquer tentativa de obter vantagens ilegalmente como usar scripts, hacks ou duelos armados é estritamente contra as regras do jogo e estará sujeito a punição.
<br>
<b>Regras de conduta:</b>
<br>
É proibido ofender de qualquer forma outro jogador ou mesmo a administração do jogo, críticas construtivas serão sempre bem vindas, mas palavreado sujo ou baixo é terminantemente proibido.
<br>
Usar o jogo para transmitir span ou qualquer outro tipo de propaganda é proibido e está sujeita a punição assim como também usar o chat do jogo pra obter vantagens se passando por GM ou administrador.<br>
A Konami Corporation mantém uma lista de cartas banidas e limitadas além de um conjunto de regras oficiais, esse jogo busca ser o mais próximo a essas regras possível, entretanto, não existe nenhuma garantia que as mesmas serão inteiramente seguidas, isso fica a critério do administrador.<br>
<b>Conclusão:</b><br>
A quebra de qualquer regra citada aqui acarretará banimento, cabe a administração decidir e realizar a punição adequada.
<br />
As regras aqui citadas são determinadas pelo administrador/dono do jogo, cabe a ele modificar estas com ou sem aviso prévio. Além disso se qualquer jogador for pego realizando uma ação de má índole este poderá ser banido pelo administrador mesmo que o caso não se encaixe nas regras aqui citadas.
</p>
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