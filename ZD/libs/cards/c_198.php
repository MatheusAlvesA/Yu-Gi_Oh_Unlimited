<?php
class c_198 extends Magica {
 // carta terminada em 11/04/2017. criada em 1 dia
 /*Aumenta seus Pontos de vida por 1000 pontos.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  	$gatilho[0] = 'magica';
	$gatilho[1] = 'efeito';
        $gatilho[1] = 'aumentar_lps';
	if(parent::checar($gatilho)) {
		 $carta = &parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		  if($resposta['bloqueado']) {
                      return false;
                  }
	}

  parent::mudar_modo(MODOS::ATAQUE);
  parent::dano_direto($this->duelo->dir_duelo.$this->dono.'/lps.txt', -1000);
  parent::avisar('Efeito da carta '.$this->nome.' foi ativado, o duelista ganha 1000 de LPs', 1);
  parent::destruir();
  return true;
 }
}
?>