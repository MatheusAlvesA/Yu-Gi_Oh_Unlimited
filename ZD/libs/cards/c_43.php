<?php

/* 
 * Se o seu oponente controla 3 ou mais monstros, eles não podem declarar um ataque.
 */

class c_43 extends Magica {

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar() || parent::ler_variavel('ativado_em') != 0) {return false;}
        $gatilho[0] = 'monstro';
	$gatilho[1] = 'ataque';
	$gatilho[2] = 'tudo';
	$gatilho[3] = $this->dono.'-'.$this->inst.'-oponente';
	parent::set_gatilho($gatilho);
   $this->mudar_modo(1);
   parent::avisar('A carta mágica '.$this->nome.' foi ativada', 1);
   parent::manter('ativado_em', parent::ler_turno());
   return true;
 }
 
  function acionar($gatilho, $monstro) {
  $r['bloqueado'] = false;
  if($this->_checar()) {
      $r['bloqueado'] = true;
      parent::avisar($monstro->nome.' bloqueado pelo efeito da '.$this->nome, 1);
  }
  return $r;
 }
 
 private function _checar() {
     $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
     $quantos = 0;
     for($x = 1; $x <= 5; $x++) {if($campo[$x][1] != 0) {$quantos++;}}
     if($quantos >= 3) {return true;}
     else {return false;}
     }
     
     function destruir($motivo = 0) {
         parent::out_gatilho();
         return parent::destruir($motivo);
     }
}
?>