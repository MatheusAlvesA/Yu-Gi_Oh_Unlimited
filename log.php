<?php
// este algoritmo e responsavel por logar o usuario
// algoritmo terminado em 13/11/2014
// modifcado para funcionar com a nova biblioteca do Banco de dados em 29-04-2015
include_once("libs/tools_lib.php");
include_once("libs/db_lib.php");
require_once 'libs/Mobile_Detect.php';

$local = 'index'; // retorna ara index em caso de erro
if($_GET["referencia"] === 'sobre') $local = 'sobre'; // retorna para sobre

  if($_POST["nome"] == '' || $_POST["senha"] == '') {header("location: $local.php?erro=13");exit();}
  $bd_temp = new DB();
  $var = new Tools();

  if(!$var->verificarstr($_POST["nome"])) {header("location: $local.php?erro=14");exit();}
  if(strpos($_POST["senha"], ' ') !== false) {header("location: $local.php?erro=14");exit();}
 //testar se o nome de usuario existe
  if(!$bd_temp->user_existe($_POST['nome'])) {header("location: $local.php?erro=14");exit();}
 $id = $bd_temp->nome_id($_POST['nome']);
 unset($bd_temp);
 // peguou o id agora vai verificar a senha
 $db = new DB();
 $db->ler($id);
  if(sha1($_POST["senha"]) == $db->senha) { // senha confere
   if($db->status == 2) {header("location: block.php");exit();} // impedir usuario bloqueado
   session_start();
   $_SESSION["id"] = $id;
   $_SESSION["logado"] = 'S';
   $db->acessada(); // atualiza o ultimo acesso
   header("location: home.php");
  }
 else {header("location: $local.php?erro=14");}
?>