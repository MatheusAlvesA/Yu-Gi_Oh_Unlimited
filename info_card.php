<?php
// algoritmo responsavel por mostrar informacoes de uma carta
// alem de ter opcoes de comprar e manipular a mesma na conta do usuario
// algoritmo terminado em 18-4-2015
// terminado em 5 dias
include("libs/tools_lib.php");
include("libs/cards_lib.php");
include("libs/db_lib.php");

$tools = new Tools();
$tools->verificar();
$tools->verificarlog();
$card = new DB_cards();

$grav = new Gravacao();
$grav->set_caminho('bd_cards.txt');
$x = $grav->ler(0);
$y = $x[0] - 1;
unset($grav);
unset($x);

if($_GET['comprada'] == 1) {
	$mostrar = '<table border=1 width=100% height=40%><tr><td><b style="color: blue">A carta foi adicionada ao seu inventário.</b></td></tr></table>';
}
elseif($_GET['comprada'] == 2) {
	$mostrar = '<table border=1 width=100% height=40%><tr><td><b style="color: red">Seu inventário está cheio.</b></td></tr></table>';
}
else {
	if($_GET['id'] == 13) {$titulo = 'ERRO'; $mostrar = erro(13);}
	elseif($_GET['fonte'] <= 0 || $_GET['fonte'] > 3) {$titulo = 'ERRO'; $mostrar = erro($_GET['fonte']);}
	elseif($_GET['id'] >= 1 && $_GET['id'] <= $y) {
		$card->ler_id($_GET['id']);
		if(!$card->filtro() && ($card->tipo != 'fusion' && $card->tipo != 'fusion-effect')) { // caso a carta não possa ser vista por não estar pronta
   	 		header('location: info_card.php');
   	 		exit();
		}
	$mostrar = infos($card, (int)$_GET['fonte']);
	$titulo = $card->nome;
	}
	else {$titulo = 'ERRO'; $mostrar = erro($_GET['fonte']);}
}
?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://<?php echo $_SERVER['SERVER_NAME'];?>/imgs/favicon.png" />
<title><?php echo $titulo;?> Yu-Gi-Oh Unlimited</title>
</head>
<body>
<img src="<?php echo $tools->capa;?>" height="<?php echo $tools->rcapa;?>" width="100%" />
<?php echo $mostrar;?>
<?php include 'rodape.txt';?>
<script type="text/javascript" src="ZD/libs/php.js"></script>
<script type="text/javascript">
	function remover_deck(id) {
 		file_get_contents('info_card.php?id='+id+'&fonte=1&act=1');
 		window.opener.location.href='deck.php?movida=1';
 		window.close();
	}
	function mover_deck(id) {
 		file_get_contents('info_card.php?id='+id+'&fonte=2&act=1');
 		window.opener.location.href='inventario.php?movida=1';
 		window.close();
	}
	function remover_inventario(id) {
 		file_get_contents('info_card.php?id='+id+'&fonte=2&act=2');
 		window.opener.location.href='inventario.php?removida=1';
 		window.close();
	}
</script>
<?php echo file_get_contents('EOP.txt');?>
</body>
</html>

