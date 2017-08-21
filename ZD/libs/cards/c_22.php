<?php

/* 
 * Ative somente se você possui 9000 ou mais LPs e por pagar 2000 LPs.
 * Compre 2 cartas.
 */

class c_22 extends Magica {
 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  if($this->duelo->ler_lps($this->dono) < 9000) {
   parent::avisar('Não foi possivel ativar o efeito da carta '.$this->nome);
   parent::destruir();
   return false;
  }
  
  parent::dano_direto($this->duelo->dir_duelo.$this->dono.'/lps.txt', 2000);
  $this->duelo->puxar_carta($this->dono);
  $this->duelo->puxar_carta($this->dono);
  
  parent::avisar('Efeito da carta '.$this->nome.' foi ativado', 1);
   parent::destruir();
   return true;
 }
}
?>