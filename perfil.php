<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/Mobile_Detect.php");
$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$db = new DB();
$db->ler($_SESSION["id"]);

$erro = '';
if($_GET['serverl']) {$erro = "<div class=\"alert alert-danger\" role=\"alert\">
      <strong>Problema!</strong> O servidor está cheio
  </div>";}

// PROCESSANDO A MUDANÇA DA SENHA ALGORITMO DE 16/12/2016
  if($_POST["senha"] != '' || $_POST["senhan"] != '' || $_POST["senhav"] != '') { // significa que ele quer mudar a senha
  	if(testar_senha()) { // testar as senhas
  		$db = new DB();
  		$db->ler($_SESSION["id"]);
		  $db->set_senha($_POST['senhan']);
		  header("location: perfil.php?sucesso=1");
		  exit(); // se processar isso não deve fazer mais nada
	  } else exit();
  }
 function testar_senha() {
  if($_POST["senha"] == '' || $_POST["senhan"] == '' || $_POST["senhav"] == '') {header("location: perfil.php?erro=1"); return false;}

  $bd = new DB();
  $bd->ler($_SESSION["id"]);

  if(strpos($_POST["senha"], ' ') !== false) {header("location: perfil.php?erro=2"); return false;}
  if(sha1($_POST["senha"]) != $bd->senha) {header("location: perfil.php?erro=2"); return false;}

  if(strpos($_POST["senhan"], ' ') !== false) {header("location: perfil.php?erro=4"); return false;}

  if(strlen($_POST["senhan"]) < 6 || strlen($_POST["senhan"]) > 20) {header("location: perfil.php?erro=5"); return false;}

  if($_POST["senhan"] != $_POST["senhav"]) {header("location: perfil.php?erro=3"); return false;}
  unset($bd_temp);
  return true; // se chegar aqui deu as senhas estão ok
 }
// FIM DO PROCESSAMENTO DE MUDANÇA DE SENHA

 // PROCESSANDO A MUDANÇA DA REINO ALGORITMO DE 10/01/2017
  if(isset($_POST["reino"])) { // significa que ele quer mudar a senha
    if(testar_reino()) { // testar o valor
      $reino = (int)$_POST["reino"];
      if($reino == 4) $reino = 0; //traduzindo
      $db = new DB();
      $db->ler($_SESSION["id"]);
      $db->set_reino($reino);
      header("location: perfil.php?sucesso=2");
      exit(); // se processar isso não deve fazer mais nada
    } else exit();
  }
 function testar_reino() {
    $bd = new DB();
    $bd->ler($_SESSION["id"]);
      $reino = (int)$_POST["reino"];
      if($reino == 4) $reino = 0; //traduzindo
  if((int)$_POST["reino"] < 1 || (int)$_POST["reino"] > 4 || (int)$bd->reino === $reino) {header("location: perfil.php"); return false;}
  return true;
 }
// FIM DO PROCESSAMENTO DE MUDANÇA DE REINO

 // PROCESSANDO A MUDANÇA DA PERSONAGEM ALGORITMO DE 10/01/2017
  if(isset($_POST["char"])) { // significa que ele quer mudar a senha
    if(testar_char()) { // testar o valor
      $char = (int)$_POST["char"];
      $db = new DB();
      $db->ler($_SESSION["id"]);
      $db->set_char($char);
      header("location: perfil.php?sucesso=3");
      exit(); // se processar isso não deve fazer mais nada
    } else exit();
  }
 function testar_char() {
  if((int)$_POST["char"] < 1 || (int)$_POST["char"] > 10) {header("location: perfil.php"); return false;}
  return true;
 }
// FIM DO PROCESSAMENTO DE MUDANÇA DE PERSONAGEM
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Meu perfil Yu-Gi-Oh Unlimited</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fonte.css" type="text/css" media="screen"/>
    <?php 
      $detectar = new Mobile_Detect;
      if(!$detectar->isMobile() || $detectar->isTablet()) echo '<link href="style_PC.css" rel="stylesheet"> <!--Estilos exclusivos quando aberto no computador-->';
      else echo '<link href="style.css" rel="stylesheet"> <!--Estilos personalizados-->';
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
            <li><a href="home.php">Principal</a></li>
            <li class="active"><a href="perfil.php">Minha conta</a></li>
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

<div class="row">

  <div role="main" class="col-md-6 col-md-push-3">
    <h1 style="text-align: center;" class="oficial-font"><?php echo $db->nome;?></h1>
    <hr />
        <table class="table table-striped" cellspacing="0" cellpadding="0">
			<tr>
				<td align="left"><b>Login: <?php echo $db->nome;?></b></td>
				<td align="right"><b><?php echo $db->dinheiro." MN";?></b></td>
			</tr>
			<tr>
				<td align="left"><b>XP: <?php echo $db->xp;?></b></td>
				<td align="right"><b><?php echo $db->v."V/".$db->d."D";?></b></td>
			</tr>
			<tr>
				<td align="left"><b>Idade: <?php echo $db->idade;?></b></td>
				<td align="right"><b>Sexo: <?php if($db->sexo == 'F') {echo "Feminino";} else {echo "Masculino";}?></b></td>
			</tr>
      <tr>
        <td align="left"><b>Reino: <?php echo $db->nome_reino();?></b></td>
        <td align="right"><img src="imgs/chars/char_<?php echo $db->char;?>.png" class="img-responsive" /></td>
      </tr>
      <tr>
        <td align="left"><b>CLÃ:</b></td>
        <td align="right"><?php if($db->status_clan == 0) echo '<b>Nenhum</b>'; else echo '<b>'.$db->clan.'</b>';?></td>
      </tr>
      <tr>
        <td align="left"><b>pts_reino: <?php echo $db->pts_reino;?></b></td>
        <td align="right"><b>pts_clan: <?php echo $db->pts_clan;?></b></td>
      </tr>
         </table>

         <hr>

    <form action="perfil.php" method="post" role="form">
		<h3 style="text-align: center; color: #535b82;">Mudar senha</h3>
		<?php echo erro();?>
  	<div class="form-group">
	    <label for="textNome" class="control-label">Senha antiga</label>
    	<input id="textNome" class="form-control" placeholder="Digite sua senha..." type="password" name="senha">
  	</div>
  
  	<div class="form-group">
	    <label for="inputPassword" class="control-label">Nova Senha</label>
    	<input type="password" class="form-control" id="inputPassword" placeholder="Digite a nova senha..." name="senhan">
  	</div>

  	<div class="form-group">
	    <label for="inputPassword" class="control-label">Repita a nova senha</label>
    	<input type="password" class="form-control" id="inputPassword" placeholder="Digite a nova senha..." name="senhav">
  	</div>

  	<button type="submit" class="btn btn-danger bnt-md-lg">Mudar senha</button>
	</form>
<hr>
  <form action="perfil.php" method="post" role="form">
  <legend>Alterar seu reino:</legend>
  <?php if($_GET["sucesso"] == 2) echo "\n<div class=\"alert alert-success\" role=\"alert\"><strong>Reino alterado com sucesso !</strong></div>\n";?>
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

    <button type="submit" class="btn btn-danger bnt-md-lg">Mudar reino</button>
  </form>

  <form action="perfil.php" method="post" role="form">
      <legend>Mudar de personagem</legend>
  <?php if($_GET["sucesso"] == 3) echo "\n<div class=\"alert alert-success\" role=\"alert\"><strong>Personagem alterado com sucesso !</strong></div>\n";?>
  <div class="row" style="text-align: center;">
    <div class="col-md-2 col-md-push-1" onclick="marcar_char(1)">
      <div class="thumbnail" id="div_char_1">
        <img src="imgs/chars/char_1.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_1" value="1"></p>
        </div>
      </div>
    </div>

    <div class="col-md-2 col-md-push-1" onclick="marcar_char(2)">
      <div class="thumbnail" id="div_char_2">
        <img src="imgs/chars/char_2.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_2" value="2"></p>
        </div>
      </div>
    </div>

    <div class="col-md-2 col-md-push-1" onclick="marcar_char(3)">
      <div class="thumbnail" id="div_char_3">
        <img src="imgs/chars/char_3.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_3" value="3"></p>
        </div>
      </div>
    </div>

    <div class="col-md-2 col-md-push-1" onclick="marcar_char(4)">
      <div class="thumbnail" id="div_char_4">
        <img src="imgs/chars/char_4.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_4" value="4"></p>
        </div>
      </div>
    </div>

    <div class="col-md-2 col-md-push-1" onclick="marcar_char(5)">
      <div class="thumbnail" id="div_char_5">
        <img src="imgs/chars/char_5.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_5" value="5"></p>
        </div>
      </div>
    </div>
  </div>
  <div class="row" style="text-align: center;">
    <div class="col-md-2 col-md-push-1" onclick="marcar_char(6)">
      <div class="thumbnail" id="div_char_6">
        <img src="imgs/chars/char_6.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_6" value="6"></p>
        </div>
      </div>
    </div>

    <div class="col-md-2 col-md-push-1" onclick="marcar_char(7)">
      <div class="thumbnail" id="div_char_7">
        <img src="imgs/chars/char_7.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_7" value="7"></p>
        </div>
      </div>
    </div>

    <div class="col-md-2 col-md-push-1" onclick="marcar_char(8)">
      <div class="thumbnail" id="div_char_8">
        <img src="imgs/chars/char_8.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_8" value="8"></p>
        </div>
      </div>
    </div>

    <div class="col-md-2 col-md-push-1" onclick="marcar_char(9)">
      <div class="thumbnail" id="div_char_9">
        <img src="imgs/chars/char_9.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_9" value="9"></p>
        </div>
      </div>
    </div>

    <div class="col-md-2 col-md-push-1" onclick="marcar_char(10)">
      <div class="thumbnail" id="div_char_10">
        <img src="imgs/chars/char_10.png">
        <div class="caption">
          <p><input type="radio" class="form-check-input" name="char" id="char_radio_10" value="10"></p>
        </div>
      </div>
    </div>
  </div>
    <button type="submit" class="btn btn-danger bnt-md-lg">Mudar Personagem</button>
  </form>
</div>

  <aside role="complementary" class="col-md-3 col-md-push-3">
  <?php echo $erro;?>
    <hr><a href="mapa.php"><img src="imgs/explorar_mapa.png" class="img-responsive" /></a><hr>
    <a href="ZD/index.php"><img src="imgs/B_duelos.png" class="img-responsive" /></a><hr>
    <a href="deck.php"><img src="imgs/B_Mdeck.png" class="img-responsive" /></a><hr>
    <a href="inventario.php"><img src="imgs/B_Minventario.png" class="img-responsive" /></a><hr>
    <a href="iedeck.php"><img src="imgs/iedeck.png" class="img-responsive" /></a><hr>
    <a href="comercio.php"><img src="imgs/B_comercio.png" class="img-responsive" /></a><hr>
    <a href="ranking.php"><img src="imgs/B_ranking.png" class="img-responsive" /></a><hr>
    <a href="meu_clan.php"><img src="imgs/B_clan.png" class="img-responsive" /></a><hr>
    <a href="amigos.php"><img src="imgs/B_amigos.png" class="img-responsive" /></a><hr>
    <a href="mensagens.php"><img src="imgs/B_<?php echo avisar_mensagem();?>mensagens.png" class="img-responsive" /></a><hr>
  </aside>

    <div role="left" class="col-md-3 col-md-pull-9">
        <?php // esse código insere propaganda na página
            global $G_PROPAGANDA;
            if($G_PROPAGANDA) echo '
        <script type="text/javascript">
          ( function() {
            if (window.CHITIKA === undefined) { window.CHITIKA = { \'units\' : [] }; };
            var unit = {"calltype":"async[2]","publisher":"doutorx","width":300,"height":600,"sid":"Chitika Default"};
            var placement_id = window.CHITIKA.units.length;
            window.CHITIKA.units.push(unit);
            document.write(\'<div id="chitikaAdBlock-\' + placement_id + \'"></div>\');
        }());
        </script>
        <script type="text/javascript" src="//cdn.chitika.net/getads.js" async></script>
                ';
        ?>
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
    </script>
<?php echo file_get_contents('EOP.txt');?>
  </body>
</html>
<?php 
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

 function erro() {
  $retorno;
  if($_GET["erro"] == '' && $_GET["sucesso"] == '') {return '';} // caso nao aja erro
  if($_GET["erro"] == 1) {
  $retorno = "<div class=\"alert alert-danger\" role=\"alert\"><strong>Problema!</strong> Todos os campos devem ser preenchidos</div>\n";
  }
  if($_GET["erro"] == 2) {
  $retorno = "\n<div class=\"alert alert-danger\" role=\"alert\"><strong>Problema!</strong> Senha antiga inválida</div>\n";
  }
  if($_GET["erro"] == 3) {
  $retorno = "\n<div class=\"alert alert-danger\" role=\"alert\"><strong>Problema!</strong> As senhas não conferem</div>\n";
  }
  if($_GET["erro"] == 4) {
  $retorno = "\n<div class=\"alert alert-danger\" role=\"alert\"><strong>Problema!</strong> Nova senha inválida</div>\n";
  }
  if($_GET["erro"] == 5) {
  $retorno = "\n<div class=\"alert alert-danger\" role=\"alert\"><strong>Problema!</strong> A senha deve ter no mínimo 6 caracteres e no máximo 20</div>\n";
  }

  if($_GET["sucesso"] == 1) {
  $retorno = "\n<div class=\"alert alert-success\" role=\"alert\"><strong>Senha alterada com sucesso !</strong></div>\n";
  }
  return $retorno;
 }
?>