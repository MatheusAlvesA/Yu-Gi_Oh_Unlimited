<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/Mobile_Detect.php");

$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$db = new DB();
$db_proprio = new DB();
$db_proprio->ler($_SESSION['id']);
 // testes iniciais
if(!$tools->verificarstr($_GET["nome"])) {header("location: amigos.php?usernf=1");exit();}
$id = $db->nome_id($_GET["nome"]);
// testando o id se nçao existe ou é o próprio
if(!$id) {header("location: amigos.php?usernf=1");exit();}
if($id == $_SESSION["id"]) {header("location: perfil.php");exit();}
// se for amigo ou não mostrar o botão correspondente
if(amigo($id, $db)) {$add = "<tr><td colspan=\"2\" align=\"center\"><a href=\"radd.php?nome=".$_GET["nome"]."\"><button class=\"btn btn-danger\">Excluir amigo</button></a></td></tr>";} // verificar se ja e amigo
else {$add = "<tr><td colspan=\"2\" align=\"center\"><a href=\"add.php?nome=".$_GET["nome"]."\"><button class=\"btn btn-primary\">Adicionar amigo</button></a></td></tr>";}

$db->ler($id); // lendo pra mostrar os dados

$erro = '';
if($_GET['serverl']) {$erro = "<div class=\"alert alert-danger\" role=\"alert\">
      <strong>Problema!</strong> O servidor está cheio
  </div>";}
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title><?php echo $db->nome;?> Yu-Gi-Oh Unlimited</title>
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
            <li><a href="perfil.php">Minha conta</a></li>
            <li><a href="tutorial.php">Aprenda a jogar</a></li>
            <li><a href="desenvolvimento.php">Desenvolvimento</a></li>
            <li id="caixa_user_data_nxp">
              <span id="user_data_nxp"><b><?php echo $db_proprio->nome;?></b> <?php echo $db_proprio->xp;?>XP</span>
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
			<?php echo $add;?>
         </table>
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
    <a href="perfil.php"><img src="imgs/B_meu_perfil.png" class="img-responsive" /></a>
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
<?php echo file_get_contents('EOP.txt');?>
  </body>
</html>
<?php
 function amigo($id, $bd) {
  $bd->ler($_SESSION["id"]);
  $x = 1;
  while($x < $bd->amigos[0]) {
   if($bd->amigos[$x] == $id) {unset($bd);return 1;} // e amigo
   $x = $x + 1;
  }
  unset($bd);
  return 0; // não é amigo
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