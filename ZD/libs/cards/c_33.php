<?php

/* 
 * Durante sua Standby Phase, envie esta carta virada para cima para o cemitério
 *  para Special Summon 1 (Armed Dragon LV5) de sua mão ou Deck.
 */
class c_33 extends Monstro_normal {

 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
     parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
     $this->duelo->agendar_tarefa($this->inst, $this->dono, 'sb_phase', 0);
 }
    
 private function _ativar() {  // Efeito desse monstro
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'invocar_carta';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    $mao = $this->duelo->ler_mao($this->dono);
    $existe = false;
    for($x = 1; $x < $mao[0]; $x++) {
        if($mao[$x] == 34) { // id da carta LV5
            parent::excluir_carta_hand('id', 34);
            $existe = true;
            break;
        }
    }
    if(!$existe) {
        $deck = $this->duelo->ler_deck($this->dono);
     for($x = 1; $x < $deck[0]; $x++) {
        if($deck[$x] == 34) {
            parent::excluir_carta_deck('id', 34);
            $existe = true;
            break;
        }
     }
    }
    if($existe) {
     $campo = $this->duelo->ler_campo($this->dono);
     for($x = 1; $campo[$x][1] != 0 && $x <= 5; $x++) {}
    if($x > 5) {
     parent::avisar('Não existe espaço para invocar a carta');
     return false;
    }
     parent::avisar('Efeito do Monstro '.$this->nome.' ativado', 1);
     $this->duelo->invocar($this->dono, $x, 1, 34, 'especial'); // invocando de forma especial com sacrificio
     return true;
    }
    else {
     parent::avisar('Armed Dragon LV5 não foi encontrado na sua mão ou deck');
     return false;
    }
  }
  
function tarefa($txt) { //essa carta deve se destruir caso seja ativada e round passe
     $this->duelo->apagar_tarefa($this->dono, 'sb_phase', $this->inst);
     parent::destruir();
     $this->_ativar();
     return true;
 }
 
}
?>