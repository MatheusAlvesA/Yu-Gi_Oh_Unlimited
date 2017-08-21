<?php
class c_199 extends Magica {
 // carta terminada em 20/04/2017. criada em 1 dia
 /*Selecione 1 carta do seu Deck e remova-a do jogo virada para baixo (sem revelar).
  *Durante a sua 2° Standby Phase após a ativação, destrua esta carta e adicione a carta removida para a sua mão./
  */

 function ativar_efeito() {  // unica função dessa mágica
  if(parent::ler_variavel('encapsulada') != 0) {
      parent::avisar('Esse efeito já foi ativado');
      return false;
  }
  if(!parent::checar_ativar()) {return false;}
  	$gatilho[0] = 'magica';
	$gatilho[1] = 'efeito';
        $gatilho[1] = 'invocar';
	if(parent::checar($gatilho)) {
		 $carta = &parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		  if($resposta['bloqueado']) {
                      return false;
                  }
	}

  $deck = $this->duelo->ler_deck($this->dono);
  $lista = array();
  for($x = 1; $x < $deck[0]; $x++) {
    $lista[$x-1] = (int)$deck[$x];
  }
  
  if(count($lista) <= 0) {
      parent::avisar('Você não tem cartas em seu deck para ativar esse efeito.');
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
     parent::manter('encapsulada', $lista[$cartaS]);
     parent::manter('ativado_em', parent::ler_turno());
     $this->duelo->agendar_tarefa($this->inst, $this->dono, 'sb_phase', 'x');
     
     return true;
 }
 
 function tarefa($x) {
     if((int)parent::ler_turno()-(int)parent::ler_variavel('ativado_em') >= 4) {
         parent::colocar_hand(parent::ler_variavel('encapsulada'), $this->dono);
         parent::avisar('Efeito da '.$this->nome.' concluido', 1);
         parent::destruir();
     }
 }
}
?>