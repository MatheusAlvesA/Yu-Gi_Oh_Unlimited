<?php
// este algoritmo é responsável por manipular o chat do jogo
// algoritmo terminado em 2015-05-10
include("libs/gravacao_lib.php");
include_once("../libs/desafio_lib.php");
include_once("../libs/db_lib.php");
class Chat {
var $limite = 30;
var $atual;
	function __construct() {
		$x = 1;
	while($x <= 7) {
  	if(file_exists('chat/'.$x.'.txt')) {$this->atual = $x;}
	$x++;
	}
 	if($this->atual == '') {
	$arq = fopen('chat/1.txt', 'w');
	fwrite($arq, date('Y').'-'.date('m').'-'.date('d'));
	fclose($arq);
	$this->atual = 1;
	}
	else {
	$arq = fopen('chat/'.$this->atual.'.txt', 'r');
	$data = fgets($arq);
	if(substr($data, -1, 1) == "\n") {$data = substr($data, 0, -1);}
	fclose($arq);
	if($data == '') {
		$arq = fopen('chat/'.$this->atual.'.txt', 'w');
		fwrite($arq, date('Y').'-'.date('m').'-'.date('d'));
		fclose($arq);
		}
		elseif($data != date('Y').'-'.date('m').'-'.date('d')) {
		if($this->atual < 7) {
			$this->atual++;
			$arq = fopen('chat/'.$this->atual.'.txt', 'w');
		 fwrite($arq, date('Y').'-'.date('m').'-'.date('d'));
		  fclose($arq);
			}
			else {
			$this->atual = 1;
	$arq = fopen('chat/'.$this->atual.'.txt', 'r');
	$data = fgets($arq);
        if(substr($data, -1, 1) == "\n") {$data = substr($data, 0, -1);}
        if($data != date('Y').'-'.date('m').'-'.date('d')) {
			$arq = fopen('chat/'.$this->atual.'.txt', 'w');
		      fwrite($arq, date('Y').'-'.date('m').'-'.date('d'));
        fclose($arq);
        }
			}
		}
	}
}

 function ler() {
  $grav = new Gravacao();
  $grav->set_caminho('chat/'.$this->atual.'.txt');
  $matriz = $grav->ler(1);
  unset($grav);
  if($matriz[0][0] <= 12) {
 $retorno[0][0] = $matriz[0][0];
  $retorno[1][0] = $matriz[1][0];

  $y = 0;
   for($x = $matriz[0][0] - 1; $x > 1; $x--) {
	  $retorno[$x][0] = $matriz[(($matriz[0][0] - 1) - $y)][0];
	  $retorno[$x][1] = $this->sanitizar_utf8(htmlspecialchars($matriz[(($matriz[0][0] - 1) - $y)][1], 0, "UTF-8"));
	  $y++;
   }
  return $retorno;
  }
  else {
  $retorno[0][0] = 12;
  $retorno[1][0] = $matriz[1][0];

  $y = 0;
   for($x = 11; $x > 1; $x--) {
	  $retorno[$x][0] = $matriz[(($matriz[0][0] - 1) - $y)][0];
	  $retorno[$x][1] = $this->sanitizar_utf8(htmlspecialchars($matriz[(($matriz[0][0] - 1) - $y)][1], 0, "UTF-8"));
	  $y++;
   }
return $retorno;
  }
 }

