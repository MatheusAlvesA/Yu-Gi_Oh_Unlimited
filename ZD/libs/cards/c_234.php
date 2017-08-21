<?php

class c_234 extends Monstro_normal {
/* carta terminada dem 11/05/2017 terminada em 1 dia
 * Uma vez por turno, durante sua Standby Phase, adicione 1 (Polimerização) de seu Deck ou Cemitério para a sua mão.
*/

 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
     $r = parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
     if($r) $this->duelo->agendar_tarefa($this->inst, $dono, 'sb_phase', 'x');
     return $r;
 }
 
 function tarefa($txt) {
     $cmt = $this->duelo->ler_cmt($this->dono);
     for($x = 1; $x < $cmt[0]; $x++) {
         if($cmt[$x] == 561) {
             $this->duelo->apagar_cmt(561, $this->dono);
             $this->duelo->colocar_carta_hand(561, $this->dono);
             parent::avisar('Efeito da carta '.$this->nome.' ativado',1);
             return true;
         }
     }
     $deck = $this->duelo->ler_deck($this->dono);
     for($x = 1; $x < $deck[0]; $x++) {
         if($deck[$x] == 561) {
             $this->duelo->apagar_carta_deck($this->dono, $x);
             $this->duelo->colocar_carta_hand(561, $this->dono);
             parent::avisar('Efeito da carta '.$this->nome.' ativado',1);
             return true;
         }
     }
     return false;
 }
 
}