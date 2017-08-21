<?php
class c_137 extends Magica {
/* carta terminada dia 18/02/2017. terminada em 1 dia
 * Selecione 2 monstros Constellar no seu Cemitério e adicione para a sua mão.
 * Você não pode conduzir sua BattlePhase no turno que você ativar esta carta.*/

function ativar_efeito() {  // Efeito desse monstro
    if(!parent::checar_ativar()) {return false;}
    if(parent::ler_variavel('ativado_em') == $this->ler_turno()) {
        parent::avisar('Esse efeito já foi ativado nesse turno');
        return false;
    }
    
    $gatilho[0] = 'magica';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'ressucitar_carta';
    $gatilho[3] = 'mão';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    
    // formando lista do cemiterio
        $cmt = $this->duelo->ler_cmt($this->dono);
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
        if($this->isConstellar($cmt[$x])) { // id de um monstro constellar
            $lista_cmt[$y] = $cmt[$x];
            $y++;
        }
    }
    
 if(!isset($lista_cmt) || count($lista_cmt) < 2) {
     parent::avisar('Não é possível ativar esse efeito sem ao menos dois constelares em seu cemitério');
     return false;
 }
    
    $this->duelo->solicitar_carta("Escolha o primeiro Constellar", $lista_cmt, $this->dono, $this->inst);
   return true;
  }
 
  function carta_solicitada($cartaS) {
  	//nesse caso pecisa guardar a carta a ser destruida e perguntar pela carta a ser ressucitada
if(parent::ler_variavel('ser_destruida') == '') {
    // formando lista do cemiterio
        $cmt = $this->duelo->ler_cmt($this->dono);
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
        if($this->isConstellar($cmt[$x])) { // id de um monstro constellar
            $lista_cmt[$y] = $cmt[$x];
            $y++;
        }
    }
    
 if(!isset($lista_cmt) || !isset($lista_cmt[$cartaS])) {
     parent::avisar('Não existem outros Constelares em seu campo ou cemitério');
     return false;
 }
 
 parent::manter('ser_destruida', $cartaS);
    // a carta a ser sacrificada está definida. hora de perguntar qual vai ser resucitada
    $y=0;
    for($x=0;$x<count($lista_cmt);$x++) {
    	if($x != $cartaS) {
    		$nova_lista[$y] = $lista_cmt[$x];
    		$y++;
    	}
    }
        $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'end');
        $this->duelo->solicitar_carta("Escolha o segundo Constellar", $nova_lista, $this->dono, $this->inst);
}
else {
        //todo agora receber o que é pra ressucitar
        $cmt = $this->duelo->ler_cmt($this->dono);
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
        if($this->isConstellar($cmt[$x])) { // id de um monstro constellar
            $lista_cmt[$y] = $cmt[$x];
            $y++;
        }
    }
    
 if(!isset($lista_cmt)) {
     parent::avisar('Não existem outros Constelares em seu cemitério');
     return false;
 }
 
     $y=0;
    for($x=0;$x<count($lista_cmt);$x++) {
    	if($x != parent::ler_variavel('ser_destruida')) {
    		$nova_lista[$y] = $lista_cmt[$x];
    		$y++;
    	}
    }
    if(!isset($nova_lista[$cartaS])) return false;
    
    // temos as duas cartas, ativando efeito
    $this->duelo->apagar_cmt($lista_cmt[parent::ler_variavel('ser_destruida')], $this->dono);
    $this->duelo->apagar_cmt($nova_lista[$cartaS], $this->dono);
    $this->duelo->colocar_carta_hand($lista_cmt[parent::ler_variavel('ser_destruida')], $this->dono);
    $this->duelo->colocar_carta_hand($nova_lista[$cartaS], $this->dono);
    
    $sacrificio = new DB_cards;
    $sacrificio->ler_id($lista_cmt[parent::ler_variavel('ser_destruida')]);
    $monstro = new DB_cards;
    $monstro->ler_id($nova_lista[$cartaS]);
    
    parent::mudar_modo(MODOS::ATAQUE);
    parent::avisar('Efeito da carta '.$this->nome.' ativado. '.$sacrificio->nome.' foi movido para a mão e '.$monstro->nome.' também. A battle Phase não poderá ser executada', 1);
    $this->duelo->agendar_tarefa($this->inst, $this->dono, 'battle_phase', 'battle');
    parent::manter('ativado_em', parent::ler_turno());
}
      return true;
  }
  
  function tarefa($txt){
      if($txt == 'battle') {
            parent::avisar('Battle Phase anulada');
            $this->duelo->battle_phase($this->dono, 1);
            parent::destruir();
      }
      else {
          parent::manter('ser_destruida', '');
          if(parent::ler_variavel('ativado_em') == parent::ler_turno()) parent::destruir ();
      }
      return true;
  }
  
   function isConstellar($id) {
     if($id >= 124 && $id <= 139 && $id != 131 && $id != 137) return true;
     else return false;
 }
    
}
?>