 function enviar($nome, $msg) {
	$msg = str_replace(';', '.', $msg);
	$msg = str_replace(substr("a\nb",1 ,1), ' ', $msg);
	if(strlen($msg) > 300) {$msg = substr($msg, 0, 300);}
  $grav = new Gravacao();
  $grav->set_caminho('chat/'.$this->atual.'.txt');
  $matriz = $grav->ler(1);
  $matriz[0][$matriz[0][0]] = 2;
  $matriz[$matriz[0][0]][0] = $this->sanitizar_utf8($nome);
  $matriz[$matriz[0][0]][1] = $this->sanitizar_utf8($msg);
  $matriz[0][0] = $matriz[0][0] + 1;
  $grav->set_matriz($matriz);
  $grav->gravar();
unset($grav);
 }
 /**
 * Função que converte caracteres ISO-8859-1 para UTF-8, mantendo os caracteres UTF-8 intactos.
 * @param string $texto
 * @return string
 */
function sanitizar_utf8($texto) {
    $saida = '';

    $i = 0;
    $len = strlen($texto);
    while ($i < $len) {
        $char = $texto[$i++];
        $ord  = ord($char);

        // Primeiro byte 0xxxxxxx: simbolo ascii possui 1 byte
        if (($ord & 0x80) == 0x00) {

            // Se e' um caractere de controle
            if (($ord >= 0 && $ord <= 31) || $ord == 127) {

                // Incluir se for: tab, retorno de carro ou quebra de linha
                if ($ord == 9 || $ord == 10 || $ord == 13) {
                    $saida .= $char;
                }

            // Simbolo ASCII
            } else {
                $saida .= $char;
            }

        // Primeiro byte 110xxxxx ou 1110xxxx ou 11110xxx: simbolo possui 2, 3 ou 4 bytes
        } else {

            // Determinar quantidade de bytes analisando os bits da esquerda para direita
            $bytes = 0;
            for ($b = 7; $b >= 0; $b--) {
                $bit = $ord & (1 << $b);
                if ($bit) {
                    $bytes += 1;
                } else {
                    break;
                }
            }

            switch ($bytes) {
            case 2: // 110xxxxx 10xxxxxx
            case 3: // 1110xxxx 10xxxxxx 10xxxxxx
            case 4: // 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
                $valido = true;
                $saida_padrao = $char;
                $i_inicial = $i;
                for ($b = 1; $b < $bytes; $b++) {
                    if (!isset($texto[$i])) {
                        $valido = false;
                        break;
                    }
                    $char_extra = $texto[$i++];
                    $ord_extra  = ord($char_extra);

                    if (($ord_extra & 0xC0) == 0x80) {
                        $saida_padrao .= $char_extra;
                    } else {
                        $valido = false;
                        break;
                    }
                }
                if ($valido) {
                    $saida .= $saida_padrao;
                } else {
                    $saida .= ($ord < 0x7F || $ord > 0x9F) ? utf8_encode($char) : '';
                    $i = $i_inicial;
                }
                break;
            case 1:  // 10xxxxxx: ISO-8859-1
            default: // 11111xxx: ISO-8859-1
                $saida .= ($ord < 0x7F || $ord > 0x9F) ? utf8_encode($char) : '';
                break;
            }
        }
    }
    return $saida;
}
}
// este algoritmo vai ser responsavel por lidar com os usuarios online
// terminado em 01/06/2015
class Online {
var $grav;
var $file = 'onlines.txt';

 function __construct() {
  if(!file_exists($this->file)) {fclose(fopen($this->file, 'w'));}
  $this->grav = new Gravacao();
  $this->grav->set_caminho($this->file);
 }

 function onlines() {
  $array = $this->grav->ler(1);
  return $array;
 }

 function in($id) {
	if((int)$id == '') {return 0;}
	 $grav = new Gravacao();
  $grav->set_caminho($this->file);
  $array = $grav->ler(1);

  $x = 1;
  while($id != $array[$x][0] && $x < $array[0][0]) {$x++;}
// atualizar tempo de um usuario se ele ja estiver online
  if($x < $array[0][0]) {$array[$x][1] = time(); $grav->set_matriz($array); $grav->gravar(); return 1;}

  $array[$array[0][0]][0] = $id; // anotando o id no arquivo
  $array[$array[0][0]][1] = time(); // anotando o horário que o usuário entro
  $array[$array[0][0]][2] = 'S'; // o usuario está disponível
  $array[0][$array[0][0]] = 3;
  $array[0][0]++;
  $grav->set_matriz($array);
  $grav->gravar();

  return 1;
 }

