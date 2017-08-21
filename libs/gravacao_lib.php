<?php
require_once("erro/erro_lib.php");
// Algoritmo que escreve e le os bancos dados
// escrito em 5 dias
// terminado dia 01-09-2014

 class Gravacao {
 var $caminho; // string com o caminho do arquivo
 var $array; //texto para ser escrito ou lido
 var $matriz; //caso tenha mais deuma linha
 var $multi; //armasena se sao mais de uma linha
 var $erro; // armasena objeto para tratar erros

  function __construct() {$this->erro = new Erro();}
 
  function set_caminho($x) {$this->caminho = $x;} //recebendo caminho do arquivo
  function set_array($x) { //caso seja array
   $this->array = $x;
   $this->multi = 0;
   }
  function set_matriz($x) { //caso seja matriz
   $this->matriz = $x;
   $this->multi = 1;
  }

  function apagar($linha) {
   if($this->multi == 1) {$this->erro->set(1,'');} // executou funcao errada
   if($linha == 0) {$this->erro->set(2,'');} // nao pode apagar isso
   if($linha >= count($this->array)) {$this->erro->set(2,'');}
   if($this->array[0] == '') {$this->erro->set(3, '');}

  while($linha < $this->array[0]-1) {
   $this->array[$linha] = $this->array[$linha+1];
   $linha = $linha + 1;
  }
  unset($this->array[$this->array[0]-1]); //apaga linha extra
  $this->array[0] = $this->array[0] - 1;
 }

  function apagarm($a, $b) { // caso seja matriz
   if($this->multi == 0) {$this->erro->set(1,'');} // executou funcao errada
   if($a == 0) {$this->erro->set(2,'');} // nao pode apagar isso
   if($a >= $this->matriz[0][0] || $b >= $this->matriz[0][$a]) {$this->erro->set(2,'');}
   if($this->matriz[0][0] == '') {$this->erro->set(3, '');}

  while($b < $this->matriz[0][$b]) {
   $this->matriz[$a][$b] = $this->matriz[$a][$b+1];
   $b = $b + 1;
  }
  unset($this->matriz[$a][$this->matriz[0][$a]-1]); //apaga linha extra
  $this->matriz[0][$a] = $this->matriz[0][$a] - 1;
  }

  function trocar($a, $b) {
   if($this->multi == 1) {$this->erro->set(1,'');} // tratar erro
   if($this->array[0] == '') {$this->erro->set(3, '');}
   if($a >= count($this->array) || $b >= count($this->array)) {$this->erro->set(2, '');}
   if($a == 0 || $b == 0) {$this->erro->set(2,'');}
   
   $bk = $this->array[$a];
   $this->array[$a] = $this->array[$b];
   $this->array[$b] = $bk;
  }

  function trocarm($a, $b, $c, $d) { // caso seja matriz
   if($this->multi == 0) {$this->erro->set(1,'');} // tratar erro
   if($this->matriz[0][0] == '') {$this->erro->set(3, '');}
   if($a >= $this->matriz[0][0] || $b >= $this->matriz[0][$a]) {$this->erro->set(2,'');}
   if($a == 0 || $b == 0) {$this->erro->set(2, '');}
   if($c >= $this->matriz[0][0] || $d >= $this->matriz[0][$c]) {$this->erro->set(2,'');}
   if($c == 0 || $d == 0) {$this->erro->set(2,'');}
   
   $bk = $this->matriz[$a][$b];
   $this->matriz[$a][$b] = $this->matriz[$c][$d];
   $this->matriz[$c][$d] = $bk;   
  }

  function ler($m) {
   if($this->caminho == '') {$erro->set(4,'');} // executou sem informar

   $arquivo = fopen($this->caminho, 'r');
   if($arquivo == '') {$this->erro->set(5, $this->caminho);} // tratar erro
   $x = 0;
   $retorno[0] = 'x';
   while($retorno[$x] != '') {
   $x = $x + 1;
   $retorno[$x] = fgets($arquivo);
    }
   fclose($arquivo);
   unset($retorno[$x]);
   $retorno[0] = count($retorno);
   // segunda fase corrigir bug de nova linha
   $y = 1;
   while($y < $retorno[0]-1) { // -1 pois a ultima linha não tem quebra de linha a ser removida
   $retorno[$y] = substr($retorno[$y], 0, -1);
   $y = $y + 1;
   } // bug corrigido

   if($m == 1) { // caso seja uma matriz
   $x = 1;
   while($x < $retorno[0]) {
    $mretorno[$x] = explode(';', $retorno[$x]);
    $mretorno[0][$x] = count($mretorno[$x]);
    $x = $x + 1;
     }
     $mretorno[0][0] = $retorno[0];
     return $mretorno;
   }

   return $retorno;
  }

  function escrever($linha, $n) {
    if($this->multi == 1) {$this->erro->set(1,'');}
    if($this->array[0] == '') {$this->erro->set(3, '');} // tratamento de erro
    if($linha == '') {$this->erro->set(2,'');}
    if($n >= $this->array[0]) {$this->erro->set(2,'');}
    if($n == '' || $n == 0) {$this->erro->set(2,'');}
    
   $bk = $this->array[$n];
   $this->array[$n] = $linha;
   $x = $this->array[0] - 1;
   while($x > $n) {
    $this->array[$x+1] = $this->array[$x];
    $x = $x - 1;
   }
   $this->array[$x+1] = $bk;
   $this->array[0] = count($this->array);
  }

  function escreverm($linha, $a, $b) { // caso seja matriz
    if($this->multi == 0) {$this->erro->set(1,'');}
    if($this->matriz[0][0] == '') {$this->erro->set(3, '');} // tratamento de erro
    if($linha == '') {$this->erro->set(2,'');}
    if($a >= $this->matriz[0][0]) {$this->erro->set(2,'');}
    if($a == '' || $a == 0) {$this->erro->set(2,'');}
    if($b >= $this->matriz[0][$a]) {$this->erro->set(2,'');}
     
   $bk = $this->matriz[$a][$b];
   $this->matriz[$a][$b] = $linha;
   $x = $this->matriz[0][$a] - 1;
   while($x > $b) {
    $this->matriz[$a][$x+1] = $this->matriz[$a][$x];
    $x = $x - 1;
   }
   $this->matriz[$a][$x+1] = $bk;
   $this->matriz[0][$a] = count($this->matriz[$a]);
  }
 
  function gravar() { //gravar caso seja apenas uma linha

   if($this->array[0] == '' && $this->matriz[0][0] == '') {$this->erro->set(3, '');} // tratar erro
   if($this->caminho == '') {$this->erro->set(4,'');}

   if($this->multi == 1) { // converter em array
    $loop = 1;
    $loop2 = 0;
    $this->array[0] = $this->matriz[0][0];
    while($loop < $this->matriz[0][0]) {
      $loop2 = 0;
      while($loop2 < $this->matriz[0][$loop]) {
       $this->array[$loop] = $this->array[$loop].$this->matriz[$loop][$loop2].';';
       $loop2 = $loop2 + 1;
      }
     $this->array[$loop] = substr($this->array[$loop], 0, -1);
     $loop = $loop + 1;
    }
   }

   $arquivo = fopen($this->caminho, 'w'); //esvasiando
   if($arquivo == '') {$this->erro->set(5, $caminho);} //tratar caso nao abra
   fwrite($arquivo, '');
   fclose($arquivo);

   $arquivo = fopen($this->caminho, 'a');
    if($arquivo == '') {$this->erro->set(5, $caminho);} //tratar caso nao abra
   $x = 1;
    while($x < $this->array[0]) { //loop de gravacao
     $txt = $this->array[$x]."\n";
     if($x == $this->array[0]-1) {$txt = $this->array[$x];}
     fwrite($arquivo, $txt);
     $x = $x + 1;
    }
   fclose($arquivo);
  }
}
?>