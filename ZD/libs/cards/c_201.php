<?php
class c_201 extends Armadilha {
    /* Carta terminada em 20/04/2017, terminada em 1 dia
     * Quando o oponente declara um ataque: Selecione aquele monstro, Remova do jogo o monstro selecionado.
     */
function invocar($local, $id, $modo, $dono, $tipo, $gatilho = 'ataque_monstro') {
  	parent::invocar($local, $id, MODOS::ATAQUE_BAIXO, $dono, $tipo, 'ataque_monstro');
 }
 function ativar_efeito() {parent::avisar('Esse efeito só pode ser ativado quando um monstro do oponente atacar');}
 
function acionar($gatilho, $atacante) {
  $r['bloqueado'] = true;
 
  parent::avisar('Efeito da '.$this->nome.' ativado!',1);
  parent::mudar_modo(MODOS::ATAQUE);
  $efeito[0] = 'remover_do_jogo';
  $atacante->sofrer_efeito($efeito, $this);
  parent::out_gatilho();
  parent::destruir();
  
  return $r;
 }
 
}
?>