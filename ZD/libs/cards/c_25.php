<?php

// Special invoque 1 monstro normal de Level 5 ou maior da sua mão.

class c_25 extends Magica {
 function ativar_efeito() {  // unica função dessa mágica
  if(parent::ler_variavel('carta_solicitada') != 0) {
   $campo = $this->duelo->ler_campo($this->dono);
  for($x = 1; $campo[$x][1] != 0 && $x <= 5; $x++) {}
   if($x > 5) {
    parent::avisar('Não existe espaço para invocar a carta');
    $this->tarefa('0');
    return false;
  }
   parent::avisar('Efeito da carta '.$this->nome.' ativado', 1);
   if($this->duelo->invocar($this->dono, $x, 1, parent::ler_variavel('carta_solicitada'), 'especial') === 1) {
    parent::excluir_carta_hand('id', parent::ler_variavel('carta_solicitada'));
    $this->tarefa('0');
   }
   return true;
  }
  else {
   if(!parent::checar_ativar()) {return false;}
   $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 0); // agendar tarefa de destruir caso não faça nada
   $hand = $this->duelo->ler_mao($this->dono);
   $carta = new DB_cards();
   $y = 0;
   for($x = 1; $x < $hand[0]; $x++) {
       $carta->ler_id($hand[$x]);
       if($carta->categoria == 'monster' && $carta->lv >= 5) {
           $lista[$y] = $hand[$x];
           $y++;
       }
    }
    if(!isset($lista)) {
     parent::avisar('Você não possui monstros na sua mão');
     parent::destruir();
     return false;
    }
    $this->duelo->solicitar_carta('Escolha um monstro', $lista, $this->dono, $this->inst);
    $this->mudar_modo(1);
    return true;
  }
 }
 
 function carta_solicitada($cartaS) {
  $hand = $this->duelo->ler_mao($this->dono);
  $carta = new DB_cards();
  $y = 0;
   for($x = 1; $x < $hand[0]; $x++) {
       $carta->ler_id($hand[$x]);
       if($carta->categoria == 'monster' && $carta->lv >= 5) {
           $lista[$y] = $hand[$x];
           $y++;
       }
    }
  if(!isset($lista)) {
   parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
   parent::destruir();
   return false;
  }
  if(!isset($lista[$cartaS])) {$this->tarefa('0');return false;}
  parent::manter('carta_solicitada', $lista[$cartaS]);
  $this->ativar_efeito();
}

function tarefa($txt) { //essa carta deve se destruir caso seja ativada e round passe
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     parent::destruir();
     return false;
 }
 
}
?>