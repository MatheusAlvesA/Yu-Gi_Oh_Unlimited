<?php
//este algoritmo vai ser responsavel por ler os dados de uma carta qualquer
//algoritmo terminado em 31/03/2015
//terminado em 2 dias
include_once 'libs/gravacao_lib.php';
class DB_cards {
 var $nome;
 var $id;
 var $categoria;
 var $atributo;
 var $tipo;
 var $specie;
 var $maximo;
 var $atk;
 var $def;
 var $preco;
 var $lv;
 var $descricao;
 var $img;
 var $bd;

 var $gravar;
 function __construct() {
  $this->gravar = new Gravacao();
  $this->gravar->set_caminho('bd_cards.txt');
  $this->bd = $this->gravar->ler(1);
  unset($this->gravar);
 }

 function ler($nome) { //seta a galera toda
  $x = 1;
	 while($x < $this->bd[0][0] && !$this->str_igual($nome, $this->bd[$x][2])) {$x++;}
  if($x >= $this->bd[0][0]) {return 0;}
  //comecando a setar tudo
  $this->id = $this->bd[$x][0];
  $this->maximo = $this->bd[$x][1];
  $this->nome = $this->bd[$x][2];
  $this->categoria = $this->bd[$x][3];
  $this->tipo = $this->bd[$x][4];
  $this->atributo = $this->bd[$x][5];
  $this->specie = $this->bd[$x][6];
  $this->lv = $this->bd[$x][7];
  $this->atk = $this->bd[$x][8];
  $this->def = $this->bd[$x][9];
  $this->preco = $this->bd[$x][10];
  $this->descricao = $this->bd[$x][11];
  $this->img = 'imgs/cards/'.$this->id.'.png';
return 1;
 }

 function ler_id($id) { //seta a galera toda
  $x = (int)$id;
  if($x >= $this->bd[0][0] || $x <= 0) {return 0;}
  //comecando a setar tudo
  $this->id = $this->bd[$x][0];
  $this->maximo = $this->bd[$x][1];
  $this->nome = $this->bd[$x][2];
  $this->categoria = $this->bd[$x][3];
  $this->tipo = $this->bd[$x][4];
  $this->atributo = $this->bd[$x][5];
  $this->specie = $this->bd[$x][6];
  $this->lv = $this->bd[$x][7];
  $this->atk = $this->bd[$x][8];
  $this->def = $this->bd[$x][9];
  $this->preco = $this->bd[$x][10];
  $this->descricao = $this->bd[$x][11];
  $this->img = 'imgs/cards/'.$this->id.'.png';
return 1;
 }

function filtro() {
 if($this->tipo == 'fusion-effect' || $this->tipo == 'fusion' || $this->categoria != 'monster' || $this->tipo == 'effect' || $this->tipo == 'ritual' || $this->tipo == 'ritual-effect') {
     if($this->categoria != 'monster' || $this->tipo == 'effect' || $this->tipo == 'ritual' || $this->tipo == 'ritual-effect') {
         if(file_exists('ZD/libs/cards/c_'.$this->id.'.php')) {return true;}
     }
     return false;
 }
 else {return true;}
}

function str_igual($str1, $str2) {
 $str1 = strtoupper($str1);
 $str2 = strtoupper($str2);
 if($str1 == $str2) {return 1;} // strings perfeitamente iguais
 else {return 0;}  // se chegou aqui entao nao e parecido
}
}
?>