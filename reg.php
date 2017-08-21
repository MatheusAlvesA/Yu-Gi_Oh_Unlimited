<?php
// este algoritmo e responsavel por registrar novos usuarios
//algoritmo terminado em 11/11/2014
//tempo de programacao 6 dias

include("libs/tools_lib.php");
include("libs/db_lib.php");
require_once 'libs/Mobile_Detect.php';

 testar(); // testar se esta tudo digitado certo
 $bd = new DB();
 
 $bd->nome = $_POST["name"];
 $bd->senha = $_POST["senha"];
 $bd->idade = (int)$_POST["idade"];
 $bd->sexo = $_POST["sexo"];
 $bd->email = $_POST['email'];
 $bd->reino = (int)$_POST["reino"];
 $bd->char = (int)$_POST["char"];

 // geracao automatica
 $bd->status = 1;
 $bd->xp = 100;
 $bd->pts_reino = 0;
 $bd->pts_clan = 0;
 $bd->dinheiro = 1000;

 $bd->criar();
// logando o usuario
 session_start();
 $_SESSION["logado"] = 'S';
 $_SESSION["id"] = $bd->cod;

 header("location: avisobd.php");

 function testar() {
  $bd_temp = new DB();
  $var = new Tools();
  if($_POST["name"] == '') {
      header("location: index.php?erro=1&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();
  }
  if($_POST["senha"] == '' || $_POST["senhav"] == '') {header("location: index.php?erro=1&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if($_POST["idade"] == '') {header("location: index.php?erro=1&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if($_POST["sexo"] == '') {header("location: index.php?erro=1&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if($_POST["email"] == '') {header("location: index.php?erro=1&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if($_POST["reino"] == '') {header("location: index.php?erro=1&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if($_POST["char"] == '') {header("location: index.php?erro=1&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if($_POST["termos"] != 'S') {header("location: index.php?erro=12&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}

  if(strlen($_POST["name"]) < 6 || strlen($_POST["name"]) > 15) {header("location: index.php?erro=2&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if(strlen($_POST["senha"]) < 6 || strlen($_POST["senha"]) > 20) {header("location: index.php?erro=3&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if(strlen($_POST["senhav"]) < 6 || strlen($_POST["senhav"]) > 20) {header("location: index.php?erro=3&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if((int)$_POST["reino"] < 1 || (int)$_POST["reino"] > 4) {header("location: index.php?erro=1&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if((int)$_POST["reino"] == 4) $_POST["reino"] = 0; // convertendo para o valor correto
  if((int)$_POST["char"] < 1 || (int)$_POST["char"] > 10) {header("location: index.php?erro=1&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  
  $temp_array = explode('@', $_POST['email']);
  if(count($temp_array) != 2) {header("location: index.php?erro=10&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if($bd_temp->email_existe($_POST['email'])) {unset($bd_temp); header("location: index.php?erro=11&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}

  if(!$var->verificarstr($_POST["name"])) {header("location: index.php?erro=4&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if(strpos($_POST["senha"], ' ') !== false || strpos($_POST["senhav"], ' ') !== false) {header("location: index.php?erro=5&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if(!$var->verificaridade(date('Y')-$_POST["idade"])) {header("location: index.php?erro=6&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if((date('Y')-$_POST["idade"]) < 6) {header("location: index.php?erro=6&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  if($_POST["sexo"] != 'M' && $_POST["sexo"] != 'F') {header("location: index.php?erro=7");exit();}
  if($_POST["senhav"] != $_POST["senha"]) {header("location: index.php?erro=9&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
 //testar se o nome de usuario ja existe
   if($bd_temp->user_existe($_POST['name'])) {unset($bd_temp); header("location: index.php?erro=8&nome=".$_POST["name"].'&idade='.$_POST["idade"].'&sexo='.$_POST["sexo"].'&email='.$_POST["email"].'&reino='.$_POST["reino"].'&char='.$_POST["char"]);exit();}
  unset($bd_temp);
 }
?>