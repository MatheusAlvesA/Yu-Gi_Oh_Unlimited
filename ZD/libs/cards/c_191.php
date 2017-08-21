<?php
class c_191 extends Armadilha {
    /* Carta terminada em 06/04/2017, terminada em 1 dia
     * Quando um monstro do seu oponente declara um ataque,
     * você pode descartar 1 carta para negar aquele ataque.
     * Destrua esta carta durante a 3° End Phase do seu oponente após a ativação.
     */
function invocar($local, $id, $modo, $dono, $tipo, $gatilho = 'ataque_monstro') {
  	parent::invocar($local, $id, MODOS::ATAQUE_BAIXO, $dono, $tipo, 'ataque_monstro');
 }
 function ativar_efeito() {parent::avisar('Esse efeito só pode ser ativado quando um monstro do oponente atacar');}
 
function acionar($gatilho, $atacante) {
  $r['bloqueado'] = true;
  // se esse efeito está pendente em outro ataque
  if(parent::ler_variavel('atacante') != '') {$r['bloqueado'] = false; return $r;}
  
  if($gatilho[3] == 'direto') $atacado = 'diretamente';
  else $atacado = $this->duelo->regenerar_instancia($gatilho[4], $this->dono)->nome;
  
  if(!$this->_ativar($atacante->nome, $atacado)) $r['bloqueado'] = false;
  else {
      parent::manter('atacante', $atacante->inst);
      if($gatilho[3] == 'direto') parent::manter('atacado', 'direto_s');
      else parent::manter('atacado', $gatilho[4]);
      $this->duelo->set_engine($this->inst, $this->dono);
  }
  
  return $r;
 }
 
 private function _ativar($atacante, $atacado) {  // unica função dessa armadilha
  if(!parent::checar_ativar()) {return false;}
  
  $hand = $this->duelo->ler_mao($this->dono);
  $lista = array();
  for($x = 1; $x < $hand[0]; $x++) $lista[$x-1] = $hand[$x];
  
  if(count($lista) === 0) return false;
  
  $this->duelo->solicitar_carta($atacante.' atacando '.$atacado, $lista, $this->dono, $this->inst);
  parent::manter('momento_da_solicitação', time());
  
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'start_phase', 'x');
  if(parent::ler_variavel('ativado_em') === false) parent::manter('ativado_em', parent::ler_turno());
  $this->mudar_modo(MODOS::ATAQUE);
  parent::avisar('Efeito da carta '.$this->nome.' ativado',1);
 
  return true;
 }
 
 function carta_solicitada($cartaS) {
     if(parent::ler_variavel('atacante') == '') return false;
  $hand = $this->duelo->ler_mao($this->dono);
  $lista = array();
  for($x = 1; $x < $hand[0]; $x++) $lista[$x-1] = $hand[$x];
  
    if(count($lista) === 0 || !isset($lista[$cartaS])) {
        $this->cancelar_efeito();
        return false;
    }
 
    $this->duelo->apagar_carta_hand($this->dono, $cartaS+1);
    $this->duelo->colocar_no_cemiterio($lista[$cartaS], $this->dono);
    parent::avisar('Uma carta foi descartada para negar o ataque',1);
    parent::manter('atacante', '');
    
    return false;
 }
 
 function cancelar_efeito() {
     if(parent::ler_variavel('atacante') == '') return false;
     if(!file_exists($this->duelo->dir_duelo.$this->duelo->oponente($this->dono).'/'.parent::ler_variavel('atacante').'.txt')) {
                  parent::manter('atacante', '');
                  return false;
     }
     $atacante = $this->duelo->regenerar_instancia(parent::ler_variavel('atacante'), $this->duelo->oponente($this->dono));
     $atacante->manter('atacou_em', '0');
     if(parent::ler_variavel('atacado') != 'direto_s') $atacante->atacar($this->duelo->regenerar_instancia(parent::ler_variavel('atacado'), $this->dono), $this->pasta.'lps.txt');
     else $atacante->atacar(parent::ler_variavel('atacado'), $this->pasta.'lps.txt');
     parent::manter('atacante', '');
     parent::manter('atacado', '');
     return true;
 }
 
 function engine() {
     if(parent::ler_variavel('atacante') == '') return false;
     if(time() - (int)parent::ler_variavel('momento_da_solicitação') >= 10) $this->cancelar_efeito();
     return true;
 }
         
 function tarefa($txt) {
     parent::manter('atacante', ''); //limpando ultima ativação
     if(parent::ler_turno() - parent::ler_variavel('ativado_em') >= 7){
        parent::out_gatilho();
        parent::destruir();
     }
     return true;
 }
 
}
?>