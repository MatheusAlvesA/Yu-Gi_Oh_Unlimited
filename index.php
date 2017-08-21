<?php
require 'libs/tools_lib.php';
require_once 'libs/Mobile_Detect.php';

$obj = new Tools();
$obj->verificar();
//contador de acessos
if(!file_exists('acessos.txt')) {fclose(fopen('acessos.txt', 'w'));}
$acessos = file_get_contents('acessos.txt');
file_put_contents('acessos.txt', $acessos+1);

global $G_SALVAR_LOG_ACESSOS;
if($G_SALVAR_LOG_ACESSOS) contabilizar(); // função para contar acessos e registrar ips

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
    <meta property="og:image" content="https://www.yugiohult.com.br/imgs/obelisk_ICO.jpg">
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
            <li class="active"><a href="#">Principal</a></li>
            <li><a href="tutorial.php">Aprenda a jogar</a></li>
            <li><a href="desenvolvimento.php">Desenvolvimento</a></li>
            <li><a href="sobre.php">Sobre</a></li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">

<aside role="complementary" class="col-md-3">
	<form action="log.php" method="post" role="form">
		<h2 style="text-align: center; color: #535b82;">Faça Login</h2><hr>
  	<div class="form-group">
  	<?php echo errologin();?>
	    <label for="textNome" class="control-label">Login</label>
    	<input id="textNome" class="form-control" placeholder="Digite seu Nome..." type="text" name="nome">
  	</div>
  
  	<div class="form-group">
	    <label for="inputPassword" class="control-label">Senha</label>
    	<input type="password" class="form-control" id="inputPassword" placeholder="Digite sua Senha..." name="senha">
  	</div>

  	<button type="submit" class="btn btn-primary">Logar</button>

	</form>
    <hr>
<div class="row">
    <div role="main" class="col-md-12">
        
        <div  style="background-color: #F0F8FF; text-align: center;">
            <hr>
            <h2 class="oficial-font" style="color: #2F4F4F; text-decoration: underline;">Ou Experimente</h2>
            <hr>
        </div>
        <a href="duelar.php"><img src="imgs/B_duelar_agora.png" class="img-responsive" /></a><hr>
    </div>
</div>
    
<?php // esse código insere propaganda na página
    global $G_PROPAGANDA;
    $m = new Mobile_Detect;
    if($G_PROPAGANDA && !$m->isMobile()) echo 'CÓDIGO DO BANNER DE PROPAGANDA';
?>
</aside>
    
<div role="main" class="col-md-9" style="background-color: white;">

<form id="formExemplo" action="reg.php" method="post">
	<h2 style="text-align: center;">Cadastre-se para Jogar</h2><hr>
  <div class="form-group">
  <?php echo erro();?>
    <label for="textNome" class="control-label">Login</label>
    <input id="textNome" class="form-control" placeholder="Digite seu usuário... somente caracteres [a-z][0-9]" value="<?php echo $_GET['nome']?>" type="text" name="name">
  </div>
  
  <div class="form-group">
    <label for="inputEmail" class="control-label">Email</label>
    <input id="inputEmail" class="form-control" placeholder="Digite seu E-mail" value="<?php echo $_GET['email']?>" type="email" name="email">
  </div>
  
  <div class="form-group">
    <label for="inputPassword" class="control-label">Senha</label>
    <input type="password" class="form-control" id="inputPassword" placeholder="Digite sua Senha..." name="senha">
  </div>
  
  <div class="form-group">
    <label for="inputConfirm" class="control-label">Confirme a Senha</label>
    <input type="password" class="form-control" id="inputConfirm" placeholder="Confirme sua Senha..." name="senhav">
  </div>

<div class="form-group">
  <label for="number-input" class="control-label">Ano de nascimento</label>
  <?php
    if(isset($_GET['idade'])) echo '<input class="form-control" type="number" value="'.$_GET['idade'].'" id="number-input" name="idade">';
    else echo '<input class="form-control" type="number" value="1990" id="number-input" name="idade">';
  ?>