 function out($id) {
  if((int)$id == '') {return 0;}
  $grav = new Gravacao();
  $grav->set_caminho($this->file);
  $array = $grav->ler(1);

  $x = 1;
  while($id != $array[$x][0] && $x < $array[0][0]) {$x++;}
  if($x >= $array[0][0]) {return 1;}

  while($x < $array[0][0]) {
   $array[$x] = $array[$x+1];
   $x++;
  }
  unset($array[$array[0][0]-1]); //apaga linha extra
  unset($array[0][$array[0][0] - 1]);
  $array[0][0] = $array[0][0] - 1;

  $grav->set_matriz($array);
  $grav->gravar();
  return 1;
 }

 function atualizar() {
	if(!file_exists('last_att.txt')) {$xtemp = fopen('last_att.txt', 'w'); fwrite($xtemp, time()); fclose($xtemp); return 0;}
	$arq = fopen('last_att.txt', 'r');
	$last = fgets($arq);
	fclose($arq);
	if((time() - $last)/60 < 1) {return 0;}
	 $xtemp = fopen('last_att.txt', 'w');
	 fwrite($xtemp, time());
	 fclose($xtemp);
	
  $matriz = $this->onlines();
  $x = 1;
   while($x < $matriz[0][0]) {
	  $decorrido = (time() - $matriz[$x][1]);
    if($decorrido > 5) {$this->out($matriz[$x][0]);}
    $x++;
   }
 }

 function disponivel($id, $f=1) {
	if((int)$id == '') {return 0;}
	$grav = new Gravacao();
	$grav->set_caminho($this->file);
  $array = $grav->ler(1);

  $x = 1;
  while($id != $array[$x][0] && $x < $array[0][0]) {$x++;}
  if($x >= $array[0][0]) {return 0;}

  if($f == 1) {$array[$x][2] = 'S';}
   else {$array[$x][2] = 'N';}

  $grav->set_matriz($array);
  $grav->gravar();
  return 1;
  }
 }
// esta classe da suporte ao inicio do duelo
class SSID {
var $arquivo = 'desafios.txt';

 function __construct() {if(!file_exists($this->arquivo)) {fclose(fopen($this->arquivo, 'w'));}}

 function adversario($id) {
	if((int)$id == '') {return 0;}
  $grav = new Gravacao();
  $grav->set_caminho($this->arquivo);
  $matriz = $grav->ler(1);
  unset($grav);

  $x = 1;
  while($x < $matriz[0][0]) {
   if($id == $matriz[$x][0]) {return $matriz[$x][1];}
   if($id == $matriz[$x][1]) {return $matriz[$x][0];}
   $x++;
	}
	return 0;
 }

 function desafiado($id) {
	if((int)$id == '') {return 0;}
  $grav = new Gravacao();
  $grav->set_caminho($this->arquivo);
  $matriz = $grav->ler(1);
  unset($grav);

  $x = 1;
  while($x < $matriz[0][0]) {
   if($id == $matriz[$x][0] && $matriz[$x][2] == 'P') {return 1;}
   if($id == $matriz[$x][1] && $matriz[$x][2] == 'P') {return 2;}

   if($id == $matriz[$x][0] && $matriz[$x][2] != 'P') {return $matriz[$x][2];}
   if($id == $matriz[$x][1] && $matriz[$x][2] != 'P') {return $matriz[$x][2];}
   $x++;
	}
	return 0;
 }

 function desafiar($id) {
 	if((int)$id == 0) {return 0;}
  $grav = new Gravacao();
  $grav->set_caminho($this->arquivo);
  $matriz = $grav->ler(1);

  for($x = 1; $x < $matriz[0][0]; $x++) {
   if($id == $matriz[$x][0] || $id == $matriz[$x][1]) {return 0;}
	}

	session_start();
	$matriz[$matriz[0][0]][0] = $_SESSION['id'];
	$matriz[$matriz[0][0]][1] = $id;
  $matriz[$matriz[0][0]][2] = 'P';
  $matriz[$matriz[0][0]][3] = time();
  $matriz[$matriz[0][0]][4] = 0; // id do duelo
  $matriz[0][$matriz[0][0]] = 5;
  $matriz[0][0]++;

  $grav->set_matriz($matriz);
  $grav->gravar();
	}
	
