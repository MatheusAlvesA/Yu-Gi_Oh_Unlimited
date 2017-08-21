<?php
// este algoritmo vai funcionar como uma rodoviária
// mandando o usuario para onde ele deve estar
// a partir da leitura de sua variável session associada
include("../libs/tools_lib.php");
include("../libs/db_lib.php");
include("libs/loby_lib.php");
$tool = new Tools(true);
$tool->verificar();

session_start();
if($_SESSION["id"] == '') {header("location: ../logout.php"); exit();}
if($_SESSION['duelando'] != '') {header("location: duelo.php"); exit();}
 if($_GET['sair'] != '') {
 $temp = new Online(); //retirar usuario da lista
 $array = $temp->onlines();

  $x = 1;
  while($_SESSION['id'] != $array[$x][0] && $x < $array[0][0]) {$x++;}
  if($x >= $array[0][0]) {$_SESSION['ZD'] = ''; header("location: ../home.php"); exit();}

 if($array[$x][2] == 'S') {
	 $temp->out($_SESSION['id']);
  	unset($array);
	  unset($temp);
   $_SESSION['ZD'] = '';
   if($_GET['dberro'] != '') { // a saida está acontecendo por uma falha no banco de dados
    header("location: ../dberro.php");
    exit();
   }
   header("location: ../home.php"); 
   exit();
  }
 else {
  header("location: loby.php");
  exit();
  }
}

$temp = new Online();
$array_temp = $temp->onlines();
if($array_temp[0][0] >= 51) {header("location: ../home.php?serverl=1"); exit();}
unset($array_temp);
unset($temp);

$banco_temp = new DB();
$banco_temp->ler($_SESSION['id']);
if($banco_temp->deck[0] < 41) {
	header("location: ../deck.php?incompleto=1");
  exit();
}
	else {
$_SESSION['ZD'] = 1;
header("location: loby.php");
}
unset($banco_temp);
?>