<?php
function erro($fonte) {
$tools = new Tools();
if($fonte == 1) {
	return '<table border=1 width=100% height=40%><tr><td><b style="color: red">Esta carta não foi encontrada no seu deck. Verifique novamente.</b></td></tr></table>';
	}
elseif($fonte == 2) {
	return '<table border=1 width=100% height=40%><tr><td><b style="color: red">Esta carta não foi encontrada no seu inventário. Verifique novamente.</b></td></tr></table>';
	}
elseif($fonte == 13) {
	return '<table border=1 width=100% height=40%><tr><td><b style="color: red">A carta número 13 está trancada nos confins do banco de dados de onde ela jamais sairá.</b></td></tr></table>';
	}
else {
	return '<table border=1 width=100% height=40%><tr><td><b style="color: red">Erro desconhecido. Verifique o endereço e tente novamente.</b></td></tr></table>';
	}
}

 function infos($card, $fonte) {
$temp = new DB();
$temp->ler($_SESSION['id']);

if($fonte == 1) {
	$x = 1;
	while($card->id != $temp->deck[$x] && $temp->deck[0] > $x) {$x++;}
	if($temp->deck[0] <= $x) {return erro($fonte);}
}
if($fonte == 2) {
	$x = 1;
	while($card->id != $temp->cards[$x] && $temp->cards[0] > $x) {$x++;}
	if($temp->cards[0] <= $x) {return erro($fonte);}
 }

if($_GET['act'] != '') {modificar($_GET['act']);}
// se chegou aqui e pq esta tudo certo com os dados passados
// agora tudo vai ser preencido para ser retornado
$retorno = '
<table border=1 width=80% style="float: top; position:relative; left: 10%;"><tr>
<td><img src="'.$card->img.'" width=100% /></td></tr></table>
'."\n".'<table border=1 width=100%><tr><td align="center" style = "background-color: black;" colspan=3 >';

switch ($card->categoria) {
 case 'monster':
 if($card->tipo == 'normal') {$retorno .= '<b style="color: yellow;">Monstro</b></td></tr>';}
 if($card->tipo == 'fusion') {$retorno .= '<b style="color: purple;">Monstro fusão</b></td></tr>';}
 if($card->tipo == 'effect') {$retorno .= '<b style="color: #FF9933;">Monstro com efeito</b></td></tr>';}
 if($card->tipo == 'fusion-effect') {$retorno .= '<b style="color: purple;">Monstro fusão</b></td></tr>';}
 if($card->tipo == 'ritual') {$retorno .= '<b style="color: blue;">Monstro de ritual</b></td></tr>';}
 if($card->tipo == 'ritual-effect') {$retorno .= '<b style="color: blue;">Monstro de ritual</b></td></tr>';}
$retorno .= '<tr><td align="center" colspan=3><p>'.$card->descricao.'<p></td></tr>'."\n";
$retorno .= '<tr><td><b>ATK: </b>'.$card->atk.'</td><td><b>DEF: </b>'.$card->def.'</td><td align="center"><b>LV: </b>'.$card->lv.'</td></tr>
<tr><td align="center">'.especie($card->specie).'</td><td align="center"><b style="color: green;">Grátis</b></td>'.unidades($temp, $card).'</tr></table>
</table>';
 break;
 case 'spell':
 $retorno .= '<b style="color: green;">Mágica</b></td></tr>';
 $retorno .= '<tr><td align="center" colspan=3><p>'.$card->descricao.'<p></td></tr>'."\n";
$retorno .= '<tr><td align="center">'.especie($card->tipo).'</td><td align="center"><b style="color: green;">Grátis</b></td>'.unidades($temp, $card).'</tr></table>
</table>';
 break;
 case 'trap':
 $retorno .= '<b style="color: pink;">Armadilha</b></td></tr>';
 $retorno .= '<tr><td align="center" colspan=3><p>'.$card->descricao.'<p></td></tr>'."\n";
$retorno .= '<tr><td align="center">'.especie($card->tipo).'</td><td align="center"><b style="color: green;">Grátis</b></td>'.unidades($temp, $card).'</tr></table>
</table>';
 break;
}

if($fonte == 1) {
if($temp->cards[0] <= 100) {$retorno .= "\n".'<a href="javascript:remover_deck('.$card->id.')"><img src="imgs/B_remover.png" height="15%" width="100%" /></a>';}
}
if($fonte == 2) {
if(limite($temp, $card) && $temp->deck[0] < 51) {$retorno .= "\n".' <a href="javascript:mover_deck('.$card->id.')"><img src="imgs/B_colocar.png" height="15%" width="100%" /></a>';}
$retorno .= '<a href="javascript:remover_inventario(\''.$card->id.'\')"><img src="imgs/B_deletar.png" height="15%" width="100%" /></a>';
}
if($fonte == 3) {
if($temp->cards[0] <= 100 && $card->filtro()) {$retorno .= "\n".'<a href="info_card.php?id='.$card->id.'&fonte=3&act=1"><img src="imgs/B_comprar.png" height="15%" width="100%" /></a>';}
}
return $retorno;
}

function especie($specie) {
return '<b>'.$specie.'</b>';
}

function limite($user, $card) {
	$x = 1;
	$y = 0;
	 while($x <= ($user->deck[0] - 1)) {
   if($card->id == $user->deck[$x]) {$y++;}
		$x++;
	 }
	if($y < $card->maximo) {return 1;}
	else {return 0;}
}

function unidades($user, $card) {
	$x = 1;
	$y = 0;
	 while($x <= ($user->deck[0] - 1)) {
   if($card->id == $user->deck[$x]) {$y++;}
		$x++;
	 }
	if($y < $card->maximo) {$cor = ' style="color: green;"';}
	else {$cor = ' style="color: red;"';}
	return '<td align="center" '.$cor.'><b>'.$y.'/'.$card->maximo.'</b></td>';
}

function modificar($act) {
$temp = new DB();
$tempc = new DB_cards();
$tempc->ler_id($_GET['id']);
$temp->ler($_SESSION['id']);
 if($act == 1) {
  if($_GET['fonte'] == 1 && ($temp->cards[0] - 1) < 100) {
   $temp->set_deck($_GET['id'], 0); // retirar do deck
   $temp->set_cards($_GET['id'], 1); // colocar no inventario
   exit();
  }

  if($_GET['fonte'] == 2 && ($temp->deck[0] - 1) < 50) {
	if(limite($temp, $tempc)) {
		$temp->set_deck($_GET['id'], 1); // colocar no deck
   		$temp->set_cards($_GET['id'], 0); // retirar do inventario
   		exit();
    }
  }
	
 	if($_GET['fonte'] == 3 && ($temp->cards[0] - 1) < 100 && $tempc->filtro()) {
   		$temp->set_cards($_GET['id'], 1); // colocar no inventario
   		header('location: info_card.php?comprada=1');
   		exit();
	}
	elseif($_GET['fonte'] == 3 && ($temp->cards[0] - 1) > 100) {
		header('location: info_card.php?comprada=2');
   		exit();
	}
 }

if($act == 2) {
	 $temp->set_cards($_GET['id'], 0); // remover do inventario
     exit();
}

}
?>