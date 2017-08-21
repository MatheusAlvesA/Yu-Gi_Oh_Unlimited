<?php
class c_278 extends Magica {
 // carta terminada em 08/06/2017. criada em 1 dia
 /*Adicione 1 monstro Dinosaur-Type de Nível 6 ou menor do seu Deck para a sua mão.
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
  $lista = array();
  $carta = new DB_cards;
  $y = 0;
  for($x = 1; $x < $deck[0]; $x++) {
    $carta->ler_id($deck[$x]);
    if($carta->specie == 'dinosaur' && $carta->lv <= 6) {
        $lista[$y] = (int)$deck[$x];
        $y++;
    }
  }
  
  if(count($lista) <= 0) {
      parent::avisar('Você não tem dragões LV 6 ou menor em seu deck para ativar esse efeito.');
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
         parent::mudar_modo(MODOS::ATAQUE_BAIXO);
         return false;
     }
     
     $this->mudar_modo(MODOS::ATAQUE);
     parent::avisar('Efeito da carta '.$this->nome.' ativado.', 1);
     parent::excluir_carta_deck('id', $lista[$cartaS]);
     parent::colocar_hand($lista[$cartaS], $this->dono);

     parent::destruir();
     return true;
 }

}
?>