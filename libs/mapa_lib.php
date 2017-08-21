<?php
/*
 * Esse algoritmo gerencia todas as interações com o mapado jogo
 * algoritmo terminado em ?????
 * terminado em ??? dias
 *  */
include_once("erro/erro_lib.php");
require_once 'config.php';
class Mapa {
 var $bd;
 var $tamanho;
 
 function __construct() {
   $this->tamanho = 50; // domenção do mapa
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

public function  atualizar($x1, $y1, $x2, $y2) {
    if($x1>$x2 || $y1 > $y2 || $x1 > $this->tamanho || $x2 > $this->tamanho || $y1 > $this->tamanho || $y2 > $this->tamanho) return false; // solicitação inválida
    
    $consulta = $this->bd->prepare('SELECT * FROM mundo_1 WHERE position = :p'); // preparando
    $elementos = array();
    $indice = 1;
    for($i = $y1;$i < $y2;$i++) {
        for($j = $x1;$j < $x2;$j++) {
            $consulta->execute(array('p' => $this->posicao($j, $i))); // executando a em cima da posição
            $rs = $consulta->fetchAll(); // convertendo em uma matriz
            $valor = json_decode($rs[0]['valor'], true);
            $valor['y'] = $i-$y1;
            $valor['x'] = $j-$x1;
            $valor['char'] = 0;//inicializando
            if($valor['tem'] !== 0 && $valor['lifetime'] > time()) {
              $elementos[$indice] = $valor;
              $indice++;
            }
        }
    }
    $elementos[0] = $indice-1;
    return $elementos;
}

public function  mapear($x1, $y1, $x2, $y2) {
    if($x1>$x2 || $y1 > $y2 || $x1 > $this->tamanho || $x2 > $this->tamanho || $y1 > $this->tamanho || $y2 > $this->tamanho) return false; // solicitação inválida
    
    $consulta = $this->bd->prepare('SELECT * FROM mundo_1 WHERE position = :p'); // preparando
    $matriz = array();
    for($i = $y1;$i < $y2;$i++) {
        for($j = $x1;$j < $x2;$j++) {
            $consulta->execute(array('p' => $this->posicao($j, $i))); // executando a em cima da posição
            $rs = $consulta->fetchAll(); // convertendo em uma matriz
            $matriz[$i-$y1][$j-$x1] = json_decode($rs[0]['valor'], true); // convertendo de json pra vetor associativo
        }
    }
    return $matriz;
}
private function posicao($x, $y) {return $x + $this->tamanho*$y;}

public function spawnar($quadrante, $quem) {
  $x = 0;
  $y = 12;
  switch ($quadrante) {
    case 2:
      $x = 25;
      $y = 12;
    break;
    case 3:
      $x = 0;
      $y = 37;
    break;
    case 4:
      $x = 25;
      $y = 37;
    break;
  }
   $consulta = $this->bd->prepare('SELECT * FROM mundo_1 WHERE position = :p'); // preparando
   $requisitar = $this->bd->prepare("UPDATE mundo_1 SET valor=:v WHERE position=:p");
   // tentando expawnar na posição anterior
   session_start();
   if(isset($_SESSION['quadrante'])) {
      $bx = 0;
      $by = 0;
      switch ($_SESSION['quadrante']) {
        case 2:
          $bx = 25;
          $by = 0;
        break;
        case 3:
          $bx = 0;
          $by = 25;
        break;
        case 4:
          $bx = 25;
          $by = 25;
        break;
      }
    $consulta->execute(array('p' => $this->posicao($_SESSION['X']+$bx, $_SESSION['Y']+$by))); // executando a em cima da posição
    $rs = $consulta->fetchAll(); // convertendo em uma matriz
    $valor = json_decode($rs[0]['valor']); // convertendo de json pra vetor associativo
    if($valor->{'tem'} === $quem && $valor->{'lifetime'} > time()) {
      $this->keepAlive($quem, $_SESSION['X'], $_SESSION['Y']);
      $retorno['x'] = $_SESSION['X'];
      $retorno['y'] = $_SESSION['Y'];
      $retorno['quadrante'] = $_SESSION['quadrante'];
      return $retorno;
    }
   } else {
    $consulta->execute(array('p' => $this->posicao($x, $y))); // executando a em cima da posição
    $rs = $consulta->fetchAll(); // convertendo em uma matriz
    $valor = json_decode($rs[0]['valor']); // convertendo de json pra vetor associativo
    if($valor->{'tem'} === $quem && $valor->{'lifetime'} > time()) return false; // possivel tentativa de hack
   }
   // caso não possa ezpawnar no lugar abterior
    $matriz = array();
    for($i = $y;$i < $y+13;$i++) {
        for($j = $x;$j < $x+25;$j++) {
            $consulta->execute(array('p' => $this->posicao($j, $i))); // executando a em cima da posição
            $rs = $consulta->fetchAll(); // convertendo em uma matriz
            $valor = json_decode($rs[0]['valor']); // convertendo de json pra vetor associativo
            if(!$this->ocupado($valor)) { // encontrou um lugar para colocar
              $valor->{'tem'} = $quem;
              $valor->{'lifetime'} = time()+10; // dez segundos de tempo de vida
              $requisitar->bindValue(":p", $this->posicao($j, $i));
              $requisitar->bindValue(":v", json_encode($valor));
              $requisitar->execute();
              $retorno['x'] = $j - $x;
              $retorno['y'] = $i - ($y-12);
              $retorno['quadrante'] = $quadrante;
              return $retorno;
            }
        }
    }
    for($i = $y-12;$i < $y+25;$i++) {
        for($j = $x;$j < $x+25;$j++) {
            $consulta->execute(array('p' => $this->posicao($j, $i))); // executando a em cima da posição
            $rs = $consulta->fetchAll(); // convertendo em uma matriz
            $valor = json_decode($rs[0]['valor']); // convertendo de json pra vetor associativo
            if(!$this->ocupado($valor)) { // encontrou um lugar para colocar
              $valor->{'tem'} = $quem;
              $valor->{'lifetime'} = time()+10; // dez segundos de tempo de vida
              $requisitar->execute(array('p' => $this->posicao($j, $i), 'v' => json_encode($valor)));
              $retorno['x'] = $j - $x;
              $retorno['y'] = $i - ($y-12);
              $retorno['quadrante'] = $quadrante;
              return $retorno;
            }
        }
    }
    return false;
} 

private function ocupado($local) {
  if($local->{'eh'} != 0) return true;
  if($local->{'tem'} !== 0) {
    if($local->{'lifetime'} > time()) return true;
  }
  return false;
}

public function mover($quem, $x, $y) {
  session_start();

  // traduzindo pra posição real
  switch ($_SESSION['quadrante']) {
    case 2:
      $x += 25;
    break;
    case 3:
      $y += 25;
    break;
    case 4:
      $x += 25;
      $y += 25;
    break;
  }
  if($x >= 50 || $y >= 50 || $x < 0 || $y < 0) return false;

  // efetuando o movimento em si
   $consulta = $this->bd->prepare('SELECT * FROM mundo_1 WHERE position = :p');
   $requisitar = $this->bd->prepare("UPDATE mundo_1 SET valor=:v WHERE position=:p");
   $ox = $_SESSION['X']; // a posicao original
   $oy = $_SESSION['Y'];
   switch ($_SESSION['quadrante']) {
    case 2:
      $ox += 25;
    break;
    case 3:
      $oy += 25;
    break;
    case 4:
      $ox += 25;
      $oy += 25;
    break;
  }
  // consultando posição original
   $consulta->execute(array('p' => $this->posicao($ox, $oy)));
   $rs = $consulta->fetchAll(); // convertendo em uma matriz
   $valor_o = json_decode($rs[0]['valor']); // convertendo de json pra vetor associativo
   if(!$this->ocupado($valor_o) || $valor_o->{'tem'} !== $quem) return false; // não está ocupado ou ele não tá lá
   // consultando nova posição
   $consulta->execute(array('p' => $this->posicao($x, $y)));
   $rs = $consulta->fetchAll(); // convertendo em uma matriz
   $valor = json_decode($rs[0]['valor']); // convertendo de json pra vetor associativo
   if($this->ocupado($valor)) return false; // se tá ocupado não pode ir pra lá
   // chegando aqui então é hora de mover efetivamente
   $valor->{'lifetime'} = time() + 11;
   $valor->{'tem'} = $quem;
   // zerando posição original
   $valor_o->{'tem'} = 0;
   $valor_o->{'lifetime'} = 0;
   $requisitar->execute(array('p' => $this->posicao($ox, $oy), 'v' => json_encode($valor_o)));
   // colocando o duelista na nova posição
   $requisitar->execute(array('p' => $this->posicao($x, $y), 'v' => json_encode($valor)));

   //retornando nova posição
   $retorno['quadrante'] = 0;
   $retorno['x'] = 0;
   $retorno['y'] = 0;
   // traduzindo na posição relacional ao inves da original
   if($x >= 25) { // está na 2 ou na 4
    if($y >= 25) { // está na 4
      $retorno['quadrante'] = 4;
      $retorno['x'] = $x-25;
      $retorno['y'] = $y-25;
    }
    else { // está na 2
      $retorno['quadrante'] = 2;
      $retorno['x'] = $x-25;
      $retorno['y'] = $y;
    }
   }
   else { // está na 1 ou 3
    if($y >= 25) { // está no 3
      $retorno['quadrante'] = 3;
      $retorno['x'] = $x;
      $retorno['y'] = $y-25;
    }
    else { // está no 1
      $retorno['quadrante'] = 1;
      $retorno['x'] = $x;
      $retorno['y'] = $y;
    }
   }
   return $retorno;
}

public function keepAlive($quem) {
  $x = 0;
  $y = 0;
  switch ($_SESSION['quadrante']) {
    case 2:
      $x = 25;
      $y = 0;
    break;
    case 3:
      $x = 0;
      $y = 25;
    break;
    case 4:
      $x = 25;
      $y = 25;
    break;
  }
   $consulta = $this->bd->prepare('SELECT * FROM mundo_1 WHERE position = :p'); // preparando
   $requisitar = $this->bd->prepare("UPDATE mundo_1 SET valor=:v WHERE position=:p");
   session_start();
   if(isset($_SESSION['quadrante'])) {
    $consulta->execute(array('p' => $this->posicao($_SESSION['X']+$x, $_SESSION['Y']+$y))); // executando a em cima da posição
    $rs = $consulta->fetchAll(); // convertendo em uma matriz
    $valor = json_decode($rs[0]['valor']); // convertendo de json pra vetor associativo
    if($valor->{'tem'} !== $quem || $valor->{'lifetime'} <= time()) return false;
    $valor->{'lifetime'} = time() + 10;
    $requisitar->execute(array('p' => $this->posicao($_SESSION['X']+$x, $_SESSION['Y']+$y), 'v' => json_encode($valor)));
    return true;
   } else return false;
}

public function limpar() { // essa função remove todo lixo com lifetime vencido do mapa
   set_time_limit(0);
    $consulta = $this->bd->prepare('SELECT * FROM mundo_1 WHERE position = :p'); // preparando
    $requisitar = $this->bd->prepare("UPDATE mundo_1 SET valor=:v WHERE position=:p");
    $matriz = array();
    for($i = 0;$i < $this->tamanho;$i++) {
        for($j = 0;$j < $this->tamanho;$j++) {
            $consulta->execute(array('p' => $this->posicao($j, $i))); // executando a em cima da posição
            $rs = $consulta->fetchAll(); // convertendo em uma matriz
            $valor = json_decode($rs[0]['valor']); // convertendo de json pra vetor associativo
           // if(!$this->ocupado($valor)) { // encontrou um lugar para colocar
              $requisitar->bindValue(":p", $this->posicao($j, $i));
              $requisitar->bindValue(":v", '{"eh":0,"tem":0, "zona":0}');
              $requisitar->execute();
            //}
        }
    }
}
}
?>