</div>

 <legend>Sexo</legend>
    <div class="form-check">
      <label class="form-check-label">
          <input type="radio" class="form-check-input" name="sexo" id="optionsRadios1" value="M"<?php if(isset($_GET['sexo']) && $_GET['sexo'] === 'M') echo ' checked';?>> Masculino
        <input type="radio" class="form-check-input" name="sexo" id="optionsRadios1" value="F"<?php if(isset($_GET['sexo']) && $_GET['sexo'] === 'F') echo ' checked';?>> Feminino
      </label>
    </div>
    <hr>
  <legend>Escolha um reino para defender</legend>
  <div class="row" style="text-align: center;">
    <div class="col-md-3" onclick="marcar_reino(2)">
      <div class="thumbnail" id="div_reino_2">
        <img src="imgs/slifer_ICO.jpg">
        <div class="caption">
          <h3>Slifer</h3>
          <p><input type="radio" class="form-check-input" name="reino" id="radio_2" value="2"></p>
        </div>
      </div>
    </div>

    <div class="col-md-3" onclick="marcar_reino(3)">
      <div class="thumbnail" id="div_reino_3">
        <img src="imgs/ra_ICO.jpg">
        <div class="caption">
          <h3>Dragão de RA</h3>
          <p><input type="radio" class="form-check-input" name="reino" id="radio_3" value="3"></p>
        </div>
      </div>
    </div>

    <div class="col-md-3" onclick="marcar_reino(1)">
      <div class="thumbnail" id="div_reino_1">
        <img src="imgs/obelisk_ICO.jpg" class="img-responsive">
        <div class="caption">
          <h3>Obelisco</h3>
          <p><input type="radio" class="form-check-input" name="reino" id="radio_1" value="1"></p>
        </div>
      </div>
    </div>
      
    <div class="col-md-3" onclick="marcar_reino(4)">
      <div class="thumbnail" id="div_reino_4">
        <img src="imgs/neutro_ICO.jpg">
        <div class="caption">
          <h3>Neutro</h3>
          <p><input type="radio" class="form-check-input" name="reino" id="radio_4" value="4"></p>
        </div>
      </div>
    </div>
  </div>

  <legend>Escolha um personagem</legend>
  <div class="row" style="text-align: center;">
    <div class="col-md-1 col-md-push-1" onclick="marcar_char(1)">
      <div class="thumbnail" id="div_char_1">
        <img src="imgs/chars/char_1.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_1" value="1"></p>
        </div>
      </div>
    </div>

    <div class="col-md-1 col-md-push-1" onclick="marcar_char(2)">
      <div class="thumbnail" id="div_char_2">
        <img src="imgs/chars/char_2.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_2" value="2"></p>
        </div>
      </div>
    </div>

    <div class="col-md-1 col-md-push-1" onclick="marcar_char(3)">
      <div class="thumbnail" id="div_char_3">
        <img src="imgs/chars/char_3.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_3" value="3"></p>
        </div>
      </div>
    </div>

    <div class="col-md-1 col-md-push-1" onclick="marcar_char(4)">
      <div class="thumbnail" id="div_char_4">
        <img src="imgs/chars/char_4.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_4" value="4"></p>
        </div>
      </div>
    </div>

    <div class="col-md-1 col-md-push-1" onclick="marcar_char(5)">
      <div class="thumbnail" id="div_char_5">
        <img src="imgs/chars/char_5.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_5" value="5"></p>
        </div>
      </div>
    </div>

    <div class="col-md-1 col-md-push-1" onclick="marcar_char(6)">
      <div class="thumbnail" id="div_char_6">
        <img src="imgs/chars/char_6.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_6" value="6"></p>
        </div>
      </div>
    </div>

    <div class="col-md-1 col-md-push-1" onclick="marcar_char(7)">
      <div class="thumbnail" id="div_char_7">
        <img src="imgs/chars/char_7.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_7" value="7"></p>
        </div>
      </div>
    </div>

    <div class="col-md-1 col-md-push-1" onclick="marcar_char(8)">
      <div class="thumbnail" id="div_char_8">
        <img src="imgs/chars/char_8.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_8" value="8"></p>
        </div>
      </div>
    </div>

    <div class="col-md-1 col-md-push-1" onclick="marcar_char(9)">
      <div class="thumbnail" id="div_char_9">
        <img src="imgs/chars/char_9.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_9" value="9"></p>
        </div>
      </div>
    </div>

    <div class="col-md-1 col-md-push-1" onclick="marcar_char(10)">
      <div class="thumbnail" id="div_char_10">
        <img src="imgs/chars/char_10.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_10" value="10"></p>
        </div>
      </div>
    </div>
  </div>

  <div class="checkbox">
    <label>
      <p>Li os <a href="termos.php" target="_blank">termos de uso</a>: <input type="radio" name="termos" value="S"/></p>
    </label>
  </div>
  
  <button type="submit" class="btn btn-primary">Enviar</button>
