<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$db = new DB();
if(!$tools->verificarstr($_GET["nome"])) {header("location: usererro.php");exit();}
$id = $db->nome_id($_GET["nome"]);
if(!$id) {header("location: usererro.php");exit();}
if($id == $_SESSION["id"]) {header("location: perfil.php");exit();}
if(amigo($id)) {header("location: user.php?nome=".$_GET["nome"]);exit();} // verificar se ja e amigo
// tudo verificado
unset($db);
$db = new DB();
 $db->ler($_SESSION["id"]);
 $db->set_amigos($id, 1);
 header("location: user.php?nome=".$_GET["nome"]);
// amigo adicionado

 function amigo($id) {
  $bd = new DB();
  $bd->ler($_SESSION["id"]);
  $x = 1;
  while($x < $bd->amigos[0]) {
   if($bd->amigos[$x] == $id) { unset($bd); return 1;} // e amigo
   $x = $x + 1;
  }
 unset($bd);
  return 0; // nao e amigo
 }
?>