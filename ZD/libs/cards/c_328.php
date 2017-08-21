<?php
class c_328 extends Magica {
 // carta terminada em 15/06/2017 criada em 1 dia
 /*Inflija 500 de dano no seu oponente e ganhe 500 LPs.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  	  $gatilho[0] = 'magica';
	  $gatilho[1] = 'efeito';
	  if(parent::checar($gatilho)) {
		 $carta = &parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		  if($resposta['bloqueado']) {
                      return false;
                  }
	  }

  parent::dano_direto($this->duelo->dir_duelo.$this->duelo->oponente($this->dono).'/lps.txt', 500);
  parent::dano_direto($this->duelo->dir_duelo.$this->dono.'/lps.txt', -500);
  parent::avisar('Efeito da carta '.$this->nome.' foi ativado, 500 Lps foram roubados', 1);
  parent::destruir();
  return true;
 }
}
?>