	 function recusar($id) {
 	if((int)$id == '') {return 0;}
  $grav = new Gravacao();
  $grav->set_caminho($this->arquivo);
  $matriz = $grav->ler(1);

  $x = 1;
  while($x < $matriz[0][0] && $id != $matriz[$x][1]) {$x++;}
  if($x >= $matriz[0][0]) {return 0;}

  $matriz[$x][2] = 'N';

  $grav->set_matriz($matriz);
  $grav->gravar();
	}
	
	 function aceitar($id) {
 	if((int)$id == '') {return 0;}
  $grav = new Gravacao();
  $grav->set_caminho($this->arquivo);
  $matriz = $grav->ler(1);

  $x = 1;
  while($x < $matriz[0][0] && $id != $matriz[$x][1]) {$x++;}
  if($x >= $matriz[0][0]) {return 0;}

  $matriz[$x][2] = 'S';

  $grav->set_matriz($matriz);
  $grav->gravar();
	}
	
	 function cancelar($id) {
  if((int)$id == '') {return 0;}
  $grav = new Gravacao();
  $grav->set_caminho($this->arquivo);
  $array = $grav->ler(1);

  $x = 1;
  while($id != $array[$x][0] && $x < $array[0][0]) {$x++;}
  if($x >= $array[0][0]) {return 1;}
  $adv = $array[$x][1];

  while($x < $array[0][0]) {
   $array[$x] = $array[$x+1];
   $x++;
  }
  unset($array[$array[0][0]-1]); //apaga linha extra
  unset($array[0][$array[0][0] - 1]);
  $array[0][0] = $array[0][0] - 1;

  $grav->set_matriz($array);
  $grav->gravar();

 $temp = new Online();
 $temp->disponivel($id);
 $temp->disponivel($adv);
 unset($temp);

  return 1;
 }

function id_duelo($id) {
	if((int)$id == '') {return 0;}
  $grav = new Gravacao();
  $grav->set_caminho($this->arquivo);
  $matriz = $grav->ler(1);
  unset($grav);

  $x = 1;
  while($x < $matriz[0][0]) {
   if($id == $matriz[$x][0] || $id == $matriz[$x][1]) {break;}
   $x++;
	}
	if($x >= $matriz[0][0]) {return 0;}
	return $matriz[$x][4];
}

function instanciar($id, $codigo = null) {
if((int)$id == '') {return 0;}
session_start();
$temp = $this->id_duelo($id);
if($temp) {return $temp;}
unset($temp);

  $grav = new Gravacao();
  $grav->set_caminho($this->arquivo);
  $matriz = $grav->ler(1);
  if($codigo === null) $id_duelo = uniqid(); // se o código do duelo não veio de fonte externa
  else $id_duelo = $codigo; // se veio
  $d1 = 0;
  $d2 = 0;
if($_SESSION['duelo_mapa'] !== 'S') { // caso o duelo seja vindo do loby
  $x = 1;
  while($x < $matriz[0][0]) {
   if($id == $matriz[$x][0] || $id == $matriz[$x][1]) {break;}
   $x++;
	}
	if($x >= $matriz[0][0]) {return 0;}

	$d1 = $matriz[$x][0];
	$d2 = $matriz[$x][1];
  $matriz[$x][2] = 'X';
  $matriz[$x][4] = $id_duelo;

  $grav->set_matriz($matriz);
  $grav->gravar();
  unset($grav);
  unset($matriz);
}
else { // esse é um duelo vindo do mapa
  $db = new DB;
  $db->ler($_SESSION['id']);
  $desafio = new Desafio;
  $desafio->existe($db->nome);
  $d1 = $db->nome_id($desafio->desafiante);
  $d2 = $db->nome_id($desafio->desafiado);
}
  $_SESSION['duelando'] = 'S';
  $_SESSION['id_duelo'] = $id_duelo;


 mkdir('duelos/'.$id_duelo, 0770);
 fclose(fopen('duelos/'.$id_duelo.'/metadata.txt', 'w'));
  $grav = new Gravacao();
  $grav->set_caminho('duelos/'.$id_duelo.'/metadata.txt');
  $array_temp[0][0] = 4;
  $array_temp[0][1] = 1;
  $array_temp[0][2] = 2;
  $array_temp[0][3] = 2;
  $array_temp[1][0] = time();
  $array_temp[2][0] = $d1;
  $array_temp[2][1] = $d2; 
  $array_temp[3][0] = $this->escolher($d1, $d2);
  $array_temp[3][1] = 1;
  $grav->set_matriz($array_temp);
  $grav->gravar();
  unset($grav);

 mkdir('duelos/'.$id_duelo.'/'.$d1, 0770);
  $grav = new Gravacao();
  $grav->set_caminho('duelos/'.$id_duelo.'/'.$d1.'/deck.txt');
  $db = new DB();
  $db->ler($d1);
  $deck = $this->embaralhar($db->deck);
  $grav->set_array($deck);
  $hand[0] = 6;;
  for($x = 1; $x <= 5; $x++) {$hand[$x] = $deck[$x]; $grav->apagar(1);}
  $grav->gravar();
  unset($grav);
  unset($db);
  unset($deck);
  $grav = new Gravacao();
  $grav->set_caminho('duelos/'.$id_duelo.'/'.$d1.'/hand.txt');
  $grav->set_array($hand);
  $grav->gravar();
  unset($grav);
  fclose(fopen('duelos/'.$id_duelo.'/'.$d1.'/cemitery.txt', 'w'));
  $alp = fopen('duelos/'.$id_duelo.'/'.$d1.'/lps.txt', 'w');
  fwrite($alp, '8000');
  fclose($alp);
  $arq = fopen('duelos/'.$id_duelo.'/'.$d1.'/campo.txt', 'w');
  fwrite($arq, "0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0");
  fclose($arq);

 mkdir('duelos/'.$id_duelo.'/'.$d2, 0770);
  $grav = new Gravacao();
  $grav->set_caminho('duelos/'.$id_duelo.'/'.$d2.'/deck.txt');
  $db = new DB();
  $db->ler($d2);
  $deck = $this->embaralhar($db->deck);
  $grav->set_array($deck);
  $hand[0] = 6;;
  for($x = 1; $x <= 5; $x++) {$hand[$x] = $deck[$x]; $grav->apagar(1);}
  $grav->gravar();
  unset($grav);
  unset($db);
  $grav = new Gravacao();
  $grav->set_caminho('duelos/'.$id_duelo.'/'.$d2.'/hand.txt');
  $grav->set_array($hand);
  $grav->gravar();
  unset($grav);
  fclose(fopen('duelos/'.$id_duelo.'/'.$d2.'/cemitery.txt', 'w'));
  $alp = fopen('duelos/'.$id_duelo.'/'.$d2.'/lps.txt', 'w');
  fwrite($alp, '8000');
  fclose($alp);
  $arq = fopen('duelos/'.$id_duelo.'/'.$d2.'/campo.txt', 'w');
  fwrite($arq, "0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0");
  fclose($arq);

return $id_duelo;
}

function finalizar($id) {
if($id == '') {return 0;}
session_start();
if($_SESSION['resultado']) {
$array = explode('/', $_SESSION['resultado']);

$bancov = new DB();
$bancod = new DB();
$bancov->ler((int)$array[0]);
$bancod->ler((int)$array[1]);

$bancov->set_vd(1);
$bancod->set_vd(-1);
$taxa = $bancov->xp - $bancod->xp;

if($taxa >= 1000) { // se o vitorioso era muito melhor que o derrotado
    $bancov->set_xp($bancov->xp + 50);
    $bancod->set_xp($bancod->xp + 50);
}
elseif($taxa <= -1000) { // se era muito pior
    $bancov->set_xp($bancov->xp + 100);
    $bancod->set_xp($bancod->xp + 25);
}
else { // no meio termo
    $bancov->set_xp($bancov->xp + 100);
    $bancod->set_xp($bancod->xp + 50);
}
// setando pontos do mapa
if($_SESSION['duelo_mapa'] === 'S') { // esse duelo aconteceu a partir do mapa
  $ptsv = 2;// o vitorioso ganha dois pontos por padrão
  $ptsd = 1; // o derrotado ganha um ponto por padrão
  if($_SESSION['duelo_honra'] === 1) $ptsv++;// honra da mais um ponto pro ganhador

  $bancov->set_pts_reino((int)$bancov->pts_reino+$ptsv); // setando como a soma do que já tinha mais o ganho agora
  $bancod->set_pts_reino((int)$bancod->pts_reino+$ptsd); // o mesmo se aplica ao derrotado
}

if($bancov->status_clan != 0)
  $bancov->set_pts_clan((int)$bancov->pts_clan+2);

if($bancod->status_clan != 0)
  $bancod->set_pts_clan((int)$bancod->pts_clan+1);

unset($bancov);
unset($bancod);
}
$_SESSION['duelando'] = '';
$_SESSION['id_duelo'] = '';
$_SESSION['resultado'] = '';
if($_SESSION['duelo_mapa'] !== 'S') { // caso o duelo seja vindo do loby
  $grav = new Gravacao();
  $grav->set_caminho($this->arquivo);
  $matriz = $grav->ler(1);

  $x = 1;
  while($x < $matriz[0][0] && $id != $matriz[$x][4]) {$x++;}
  if($x < $matriz[0][0]) {$this->cancelar($matriz[$x][0]);}
}
$_SESSION['duelo_mapa'] = '';
$_SESSION['honra'] = '';
if(file_exists('duelos/'.$id)) {$this->delTree('duelos/'.$id);}
return 1;
}

function atualizar() {
	if(!file_exists('last_att2.txt')) {$xtemp = fopen('last_att2.txt', 'w'); fwrite($xtemp, time()); fclose($xtemp); return 0;}
	$arq = fopen('last_att2.txt', 'r');
	$last = fgets($arq);
	fclose($arq);
	if((time() - $last)/60 < 1) {return 0;}
	 $xtemp = fopen('last_att2.txt', 'w');
	 fwrite($xtemp, time());
	 fclose($xtemp);
	
	$grav = new Gravacao();
  $grav->set_caminho($this->arquivo);
  $matriz = $grav->ler(1);

  $x = 1;
   while($x < $matriz[0][0]) {
	  $minutos = (time() - $matriz[$x][3]) / 60;
    if($minutos > 2 && $matriz[$x][2] == 'P') {$this->recusar($matriz[$x][1]);}
    if($minutos > 1 && $matriz[$x][2] == 'N') {$this->cancelar($matriz[$x][0]);}
    $x++;
   }
 }

function embaralhar($array) {
	$temp[0] = '';
 for($x = 1; $x < $array[0]; $x++) {$temp[$x - 1] = $array[$x];}
 shuffle($temp);
 for($x = 1; $x < $array[0]; $x++) {$array[$x] = $temp[$x - 1];}
 return $array;
}

function escolher($x, $y) {
 $array[0] = $x;
 $array[1] = $y;
 shuffle($array);
 return $array[1];
}

public static function delTree($dir) { // função baixada da net para apagar pastas não vazias
  $files = array_diff(scandir($dir), array('.','..')); 
  foreach ($files as $file) { 
    (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : @unlink("$dir/$file"); 
  }
  $retorno = @rmdir($dir);
  return $retorno;
}
}
?>