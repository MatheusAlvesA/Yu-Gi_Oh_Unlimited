<?php
//este algoritmo e responsavel por enviar e ler as mensagens do usuario
//algoritmo terminado em 2/12/2014
//tempo de desenvolvimento 8 dias
include_once('libs/gravacao_lib.php');
class MSG {
  var $grav;
  var $erro;
 function __construct() {
  $this->grav = new Gravacao();
  $this->erro = new Erro();
 }
 function ler($id) {
  if($id == '') {$this->erro->set(13, '');} // tratar erro 

  if(!file_exists("msgs/".$id.".txt")) { // caso nao exista cria um vasio
   $arq = fopen("msgs/".$id.".txt", 'w');
   fwrite($arq, '');
   fclose($arq);
   }

  $this->grav->set_caminho("msgs/".$id.".txt");
  $array = $this->grav->ler(0);
  if($array[0] <= 1) {$matriz_temp[0][0] = 0;return $matriz_temp;} // caso nao tenha msgs
   $y = 1;
   $x = 0;
   $z = 0; // nem pergunte
  while($y <= $this->calcular($array)) {
   $matriz[$y][0] = $array[$x + 2];
   $matriz[$y][1] = $array[$x + 1];
   $x = $x + 2;
   $z = 2;
  while(($z - 2) < $matriz[$y][0]) {
   $matriz[$y][$z] = $array[$x+1];
   $x++;
   $z++;
  }
  $y++;
 }
 $matriz[0][0] = $this->calcular($array);
 return $matriz;
}

 function calcular($array) { // retorna o numero de mensagens
  if($array[0] <= 1) {return 0;}
  $x = $array[2];
  $x = $x + 2;
  $y = 1;
  while($y <= 20) {
  if($x == $array[0] - 1) {return $y;}
  $x = $x + $array[$x + 2] + 2;
  $y = $y + 1;
  }
 }
 
 function enviar($msg, $id, $destino) {
  if($msg == '' || $id == '') {$this->erro->set(14, '');} // tratar erro
  $array = $this->converter($msg, $id);
  $matriz= $this->ler($destino);

  if($matriz[0][0] == 20) { // deletando 20 para nao passar disso
   unset($matriz[20]);
   $matriz[0][0] = $matriz[0][0] - 1;
   }

  $x = $matriz[0][0];
  while($x > 0) {
   $matriz[$x + 1] = $matriz[$x];
   $x--;
  }
  $matriz[0][0] = $matriz[0][0] + 1;
  $matriz[1] = $array;
  
  $x = 1;
  $z = 0;
  $array_final[0] = 'x';
  while(($x - 1) < $matriz[0][0]) {
   $array_final[$z + 1] = $matriz[$x][1];
   $array_final[$z + 2] = $matriz[$x][0];
   $y = 2;
   $z = $z + 3;
   while(($y - 2) < $matriz[$x][0]) {
    $array_final[$z] = $matriz[$x][$y];
    $y++;
    $z++;
   }
   $z--;
   $x++;
  }
  $array_final[0] = count($array_final);

  $grav = new Gravacao();
  $grav->set_caminho("msgs/".$destino.".txt");
  $grav->set_array($array_final);
  $grav->gravar();
  unset($grav);
 }

 function apagar($n, $id) {
  if($n == '' || $id == '') {$this->erro->set(15, '');} // tratar erro
  $matriz= $this->ler($id);   

  while($n < $matriz[0][0]) {
   $matriz[$n] = $matriz[$n + 1];
   $n++;
  }
  unset($matriz[$matriz[0][0]]);
  $matriz[0][0] = $matriz[0][0] - 1;
  
  $x = 1;
  $z = 0;
  $array_final[0] = 'x';
  while(($x - 1) < $matriz[0][0]) {
   $array_final[$z + 1] = $matriz[$x][1];
   $array_final[$z + 2] = $matriz[$x][0];
   $y = 2;
   $z = $z + 3;
   while(($y - 2) < $matriz[$x][0]) {
    $array_final[$z] = $matriz[$x][$y];
    $y++;
    $z++;
   }
   $z--;
   $x++;
  }
  $array_final[0] = count($array_final);

  $grav = new Gravacao();
  $grav->set_caminho("msgs/".$id.".txt");
  $grav->set_array($array_final);
  $grav->gravar();
  unset($grav);
 }

 function converter($msg, $id) {
  $array = $this->explodir($msg);
  $retorno[0] = count($array);
  $retorno[1] = $id;
  
  $x = 0;
  while($x < $retorno[0]) {
   $retorno[$x + 2] = $array[$x];
   $x++;
  }

  return $retorno;
 }
 function explodir($txt) {
  $tools = new Tools();
  $x = 0;
  $y = 0;
  while($x < strlen($txt)) {
    if(substr($txt, $x, 1) != substr("a\nb",1 ,1)) {
     $array[$y] = $array[$y].substr($txt, $x, 1);
    }
    else {$y++;}
    $x++;
  }
  return $array;
 }
}
?>