</form>

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
    <script type="text/javascript">
      function marcar_reino(reino) {
        $("#radio_"+reino).prop("checked", true);
        $("#div_reino_1").css("background-color", "white");
        $("#div_reino_2").css("background-color", "white");
        $("#div_reino_3").css("background-color", "white");
        $("#div_reino_4").css("background-color", "white");
        $("#div_reino_"+reino).css("background-color", "#95CAE4");
      }
      function marcar_char(char) {
        $("#char_radio_"+char).prop("checked", true);
        for(var loop = 1; loop <= 10; loop++) $("#div_char_"+loop).css("background-color", "white");
        $("#div_char_"+char).css("background-color", "#95CAE4");
      }
<?php
    if((int)$_GET['reino'] >= 1 && (int)$_GET['reino'] <= 4) echo 'marcar_reino('.(int)$_GET['reino'].');'."\n";
    if((int)$_GET['char'] >= 1 && (int)$_GET['char'] <= 10) echo 'marcar_char('.(int)$_GET['char'].');'."\n";
?>
    </script>
<?php echo file_get_contents('EOP.txt');?>
  </body>
</html>
<?php
 function erro() {
  $retorno;
  if($_GET["erro"] == '') {return '';} // caso nao aja erro
  if($_GET["erro"] == 1) {
  	$retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> Todos os campos devem ser preenchidos
	</div>";
  }
  if($_GET["erro"] == 2) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> O nome de usuário deve ter no mínimo 6 caracteres e no máximo 15
	</div>";
  }
  if($_GET["erro"] == 3) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> A senha deve ter entre 6 e 20 caracteres
	</div>";
  }
  if($_GET["erro"] == 4) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> O nome de usuário não pode ter caracteres especiais
	</div>";
  }
  if($_GET["erro"] == 5) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> A senha não pode ter espaço em branco
	</div>";
  }
  if($_GET["erro"] == 6) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> Idade inválida
	</div>";
  }
 if($_GET["erro"] == 7) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> Erro ao tentar registrar
	</div>";
  }
 if($_GET["erro"] == 8) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> Este nome de usuário já existe
	</div>";
  }
 if($_GET["erro"] == 9) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> As senhas não conferem
	</div>";
  }
 if($_GET["erro"] == 10) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> Digite um E-mail válido
	</div>";
  }
 if($_GET["erro"] == 11) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> Este E-mail já está sendo usado
	</div>";
  }
 if($_GET["erro"] == 12) {
  $retorno = "	<div class=\"alert alert-danger\" role=\"alert\">
  		<strong>Problema!</strong> Os termos de uso devem ser aceitos
	</div>";
  }
  return $retorno;
 }

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
  if($_GET["erro"] == 15) {
  $retorno = "  <div class=\"alert alert-danger\" role=\"alert\">
      <strong>Problema!</strong> Sua conta encontra-se banida
  </div>";
  }
  return $retorno;
 }

function contabilizar() {
	$linha = "IP: ".$_SERVER['REMOTE_ADDR']." | Navegador: ".$_SERVER['HTTP_USER_AGENT']." | Data: ".date("d/m/y H:i")."\n";
	if(!file_exists('log.txt')) {fclose(fopen('log.txt', 'w'));}

	$arq = fopen("log.txt", "a+");
	fwrite($arq, $linha);
	fclose($arq);
}

?>