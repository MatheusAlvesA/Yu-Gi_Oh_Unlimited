<?php
class c_2 extends Armadilha {
	function invocar($local, $id, $modo, $dono, $tipo, $gatilho = 'ataque_monstro') {
  	parent::invocar($local, $id, $modo, $dono, $tipo, 'ataque_monstro');
 }

 function acionar() {
  $r['bloqueado'] = false;
  $this->_ativar();
  return $r;
 }
 function _ativar() {  // unica função dessa armadilha
  if(!parent::checar_ativar()) {return false;}
  $id = parent::get_carta_hand('qualquer');
  $campo = $this->duelo->ler_campo($this->dono);
  for($x = 1; $campo[$x][1] != 0 && $x <= 5; $x++) {}
  if(!$id || $x > 5) {
   parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome, 1);
   parent::out_gatilho();
   parent::destruir();
   return false;
  }
    $monstro = new DB_cards();
    $monstro->ler_id($id);
    if($monstro->categoria != 'monster') {
     parent::avisar('A carta armadinha '.$this->nome.' selecionou aleatoriamente a carta '.$monstro->nome.' e ambas foram destruidas.', 1);
     parent::excluir_carta_hand('id', $id);
     $this->duelo->colocar_no_cemiterio($id, $this->dono);
     parent::out_gatilho();
     parent::destruir();
     return false;
  }
  parent::avisar('Efeito da carta armadilha '.$this->nome.' ativado', 1);
  $this->duelo->invocar($this->dono, $x, 1, $id, 'especial');
  parent::excluir_carta_hand('id', $id);
   parent::out_gatilho();
   parent::destruir();
  return true;
 }
}
?>