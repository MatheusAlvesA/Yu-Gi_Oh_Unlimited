<?php

/* terminado em 11/02/2017; em 3 dias
 * Durante o turno que essa carta for ativada,
 * se um monstro do seu oponente batalha com um monstro (Constellar),
 * e o monstro do oponente não for destruído, em batalha
 * o monstro volta pro deck do seu oponente após o cálculo de dano.
 */

class c_131 extends Armadilha {

function invocar($local, $id, $modo, $dono, $tipo, $gatilho = 'ataque_monstro') {
    return parent::invocar($local, $id, $modo, $dono, $tipo, 'ataque_monstro');
}

 function acionar($gatilho) {
  $r['bloqueado'] = false;
  $monstro = $this->duelo->regenerar_instancia($gatilho[4], $this->dono);
  if($this->isConstellar($monstro->id)) $this->ativar_efeito();
  return $r;
 }
    
 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) return false;
  if(parent::ler_variavel('invocado_em') == parent::ler_turno()) {
      parent::avisar('Você não pode ativar essa carta no mesmo turno que a colocou em campo');
      return false;
  }
  if(file_exists($this->pasta.'131_efeito.txt')) {
      parent::avisar('O efeito já está ativo');
      return false;
  }
  parent::mudar_modo(MODOS::ATAQUE);
  parent::avisar('Efeito da carta '.$this->nome.' ativado.', 1);
  file_put_contents($this->pasta.'131_efeito.txt', $this->inst); // avisando aos constelares que o efeito está ativo
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'start_phase', 'x');
   return true;
 }
 
 public function retornar(&$instancia) {
     $efeito[0] = 'voltar_deck';
     $instancia->sofrer_efeito($efeito, $this);
     return true;
 }

  public function tarefa($txt) {
     $this->duelo->apagar_tarefa($this->dono, 'start_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     @unlink($this->pasta.'131_efeito.txt');
     parent::destruir();
     return true;
 }
 
  function isConstellar($id) {
     if($id >= 124 && $id <= 139 && $id != 131 && $id != 137) return true;
     else return false;
 }
}
?>