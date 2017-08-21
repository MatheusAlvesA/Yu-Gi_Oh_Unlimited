<?php
class c_93 extends Magica {
 // carta terminada em 08/09/2016 criada em 1 dia
 /*Você e o seu oponente ganham 400 LPs.*/

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

  parent::dano_direto($this->duelo->dir_duelo.$this->duelo->oponente($this->dono).'/lps.txt', -400);
  parent::dano_direto($this->duelo->dir_duelo.$this->dono.'/lps.txt', -400);
  parent::avisar('Efeito da carta '.$this->nome.' foi ativado, ambos ganham 400 LPs', 1);
  parent::destruir();
  return true;
 }
}
?>