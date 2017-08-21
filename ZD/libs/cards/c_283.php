<?php
class c_283 extends Magica {
 // carta terminada em 08/06/2017. criada em 1 dia
 /*Adicione 1 (Polymerization) do seu Deck para a sua mão.
  */

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  	$gatilho[0] = 'magica';
	$gatilho[1] = 'efeito';
        $gatilho[1] = 'adicionar_mão';
	if(parent::checar($gatilho)) {
		 $carta = &parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		  if($resposta['bloqueado']) {
                      return false;
                  }
	}

  $deck = $this->duelo->ler_deck($this->dono);
  for($x = 1; $x < $deck[0]; $x++) {
    if($deck[$x] == 561) break;
  }
  
  if($x >= $deck[0]) {
      parent::avisar('Você não tem a carta polimerização no seu deck');
      parent::mudar_modo(MODOS::ATAQUE_BAIXO);
      return false;
  }

  parent::avisar('Efeito da carta '.$this->nome.' ativado',1);
  
  $this->duelo->colocar_carta_hand(561, $this->dono);
  $this->duelo->apagar_carta_deck($this->dono, $x);
  
  parent::destruir();
  return true;
 }

}
?>