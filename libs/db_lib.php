<?php
//este algoritmo vai ser responsavel por ler o banco de dados
//e interpretalo assim com tbm gravar novos registros
//algoritmo terminado em 28/04/2015
//modificado em 19/06/2016
//terminado em 2 dias
//modificado para funcionar em PDO terminado em 1 dia
include_once("erro/erro_lib.php");
require_once 'config.php';
class DB {
 var $bd;
 var $nome;
 var $email;
 var $confirmado;
 var $senha;
 var $idade;
 var $sexo;
 var $xp;
 var $dinheiro;
 var $deck;
 var $cards;
 var $amigos;
 var $status;
 var $v;
 var $d;
 var $registro;
 var $acesso;
 var $reino; // 1 obelisco, 2 slifer, 3 rá e 0 neutro
 var $pts_reino;
 var $char;
 var $clan;
 var $pts_clan;
 var $status_clan;
 var $VIP;
 
 function __construct() {
   $this->erro = new Erro;
   global $G_SQL_HOST;
   global $G_SQL_BANCO;
   global $G_SQL_USER;
   global $G_SQL_SENHA;
  try {
   @$this->bd = new PDO('mysql:host='.$G_SQL_HOST.';dbname='.$G_SQL_BANCO, $G_SQL_USER, $G_SQL_SENHA);
  }
  catch(PDOException $e) { // não está capturando o erro apenas redirecionando o usuario
      header('location: dberro.php');
      exit(); // garantindo que nada mais sera feito se o banco falhar
  }
 }
 
function __destruct() {
 $this->bd = NULL; //simbolicamente conexão fechada
}

private function deck() { // seta o deck do usuario
 $consulta = $this->bd->prepare('SELECT * FROM decks WHERE id = :id'); // preparando
 $consulta->execute(array('id' => (int)$this->cod)); // executando a em cima do id do jogador
 $rs = $consulta->fetchAll(); // convertendo em um vetor
 $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
 if(!$rs) {$retorno[0] = 1; return $retorno;} // algo deu errado
  $y = 1; // auxiliar
  $deck[0] = "x";
  for($x = 1; $x <= 50; $x++) { // compactando o deck corretamente
   if ($rs['c'.$x] != 0) {$deck[$y] = $rs['c'.$x]; $y++;}
  }
  $deck[0] = count($deck); // registrando seu tamanho
  return $deck; // retornando
 }

private function cards() { // seta os cards reservas
 $consulta = $this->bd->prepare('SELECT * FROM inventarios WHERE id = :id'); // preparando
 $consulta->execute(array('id' => (int)$this->cod)); // executando a em cima do id do jogador
 $rs = $consulta->fetchAll(); // convertendo em um vetor
 $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
 if(!$rs) {$retorno[0] = 1; return $retorno;}
  $y = 1; // auxiliar
  $deck[0] = "x";
  for($x = 1; $x <= 100; $x++) { // compactando inventário
	if($rs['c'.$x] != 0) {$deck[$y] = $rs['c'.$x]; $y++;}
  }
  $deck[0] = count($deck); // registrando o tamanho
  return $deck; //retornando
}

private function amigos() { // seta o array com os amigos
 $consulta = $this->bd->prepare('SELECT * FROM amigos WHERE id = :id'); // preparando
 $consulta->execute(array('id' => (int)$this->cod)); // executando a em cima do id do jogador
 $rs = $consulta->fetchAll(); // convertendo em um vetor
 $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
 if(!$rs) {$retorno[0] = 1; return $retorno;}
 $y = 1;
 $deck[0] = "x";
 for($x = 1; $x <= 100; $x++) {
  if ($rs['a'.$x] != 0) {$deck[$y] = $rs['a'.$x]; $y++;}
 }
 $deck[0] = count($deck);
 return $deck;
}
 
function ler($_cod) { //seta a galera toda
 if($_cod == '') {return 1;} //tratar erro
 $this->cod = (int)$_cod;
 $consulta = $this->bd->prepare('SELECT * FROM usuarios WHERE id = :id'); // preparando
 $consulta->execute(array('id' => $this->cod)); // executando a em cima do id do jogador
 $rs = $consulta->fetchAll(); // convertendo em um vetor
 $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
 if(!$rs) {return 1;} 
  //setar tudo
  $this->nome = $rs['nome'];
  $this->senha = $rs['senha'];
  $this->email = $rs['email'];
  $this->confirmado =$rs['confirmado'];
  $this->idade = date('Y')-$rs['idade'];
  $this->sexo = $rs['sexo'];
  $this->status = $rs['status'];
  session_start();
  if($this->status != 1 && $_SESSION['id'] == $this->cod) {$_SESSION['ban'] = 'S';}
  $this->xp = $rs['xp'];
  $this->dinheiro = $rs['dinheiro'];
  $this->v = (int)$rs['vitorias'];
  $this->registro = $this->data($rs['registro']);
  $this->acesso = $this->data($rs['acesso']);
  $this->d = (int)$rs['derrotas'];
  $this->reino = $rs['reino'];
  $this->pts_reino = $rs['pts_reino'];
  $this->char = $rs['personagem'];
  $this->clan = $rs['clan'];
  $this->pts_clan = $rs['pts_clan'];
  $this->status_clan = $rs['status_clan'];
  $this->VIP = $rs['VIP']; // debug
  $this->amigos = $this->amigos();
  $this->deck = $this->deck();
  $this->cards = $this->cards();
 }
 
function criar() {
  if($this->nome == '') {$this->erro->set(8, '');}
  if($this->email == '') {$this->erro->set(8, '');}
  if($this->idade == '') {$this->erro->set(8, '');}
  if($this->senha == '') {$this->erro->set(8, '');}
  if($this->sexo != 'M' && $this->sexo != 'F') {$this->erro->set(8, '');}
  if($this->status == '') {$this->erro->set(8, '');}
  if($this->xp == '') {$this->erro->set(8, '');}
  if($this->dinheiro == '') {$this->erro->set(8, '');}
  if(!isset($this->reino)) {$this->erro->set(8, '');}
  if(!isset($this->pts_reino)) {$this->erro->set(8, '');}
  if(!isset($this->char)) {$this->erro->set(8, '');}
  if(!isset($this->pts_clan)) {$this->erro->set(8, '');}
 
 if($this->user_existe($this->nome)) {return 0;} // previne inconsistencia no banco de dados
  try { // tudo aqui é propenço a erro
   $registro = $this->bd->prepare("INSERT INTO usuarios (nome, sexo, idade, email, confirmado, senha, xp, dinheiro, status, vitorias, derrotas, registro, acessada, reino, pts_reino, personagem, pts_clan) VALUES (:nome, :sexo, :idade, :email, :confirmado, :senha, :xp, :dinheiro, :status, '0', '0', '".date('Y').'-'.date('m').'-'.date('d')."', '".date('Y').'-'.date('m').'-'.date('d')."', :reino, :pts_reino, :personagem, :pts_clan)"); // preparando
   $registro->execute(array('nome' => $this->nome, 'sexo' => $this->sexo, 'idade' => (int)$this->idade, 'email' => $this->email, 'confirmado' => 0, 'senha' => sha1($this->senha), 'xp' => $this->xp, 'dinheiro' => $this->dinheiro, 'status' => $this->status, 'reino' => $this->reino, 'pts_reino' => $this->pts_reino, 'personagem' => $this->char, 'pts_clan' => $this->pts_clan)); // executando a em cima do id do jogador

   $consulta = $this->bd->prepare("SELECT id FROM usuarios WHERE nome = :nome"); // preparando
   $consulta->execute(array('nome' => $this->nome)); // executando a em cima do nome do jogador
   $rs = $consulta->fetchAll(); // convertendo em um vetor
   $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
   $this->cod = $rs['id']; // setando o valor do id gerado pelo banco automaticamente

   $vazio = $this->dados_vazios(); // pegando os dados vazios para o novo usuário
   $registro_d = $this->bd->prepare("INSERT INTO decks (id, ".$vazio['d'].") VALUES (:id, ".$vazio['dv'].")");
   $registro_d->execute(array('id' => $this->cod)); // executando a em cima do id do jogador
  
   $registro_i = $this->bd->prepare("INSERT INTO inventarios (id, ".$vazio['i'].") VALUES (:id, ".$vazio['iv'].")");
   $registro_i->execute(array('id' => $this->cod)); // executando a em cima do id do jogador
  
   $registro_a = $this->bd->prepare("INSERT INTO amigos (id, ".$vazio['a'].") VALUES (:id, ".$vazio['av'].")");
   $registro_a->execute(array('id' => $this->cod)); // executando a em cima do id do jogador
  } catch(PDOException $e) {header('location: dberro.php'); exit();}
}

function set_deck($id_card, $faser) {
 if($this->cod == '') {return false;} // se o usuario ainda não foi lido
 if($id_card == '') {return false;}
 if($faser > 1 || $faser < 0) {return false;}
try {
 if($faser == 0) { // retirar carta do deck
  $consulta = $this->bd->prepare('SELECT * FROM decks WHERE id = :id');
  $consulta->execute(array('id' => $this->cod));
  $rs = $consulta->fetchAll();
  $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
  if(!$rs) {return false;}
  for($x = 1;$rs['c'.$x] != $id_card && $x <= 50;$x++);
  if($x >= 51) {return false;} // a carta não estava aqui
  $retorno = 1;
  $requisitar = $this->bd->prepare("UPDATE decks SET c".$x."=0 WHERE id=:id");
  $requisitar->execute(array('id' => $this->cod));
  return $retorno;
 }

 if($faser == 1) { // colocar carta no deck
  $consulta = $this->bd->prepare('SELECT * FROM decks WHERE id = :id');
  $consulta->execute(array('id' => $this->cod));
  $rs = $consulta->fetchAll();
  $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
  if(!$rs) {return false;}
  for($x = 1;$rs['c'.$x] != 0 && $x <= 50;$x++);
  if($x >= 51) {return false;} // não tem espaço
  $retorno = 1;
  $requisitar = $this->bd->prepare("UPDATE decks SET c".$x."=:carta WHERE id=:id");
  $requisitar->execute(array('id' => $this->cod, 'carta' => $id_card));
  return $retorno;
 }
} catch (PDOException $e) {header('location: dberro.php'); exit();} // capturando erro
}

