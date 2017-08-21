<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/cards_lib.php");
include("libs/Mobile_Detect.php");

    $tools = new Tools();
    $tools->verificar();
    $tools->verificarlog();

if($_GET['baixar']) {baixar();}
elseif($_GET['up']) {uploadC(); exit();}
elseif($_GET['set']) {set($_GET['set']); exit();}

$errodl = ''; // mensagem de erro no download
if($_GET['errodl']) {$errodl = '<div class="alert alert-danger" role="alert"><strong>Seu deck precisa conter ao menos uma carta para ser baixado.</strong></div>';}

$db = new DB();
$db->ler($_SESSION["id"]);

$erro = '';
if($_GET['serverl']) {$erro = "  <div class=\"alert alert-danger\" role=\"alert\">
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
    <title>Importar/Exportar Deck Yu-Gi-Oh Unlimited</title>
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
              <span id="user_data_nxp"><b><?php echo $db->nome;?></b> <?php echo $db->xp;?>XP</span>
              <a href="logout.php" id="BNTdeslogar_ancora"><span id="BNTdeslogar" class="glyphicon glyphicon-off" aria-hidden="true"></span></a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">

  <div role="main" class="col-md-6 col-md-push-3">
  
      <h1 style="text-align: center;">Importar/Exportar DECK</h1><hr>
      <div style="width: 100%; height: 150px; background-color: white; border: 1px solid green; position: relative;">
		<table border="0" width="100%" height="10%" style="position: absolute; top: 0; left: 0;"><tr><td align="center"><b>Atualizar deck</b></td></tr></table>
		<div style="overflow: scroll; position: absolute; top: 15%; left: 0; width: 100%; height: 85%; background-color: white; border: 0;"><?php echo visualizar($_GET['visualizar']);?></div>
	</div>
	<div class="row" style="position: relative; top: 5px;">
	<?php echo $errodl;?>
		<div class="col-md-3 col-md-push-5">
			<?php
			$bnt = '<a href="iedeck.php?set='.$_GET['visualizar'].'"><button type="button" class="btn btn-success" style="float: right;">ATUALIZAR DECK</button></a>';
				if($_GET['visualizar'] == '')
					$bnt = '<button type="button" class="btn btn-success disabled" style="float: right;">ATUALIZAR DECK</button>';
				echo $bnt;
			?>
 	 	</div>
		<div class="col-md-3 col-md-push-6">
			<a href="iedeck.php?baixar=1"><button type="button" class="btn btn-danger" style="float: right;">BAIXAR DECK</button></a>
 	 	</div>
	</div>

  </div>

  <aside role="complementary" class="col-md-3 col-md-push-3">
  <?php echo $erro;?>
    <hr><a href="mapa.php"><img src="imgs/explorar_mapa.png" class="img-responsive" /></a><hr>
    <a href="ZD/index.php"><img src="imgs/B_duelos.png" class="img-responsive" /></a><hr>
    <a href="deck.php"><img src="imgs/B_Mdeck.png" class="img-responsive" /></a><hr>
    <a href="inventario.php"><img src="imgs/B_Minventario.png" class="img-responsive" /></a><hr>
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
<script language="JavaScript">
function abrir(URL) {
 
  var width = 350;
  var height = 500;
 
  var left = 200;
  var top = 100;
 
  window.open(URL,'Info', 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=no, fullscreen=no');
 
}
</script>
    <script src="bootstrap/jquery-3.1.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
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

function uploadC() {
	session_start();
	$bd = new DB();
	$bd->ler($_SESSION['id']);
	$tmp = $bd->nome;
	unset($bd);
	if($_FILES['arquivo']['error']) {header("location: iedeck.php?erro=1");exit();}
	if($_FILES['arquivo']['size'] > 1024) {header("location: iedeck.php?erro=2");}
	$nome = $tmp.'_'.time().'.deck';
 	if(move_uploaded_file($_FILES['arquivo']['tmp_name'], 'decks_tmp/'.$nome)) {
	 	if(autenticar('decks_tmp/'.$nome)) {header("location: iedeck.php?visualizar=$nome");}
			else {
			 unlink('decks_tmp/'.$nome);
			 header("location: iedeck.php?erro=2");
			}
	}
	else {header("location: iedeck.php?erro=5");}
}

function baixar() {
        session_start();
        if(!isset($_SESSION['id'])) {header('location: index.php');exit();}
	$bd = new DB();
	$bd->ler($_SESSION['id']);
	if($bd->deck[0] < 2) {header("location: iedeck.php?errodl=1"); exit();}
        $array = array();
        for($x = 1; $x < $bd->deck[0]; $x++) $array[$x-1] = (int)$bd->deck[$x];
        $string = utf8_encode(json_encode($array));

	header('Content-Type: application/octet-stream');
	header("Content-Disposition: attachment; filename=".basename($bd->nome.'.deck'));
	header('Content-Length: '.strlen($string));
	header("Pragma: no-cache");
	header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Expires: 0");
	echo $string;
	unset($bd);
	exit();
}

function visualizar($nome) {
	if($nome == '') {
		$erro = '<label>Selecione seu deck:</label>';
		if($_GET['erro'] == 1) {$erro = '<label style="color: red;">Falha no upload</b></td></tr></label>';}
		elseif($_GET['erro'] == 2) {$erro = '<label style="color: red;">Deck corrompido</b></td></tr></label>';}

		return '<form method="post" action="iedeck.php?up=1" enctype="multipart/form-data">
  '.$erro.'
  <input type="file" name="arquivo" />
  <button type="submit" class="btn btn-primary" style="float: right;">UPLOAD</button>
</form>';
	}
	$check = explode(".", $nome);
	if(end($check) != 'deck') {header("location: iedeck.php?importar=1&erro=1"); exit();}
	if(!file_exists('decks_tmp/'.$nome)) {header("location: iedeck.php?importar=1&erro=1"); exit();}
	$string = file_get_contents('decks_tmp/'.$nome);
	$array = json_decode(utf8_encode($string));

	global $tools;

return div($array);
}

function div($array) {
	$html = '';
	$espaco = 0;
 	for($x = 0; $x < count($array); $x++) {
  		$html .= ' <a href="javascript:abrir(\'info_card.php?fonte=3&id='.(int)$array[$x].'\')" target="_blank"><img src="imgs/cards/pequenas/'.(int)$array[$x].'.png" width="20%" height="100%" style="position: absolute; top: 0; left: '.$espaco.'%;" /></a>'."\n";
  		$espaco += 20;
 	}
	return $html;
}

function set($nome) {
	$check = explode(".", $nome);
	if(end($check) != 'deck') {header("location: iedeck.php?erro=1"); exit();}
	if(!file_exists('decks_tmp/'.$nome)) {header("location: iedeck.php?erro=1"); exit();}
	$string = file_get_contents('decks_tmp/'.$nome);
	$array = json_decode(utf8_encode($string));

 	for($x = 0; $x < 50; $x++) {
		if((int)$array[$x]) {
 		  $array[$x] = (int)$array[$x];
  		}
  		else {$array[$x] = 0;}
 	}

	session_start();
	$bd_temp = new DB();
	$bd_temp->ler($_SESSION['id']);
	$bd_temp->refazer_deck($array);
	unset($bd_temp);
	header('location: deck.php?att=1');
}

function autenticar($caminho) {
	$string = file_get_contents($caminho);
	$array = json_decode($string);
        file_put_contents('debug.php', json_last_error());
	if(count($array) > 50 || count($array) < 1) {return false;}
	$bdcard = new DB_cards();
	$ncards = $bdcard->bd[0][0] - 1;
	
	foreach($array as $carta) {
		$bdcard->ler_id($carta);
		if(!$bdcard->filtro()) {return false;}
		$repetidas[$carta]++;
		if($repetidas[$carta] > $bdcard->maximo || !$bdcard->filtro()) {return false;}
 		if(!(int)$carta) {return false;}
 		if((int)$carta <= 0 || (int)$carta > $ncards) {return false;}
	}

	unset($bdcard);
	return true;
}
?>