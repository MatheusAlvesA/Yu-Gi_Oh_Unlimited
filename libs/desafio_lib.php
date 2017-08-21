<?php
/*
Esse algoritmo é responsável por dar suporte aos duelos no mapa
algoritmo terminado em 14/01/2017
terminado em 1 dia
*/
include_once("erro/erro_lib.php");
require_once 'config.php';
class Desafio {
 var $bd;
 var $desafiante;
 var $desafiado;
 var $status;
 var $id_duelo;
 var $honra;
 
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
  $this->desafiante = null;
  $this->desafiado = null;
  $this->status = null;
  $this->id_duelo = null;
  $this->honra = null;
 }
 
function __destruct() {
 $this->bd = NULL; //simbolicamente conexão fechada
}

  // retorna true ou false se o duelista cujo nome foi passado está ou não disponível pra duelo
  public function ocupado($nome) {
    return $this->existe($nome);
  }
  // essa função cria um desafio
  public function criar() {
    if($this->desafiado === null || $this->desafiante === null || $this->honra === null) return false;
    $registro = $this->bd->prepare("INSERT INTO desafios_mundo_1 (desafiante, desafiado, status, id_duelo, criado, honra) VALUES (:desafiante, :desafiado, 'P', 0, ".time().", :honra)");
    $registro->execute(array('desafiante' => $this->desafiante, 'desafiado' => $this->desafiado, 'honra' => $this->honra));
    return true;
  }
  // retorna se existe um desafio para esse jogador
  public function existe($nome) {
    $consulta = $this->bd->prepare('SELECT * FROM desafios_mundo_1 WHERE desafiante = :nome'); // preparando
    $consulta->execute(array('nome' => $nome)); // executando em cima do nome do jogador
    $rs = $consulta->fetchAll(); // convertendo em um vetor
    if(count($rs) < 1) { // tentando de novo com
      $consulta = $this->bd->prepare('SELECT * FROM desafios_mundo_1 WHERE desafiado = :nome'); // preparando
      $consulta->execute(array('nome' => $nome)); // executando em cima do inome do jogador
      $rs = $consulta->fetchAll(); // convertendo em um vetor
    }
    if(count($rs) < 1) return false;
    //chegando aqui está tudo certo com o desafio
    $rs = $rs[0];
    $this->desafiante = $rs['desafiante'];
    $this->desafiado = $rs['desafiado'];
    $this->status = $rs['status'];
    $this->id_duelo = $rs['id_duelo'];
    $this->honra = $rs['honra'];
    if(time() - $rs['criado'] >= 20 && $this->status != 's') { // se o desafio não mudou em 20 segundos
      $this->remover();
      return false;
    }
    return true;
  }
  // muda o satatus para aceito para que o duelo começe
  public function aceitar() {
    if($this->desafiado === null || $this->desafiante === null) return false;
    $requisitar = $this->bd->prepare("UPDATE desafios_mundo_1 SET status='s' WHERE desafiante=:desafiante");
    $requisitar->execute(array('desafiante' => $this->desafiante));
    return true;
  }
  public function set_id_duelo($id) {
    if($this->desafiado === null || $this->desafiante === null) return false;
    $requisitar = $this->bd->prepare("UPDATE desafios_mundo_1 SET id_duelo=:id WHERE desafiante=:desafiante");
    $requisitar->execute(array('desafiante' => $this->desafiante, 'id' => $id));
    $this->id_duelo = $id;
    return true;
  }
  // apaga um desafio que já está setado
  public function remover() {
    if($this->desafiado === null || $this->desafiante === null) return false;
    $requisitar = $this->bd->prepare("DELETE FROM desafios_mundo_1 WHERE desafiante=:desafiante");
    $requisitar->execute(array('desafiante' => $this->desafiante));
    return true;
  }
}
?>