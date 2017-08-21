<?php
class c_217 extends Armadilha {
    /* Carta terminada em 04/05/2017, terminada em 1 dia
     * Negue o ataque de 1 monstro do seu oponente e ganhe Pontos de Vida iguais ao ATK do monstro.
     */
function invocar($local, $id, $modo, $dono, $tipo, $gatilho = 'ataque_monstro') {
  	parent::invocar($local, $id, MODOS::ATAQUE_BAIXO, $dono, $tipo, 'ataque_monstro');
 }
 function ativar_efeito() {parent::avisar('Esse efeito só pode ser ativado quando um monstro do oponente atacar');}
 
function acionar($gatilho, $atacante) {
  $r['bloqueado'] = true;
 
  parent::avisar('Efeito da '.$this->nome.' ativado!',1);
  parent::mudar_modo(MODOS::ATAQUE);
  $this->duelo->alterar_lp($atacante->atk, $this->dono);
  
  parent::out_gatilho();
  parent::destruir();
  return $r;
 }
 
}
?>