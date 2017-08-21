<?php
class c_274 extends Magica {
 // carta terminada em 01/06/2017. criada em 1 dia
 /*Selecione 1 monstro do seu Deck e o envie para o Cemitério.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  	$gatilho[0] = 'magica';
	$gatilho[1] = 'efeito';
        $gatilho[1] = 'mover_cemiterio';
	if(parent::checar($gatilho)) {
		 $carta = &parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		  if($resposta['bloqueado']) {
                      return false;
                  }
	}

  $deck = $this->duelo->ler_deck($this->dono);
  $lista = array();
  $carta = new DB_cards;
  $y = 0;
  for($x = 1; $x < $deck[0]; $x++) {
    $carta->ler_id($deck[$x]);
    if($carta->categoria == 'monster') {
        $lista[$y] = (int)$deck[$x];
        $y++;
    }
  }
  
  if(count($lista) <= 0) {
      parent::avisar('Você não tem monstros em seu deck para ativar esse efeito.');
      return false;
  }
  shuffle($lista); // impedir que o duelista saiba a ordem das cartas em seu deck
  parent::manter('lista', json_encode($lista));
  $this->duelo->solicitar_carta('Escolha uma carta', $lista, $this->dono, $this->inst);
  return true;
 }
 
 function carta_solicitada($cartaS) {
     $lista = json_decode(parent::ler_variavel('lista'));
     if(count($lista) <= 0 || !isset($lista[$cartaS])) {
         parent::avisar('Erro ao ativar o efeito da '.$this->nome);
         parent::destruir();
         return false;
     }
     
     $this->mudar_modo(MODOS::ATAQUE);
     parent::avisar('Efeito da carta '.$this->nome.' ativado.', 1);
     parent::excluir_carta_deck('id', $lista[$cartaS]);
     $this->duelo->colocar_no_cemiterio($lista[$cartaS], $this->dono);
     
     parent::destruir();
     return true;
 }
 

}
?>