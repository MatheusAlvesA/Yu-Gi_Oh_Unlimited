<?php
class c_108 extends Magica {
/* carta terminada dia 19/09/2016. Feita em 1 dia
 * Ative por remover do jogo 1 monstro (Blackwing) da sua mão. Compre 2 cartas.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 0); // agendar tarefa de destruir caso não faça nada
  //solicitar uma carta blackwind ao usuario se tudo der certo puxar as duas cartas
       $hand = $this->duelo->ler_mao($this->dono);
       $y = 0;
       for($x = 1; $x <= $hand[0]; $x++) {
           if($hand[$x] >= 79 && $hand[$x] <= 85) {
               $cartas[$y] = $hand[$x]; $y++;
           }
       }
       if(!isset($cartas)) {
           parent::avisar('Você não tem monstros BlackWind na mão');
           $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
           return false;
       }
       
           $this->duelo->solicitar_carta('Sacrificar monstro', $cartas, $this->dono, $this->inst);
           $this->mudar_modo(1);
           return true;
 }
 
 function carta_solicitada($cartaS) {
       $hand = $this->duelo->ler_mao($this->dono);
       $y = 0;
       for($x = 1; $x <= $hand[0]; $x++) {
           if($hand[$x] >= 79 && $hand[$x] <= 85) {
               $cartas[$y] = $hand[$x]; $y++;
           }
       }
       if(!isset($cartas) || !$cartas[$cartaS]) {
           $this->tarefa('x');
           return false;
       }
       
       parent::excluir_carta_hand('id', $cartas[$cartaS]);
       $this->duelo->puxar_carta($this->dono);
       $this->duelo->puxar_carta($this->dono);
       $this->tarefa('x');
 }
 
 function tarefa($txt) { //essa carta deve se destruir caso seja ativada e round passe
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     parent::destruir();
 }
}
?>