 function set_cards($id_card, $faser) {
 if($this->cod == '') {return 0;} // se o usuario ainda não foi lido
 if($id_card == '') {return 0;}
 if($faser > 1 || $faser < 0) {return 0;}
try {
 if($faser == 0) { // retirar carta do inventário
  $consulta = $this->bd->prepare('SELECT * FROM inventarios WHERE id = :id');
  $consulta->execute(array('id' => $this->cod));
  $rs = $consulta->fetchAll();
  $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
  if(!$rs) {return false;}
  for($x = 1;$rs['c'.$x] != $id_card && $x <= 100;$x++);
  if($x >= 101) {return false;} // a carta não estava aqui
  $retorno = 1;
  $requisitar = $this->bd->prepare("UPDATE inventarios SET c".$x."=0 WHERE id=:id");
  $requisitar->execute(array('id' => $this->cod));
  return $retorno;
 }

 if($faser == 1) { // colocar carta no inventário
  $consulta = $this->bd->prepare('SELECT * FROM inventarios WHERE id = :id');
  $consulta->execute(array('id' => $this->cod));
  $rs = $consulta->fetchAll();
  $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
  if(!$rs) {return false;}
  for($x = 1;$rs['c'.$x] != 0 && $x <= 100;$x++);
  if($x >= 101) {return false;} // não tem espaço
  $retorno = 1;
  $requisitar = $this->bd->prepare("UPDATE inventarios SET c".$x."=:carta WHERE id=:id");
  $requisitar->execute(array('id' => $this->cod, 'carta' => $id_card));
  return $retorno;
 }
} catch (PDOException $e) {header('location: dberro.php'); exit();} // capturando erro
}

function set_amigos($id, $faser) {
 if($this->cod == '') {return 0;} // se o usuario ainda não foi lido
 if($id == '') {return 0;}
try {
 if($faser == 0) { // retirar amigo
  $consulta = $this->bd->prepare('SELECT * FROM amigos WHERE id = :id');
  $consulta->execute(array('id' => $this->cod));
  $rs = $consulta->fetchAll();
  $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
  if(!$rs) {return false;}
  for($x = 1;$rs['a'.$x] != $id && $x <= 100;$x++);
  if($x >= 101) {return false;} // a carta não estava aqui
  $retorno = 1;
  $requisitar = $this->bd->prepare("UPDATE amigos SET a".$x."=0 WHERE id=:id");
  $requisitar->execute(array('id' => $this->cod));
  return $retorno;
 }

 if($faser == 1) { // colocar amigo
  $consulta = $this->bd->prepare('SELECT * FROM amigos WHERE id = :id');
  $consulta->execute(array('id' => $this->cod));
  $rs = $consulta->fetchAll();
  $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
  if(!$rs) {return false;}
  for($x = 1;$rs['a'.$x] != 0 && $x <= 100;$x++);
  if($x >= 101) {return false;} // não tem espaço
  $retorno = 1;
  $requisitar = $this->bd->prepare("UPDATE amigos SET a".$x."=:amigo WHERE id=:id");
  $requisitar->execute(array('id' => $this->cod, 'amigo' => $id));
  return $retorno;
 }
} catch (PDOException $e) {header('location: dberro.php'); exit();} // capturando erro

}

function data($data) {
 $array = explode('-', $data);
 for($x = 0; $x < 3; $x++) {$array[$x] = substr($array[$x], 0, -1);}
 $retorno['ano'] = $array[0];
 $retorno['mes'] = $array[1];
 $retorno['dia'] = $array[2];
 return $retorno;
}
 
function refazer_deck($deck) {
 for($x = 1; $x <= 50; $x++) {$_deck['d'] .= 'c'.$x.'='.$deck[$x-1].', ';}
 $_deck['d'] = substr($_deck['d'], 0, -2);
 try {
  $requisitar = $this->bd->prepare("UPDATE decks SET ".$_deck['d']." WHERE id=:id");
  $requisitar->execute(array('id' => $this->cod));
  return true;
 }
 catch(PDOException $e) {header('location: dberro.php'); return false;}
}

private function dados_vazios() { // retorna dados de preechimento para o sql totalmente vazios
 for($x = 1; $x <= 50; $x++) {$retorno['d'] .= 'c'.$x.', ';}
 $retorno['d'] = substr($retorno['d'], 0, -2);
// inserindo deck padrão de todos os duelistas
  $retorno['dv'] .= '174, '; // mago negro
  $retorno['dv'] .= '78, '; //Blackland Fire Dragon
  $retorno['dv'] .= '289, '; //Gaia The Fierce Knight
  $retorno['dv'] .= '111, '; //Celtic Guardian
  $retorno['dv'] .= '151, '; // Curse of Dragon
  $retorno['dv'] .= '53, '; //Battle Ox
  $retorno['dv'] .= '454, '; //Magician of Black Chaos
  $retorno['dv'] .= '71, '; //Black Magic Ritual
  $retorno['dv'] .= '516, '; //Mystical Elf
  $retorno['dv'] .= '63, '; // Beaver Warrior
  $retorno['dv'] .= '248, '; //Feral Imp
  $retorno['dv'] .= '659, '; //Skull Servant
  $retorno['dv'] .= '15, '; //Alpha The Magnet Warrior
  $retorno['dv'] .= '65, '; //Beta The Magnet Warrior
  $retorno['dv'] .= '290, '; // Gamma The Magnet Warrior
  $retorno['dv'] .= '805, '; //Winged Dragon, Guardian of the Fortress #1
  $retorno['dv'] .= '806, '; //Winged Dragon, Guardian of the Fortress #2
  $retorno['dv'] .= '295, '; // Gazelle the King of Mythical Beasts
  $retorno['dv'] .= '573, '; //Queens Knight
  $retorno['dv'] .= '143, '; //Cosmo Queen
  $retorno['dv'] .= '317, '; //Giant Soldier of Stone
  $retorno['dv'] .= '460, '; //Mammoth Graveyard
  $retorno['dv'] .= '653, '; //Silver Fang
  $retorno['dv'] .= '346, '; //Green Phantom King
  $retorno['dv'] .= '537, '; //Ojama Black
  $retorno['dv'] .= '538, '; //Ojama Green
  $retorno['dv'] .= '539, '; //Ojama Yellow
  $retorno['dv'] .= '3, '; // Abaki
  $retorno['dv'] .= '4, '; //Abare Ushioni
  $retorno['dv'] .= '9, '; // Airknight Parshath
  // magicas e armadilhas
  $retorno['dv'] .= '16, '; //Altar for Tribute
  $retorno['dv'] .= '25, '; //Ancient Rules
  $retorno['dv'] .= '43, '; //Attraffic Control
  $retorno['dv'] .= '49, '; //Banner of Courage
  $retorno['dv'] .= '72, '; //Black Pendant
  $retorno['dv'] .= '87, '; //Blasting the Ruins
  $retorno['dv'] .= '91, '; //Block Attack
  $retorno['dv'] .= '91, '; //Block Attack
  $retorno['dv'] .= '93, '; //Blue Medicine
  $retorno['dv'] .= '103, '; //Burden of the Mighty
  $retorno['dv'] .= '0, ';
  $retorno['dv'] .= '0, ';
  $retorno['dv'] .= '0, ';
  $retorno['dv'] .= '0, ';
  $retorno['dv'] .= '0, ';
  $retorno['dv'] .= '0, ';
  $retorno['dv'] .= '0, ';
  $retorno['dv'] .= '0, ';
  $retorno['dv'] .= '0, ';
  $retorno['dv'] .= '0';

 for($x = 1; $x <= 100; $x++) {$retorno['i'] .= 'c'.$x.', ';}
 $retorno['i'] = substr($retorno['i'], 0, -2);
 for($x = 1; $x <= 100; $x++) {$retorno['iv'] .= '0, ';}
 $retorno['iv'] = substr($retorno['iv'], 0, -2);

 for($x = 1; $x <= 100; $x++) {$retorno['a'] .= 'a'.$x.', ';}
 $retorno['a'] = substr($retorno['a'], 0, -2);
 for($x = 1; $x <= 100; $x++) {$retorno['av'] .= '0, ';}
 $retorno['av'] = substr($retorno['av'], 0, -2);
 return $retorno;
}

function user_existe($nome) {
 try {
  $consulta = $this->bd->prepare("SELECT * FROM usuarios WHERE nome = :nome");
  $consulta->execute(array('nome' => $nome));
  $array_temp = $consulta->fetchAll();
  $array_temp = $array_temp[0]; // convertendo de matriz com uma linha para vetor comum
  if($array_temp['nome'] == '') {return false;} // não existe esse nome no banco
  else {return true;} // existe esse nome já registrado no banco
 }
 catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function email_existe($email) {
 try {
  $consulta = $this->bd->prepare("SELECT * FROM usuarios WHERE email = :nome");
  $consulta->execute(array('email' => $email));
  $array_temp = $consulta->fetchAll();
  $array_temp = $array_temp[0]; // convertendo de matriz com uma linha para vetor comum
  if($array_temp['email'] == '') {return false;} // não existe esse email no banco
  else {return true;} // existe esse email já registrado no banco
 }
 catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function nome_id($nome) {
 try {
  $consulta = $this->bd->prepare("SELECT * FROM usuarios WHERE nome = :nome");
  $consulta->execute(array('nome' => $nome));
  $array_temp = $consulta->fetchAll();
  $array_temp = $array_temp[0]; // convertendo de matriz com uma linha para vetor comum
  if($array_temp['nome'] == '') {return false;} // não existe esse nome no banco
  else {return $array_temp['id'];}
 }
 catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function acessada() {
 try {
  if($this->cod == '') {return false;}
  $consulta = $this->bd->prepare("UPDATE usuarios SET acessada = '".date('Y').'-'.date('m').'-'.date('d')."' WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod));
  return true;
 }
 catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function set_senha($senha) {
 if($this->cod == '' || $senha == '') {return false;}
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET senha=:senha WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'senha' => sha1($senha)));
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function set_xp($xp) {
 if($this->cod == '' || $xp == '') {return false;}
 $this->xp = (int)$xp;
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET xp=:xp WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'xp' => $this->xp));
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function set_pts_reino($pts) {
 if($this->cod == '' || $pts == '') {return false;}
 $this->pts_reino = (int)$pts;
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET pts_reino=:pts WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'pts' => $this->pts_reino));
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function set_pts_clan($pts) {
 if($this->cod == '' || $pts == '') {return false;}
 $this->pts_clan = (int)$pts;
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET pts_clan=:pts WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'pts' => $this->pts_clan));
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function set_char($char) {
 if($this->cod == '' || ((int)$char < 1 || (int)$char > 10)) {return false;}
 $this->char = (int)$char;
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET personagem=:pers WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'pers' => $this->char));
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function set_vd($r) {
 try {
  if($r > 0) {
   (int)$this->v += (int)$r;
   $consulta = $this->bd->prepare("UPDATE usuarios SET vitorias=:v WHERE id = :id");
   $consulta->execute(array('id' => (int)$this->cod, 'v' => $this->v));
  }
  else {
   (int)$this->d -= (int)$r;
   $consulta = $this->bd->prepare("UPDATE usuarios SET derrotas=:d WHERE id = :id");
   $consulta->execute(array('id' => (int)$this->cod, 'd' => $this->d));
  }
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function reset_pts() {
 try {
   $reset_reino = $this->bd->prepare("UPDATE usuarios SET pts_reino=0");// pontos acumulados pelo reino zerados
   $reset_clan = $this->bd->prepare("UPDATE usuarios SET pts_clan=0");// pontos acumulados pelo clã zerados
   $reset_reino->execute();
   $reset_clan->execute();
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function tabela_user() {
  try {
   $consulta = $this->bd->prepare("SELECT * FROM usuarios");
   $consulta->execute();
   return $consulta->fetchAll(); // retornando a tabela inteira
  } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function banlist() {
  try {
   $consulta = $this->bd->prepare("SELECT * FROM usuarios where status=2");
   $consulta->execute();
   return $consulta->fetchAll(); // retornando a tabela inteira
  } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function tabela_user_reino($reino) {
  try {
   $consulta = $this->bd->prepare("SELECT * FROM usuarios WHERE reino=:r ORDER BY pts_reino");
   $consulta->execute(array('r' => $reino));
   return $consulta->fetchAll(); // retornando a tabela inteira
  } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function Nplayers_reino($reino) {
  $lista = $this->tabela_user_reino($reino);
  return count($lista);
}

function lider_reino($reino) {
  $lista = $this->tabela_user_reino($reino);
  return $lista[count($lista)-1]['nome'];
}

function total_pts_reino($reino) {
  $lista = $this->tabela_user_reino($reino);
  $soma = 0;
  for($loop = 0; $loop < count($lista);$loop++)
    $soma += $lista[$loop]['pts_reino'];
  return $soma;
}

function ranking() { // essa função retorna um array com ids por ordem de xp e V/D
  $N_Duelistas = 100; // esse é o numero de jogadores que vai aparecer no ranking
  try {
   $consulta = $this->bd->prepare("SELECT * FROM usuarios ORDER BY xp");
   $consulta->execute();
   $matriz = $consulta->fetchAll(); // retornando a tabela inteira
   
   usort($matriz, "comparar"); // ordenando todos os usuários

   $tamanho = count($matriz);
   for($x = 0;$x < $N_Duelistas && $x < $tamanho; $x++)
    $retorno[$x] = $matriz[$tamanho-1 - $x]['nome'];
   
   return $retorno;
  } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function set_reino($reino) {
 if(!isset($this->cod) || ((int)$reino < 0 || (int)$reino > 3)) {return false;}
 $this->reino = (int)$reino;
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET reino=:reino WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'reino' => $this->reino));
  // zerando os pontos que ele acumulou pelo reino
  $consulta = $this->bd->prepare("UPDATE usuarios SET pts_reino=:pts WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'pts' => 0));
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function set_clan($nome) {
  $clan = new DB_clan;
 if(!$clan->clan_existe($nome)) {return false;}
 $this->clan = $nome;
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET clan=:nome WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'nome' => $this->clan));
  // zerando os pontos que ele acumulou pelo clan
  $consulta = $this->bd->prepare("UPDATE usuarios SET pts_clan=:pts WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'pts' => 0));
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function abandonar_clan() {
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET clan = NULL WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod));
  // zerando os pontos que ele acumulou pelo clan
  $consulta = $this->bd->prepare("UPDATE usuarios SET pts_clan=:pts WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'pts' => 0));

  $consulta = $this->bd->prepare("UPDATE usuarios SET status_clan=0 WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod));

  $clan = new DB_clan;
  $clan->ler($this->clan);
  if($clan->Nduelistas() <= 0) $clan->excluir();
  elseif($this->cod == $clan->lider){
    $lista = $clan->duelistas();
    $temp = new DB;
    $clan->set_lider($temp->nome_id($lista[count($lista)-1]['nome']));
  }

  $this->clan = null;
  $this->pts_clan = 0;
  $this->status_clan = 0;
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function set_status_clan($valor) {
  $clan = new DB_clan;
 if($valor !== 0 && $valor !== 1) {return false;}
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET status_clan=:status WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod, 'status' => (int)$valor));
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function banir() {
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET status=2 WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod));
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}
function desbanir() {
 try{
  $consulta = $this->bd->prepare("UPDATE usuarios SET status=1 WHERE id = :id");
  $consulta->execute(array('id' => (int)$this->cod));
  return true;
 } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function nome_reino($reino = -1) {
  if($reino === (-1)) $reino = $this->reino;
  if((int)$reino < 0 || (int)$reino > 3) return 'Desconhecido';

  switch ((int)$reino) {
    case 1:
      return 'Obelisco';
    break;
    case 2:
      return 'Slifer';
    break;
    case 3:
      return 'Dragão de RA';
    break;
    case 0:
      return 'Neutro';
    break;
  }
}

}

// o método de decisão de ordem do ranking é esse: vitorias menos derrotas vezes 50 mais o xp isso resulta em um numero que quanto maior melhor é classificado esse jogador
function comparar($a, $b) {
  if(($a['vitorias'] - $a['derrotas'])*50 + $a['xp'] == ($b['vitorias'] - $b['derrotas'])*50 + $b['xp']) return 0;
  if(($a['vitorias'] - $a['derrotas'])*50 + $a['xp'] < ($b['vitorias'] - $b['derrotas'])*50 + $b['xp']) return (-1);
  if(($a['vitorias'] - $a['derrotas'])*50 + $a['xp'] > ($b['vitorias'] - $b['derrotas'])*50 + $b['xp']) return 1;
}

//este algoritmo vai ser responsavel por ler o banco de dados dos clans
//e interpretalo assim com tbm gravar novos registros
//algoritmo terminado em ??/01/2017

class DB_clan {
 var $bd;
 var $nome;
 var $lider;
 
 function __construct() {
   $this->erro = new Erro;
   global $G_SQL_HOST;
   global $G_SQL_BANCO;
   global $G_SQL_USER;
   global $G_SQL_SENHA;
  try {
   @$this->bd = new PDO('mysql:host='.$G_SQL_HOST.';dbname='.$G_SQL_BANCO, $G_SQL_USER, $G_SQL_SENHA);
  }
  catch(PDOException $e) { // não está capturando o erro apenas redirecionando o usuario
      header('location: dberro.php');
      exit(); // garantindo que nada mais sera feito se o banco falhar
  }
 }
 
function __destruct() {
 $this->bd = NULL; //simbolicamente conexão fechada
}

 
function ler($nome) { //seta a galera toda
 if($nome == '') {return false;} //tratar erro
 $consulta = $this->bd->prepare('SELECT * FROM clans WHERE nome = :nome'); // preparando
 $consulta->execute(array('nome' => $nome)); // executando a em cima do id do jogador
 $rs = $consulta->fetchAll(); // convertendo em um vetor
 $rs = $rs[0]; // convertendo de matriz com uma linha para vetor comum
 if(!$rs) {return false;} 
  //setar tudo
  $this->nome = $rs['nome'];
  $this->lider = $rs['lider'];
  return true;
 }
 
function criar() {
  if($this->nome == '') {$this->erro->set(8, '');}
  if($this->lider == '') {$this->erro->set(8, '');}
 
 if($this->clan_existe($this->nome)) {return false;} // previne inconsistencia no banco de dados
  try { // tudo aqui é propenço a erro
   $registro = $this->bd->prepare("INSERT INTO clans (nome, lider) VALUES (:nome, :lider)"); // preparando
   $registro->execute(array('nome' => $this->nome, 'lider' => $this->lider)); // executando a em cima do id do jogador

   $consulta = $this->bd->prepare("UPDATE usuarios SET clan='".$this->nome."' WHERE id = :id"); // preparando
   $consulta->execute(array('id' => (int)$this->lider)); // executando a em cima do nome do jogador
   return true;
   } catch (PDOException $e) {header('location: dberro.php');exit();} // capturando erro
}

function clan_existe($nome) {
 try {
  $consulta = $this->bd->prepare("SELECT * FROM clans WHERE nome = :nome");
  $consulta->execute(array('nome' => $nome));
  $array_temp = $consulta->fetchAll();
  $array_temp = $array_temp[0]; // convertendo de matriz com uma linha para vetor comum
  if($array_temp['nome'] == '') {return false;} // não existe esse nome no banco
  else {return true;} // existe esse nome já registrado no banco
 }
 catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function duelistas() {
  if($this->nome == '') return false;
  try {
   $consulta = $this->bd->prepare("SELECT nome, pts_clan FROM usuarios WHERE clan=:nome and status_clan=1 ORDER BY pts_clan");
   $consulta->execute(array('nome' => $this->nome));
   $vetor = $consulta->fetchAll(); // retornando a tabela inteira
   if(count($vetor) < 1) return false;
   return $vetor;
  } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function pendentes() {
  if($this->nome == '') return false;
  try {
   $consulta = $this->bd->prepare("SELECT nome FROM usuarios WHERE clan=:nome and status_clan=0");
   $consulta->execute(array('nome' => $this->nome));
   $vetor = $consulta->fetchAll(); // retornando a tabela inteira
   if(count($vetor) < 1) return false;
   return $vetor;
  } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function set_lider($nome) {
  if($this->nome == '') return false;
  try {
   $consulta = $this->bd->prepare("UPDATE clans SET lider=:lider WHERE nome=:nome");
   $consulta->execute(array('nome' => $this->nome, 'lider' => $nome));
   return true;
  } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function Nduelistas() {
  $lista = $this->duelistas();
  if($lista === false) return 0;
  return count($lista);
}
function Npendentes() {
  $lista = $this->pendentes();
  if($lista === false) return 0;
  return count($lista);
}

function total_pts() {
  $lista = $this->duelistas();
  $soma = 0;
  for($loop = 0; $loop < count($lista);$loop++)
    $soma += $lista[$loop]['pts_clan'];
  return $soma;
}

function excluir() {
  if($this->nome == '') return false;
  $requisitar = $this->bd->prepare("UPDATE usuarios SET clan = NULL, pts_clan=0, status_clan=0 WHERE clan=:clan");
  $requisitar->execute(array('clan' => $this->nome));

  $requisitar = $this->bd->prepare("DELETE FROM clans WHERE nome=:nome");
  $requisitar->execute(array('nome' => $this->nome));
  return $soma;
}

function tabela_clans() {
  try {
   $consulta = $this->bd->prepare("SELECT * FROM clans");
   $consulta->execute();
   return $consulta->fetchAll(); // retornando a tabela inteira
  } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

function ranking() { // essa função retorna um array com ids por ordem de xp e V/D
  $maximo = 100; // esse é o numero de clans que vai aparecer no ranking
  try {
   $consulta = $this->bd->prepare("SELECT * FROM clans");
   $consulta->execute();
   $matriz = $consulta->fetchAll(); // retornando a tabela inteira
   
   usort($matriz, "comparar_clans"); // ordenando todos os usuários

   $tamanho = count($matriz);
   for($x = 0;$x < $maximo && $x < $tamanho; $x++)
    $retorno[$x] = $matriz[$tamanho-1 - $x]['nome'];
   
   return $retorno;
  } catch(PDOException $e) {header('location: dberro.php'); return false;}
}

}

function comparar_clans($a, $b) {
  $banco = new DB_clan;

  $banco->ler($a['nome']);
  $pts_a = $banco->total_pts();

  $banco->ler($b['nome']);
  $pts_b = $banco->total_pts();

  if($pts_a === $pts_b) return 0;
  if($pts_a < $pts_b) return (-1);
  if($pts_a > $pts_b) return 1;
}
?>