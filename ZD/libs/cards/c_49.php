<?php

/* 
 * Apenas durante sua Battle Phase, todos os monstros que você controla ganham 200 de ATK.
 */

class c_49 extends Magica {

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar() || parent::ler_variavel('ativado_em') != 0) {return false;}
  parent::avisar('Efeito da carta '.$this->nome.' foi ativado', 1);
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'battle_phase', 'aumentar');
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm2_phase', 'diminuir');
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'diminuir');
  $this->mudar_modo(1);
  parent::manter('ativado_em', parent::ler_turno());
   return true;
 }
 
 function tarefa($txt) {
     if($txt == 'aumentar') {
      $array = $this->duelo->ler_campo_cods($this->dono);
      $string = '';
      for($x = 1; $x <= 5; $x++) {
        if($array[$x] != 0) {
         $this->duelo->alterar_valor($array[$x], $this->dono, 'atk', 200);
         $string .= $array[$x].'-';
        }
      }
      $string = substr($string, 0, -1);
      parent::manter('monstros', $string);
      return true;
     }
     elseif($txt == 'diminuir') {
      if(parent::ler_variavel('monstros') != 0 && parent::ler_variavel('monstros') != '') {
       $array = explode('-', parent::ler_variavel('monstros'));
       for($x = 0; $x < count($array); $x++) {$this->duelo->alterar_valor($array[$x], $this->dono, 'atk', -200);}
       parent::manter('monstros', '0');
      }
      return true;
     }
     else {return false;}
 }
 
 function destruir($motivo = 0) {
     $this->duelo->apagar_tarefa($this->dono, 'battle_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm2_phase', $this->inst);
     $this->tarefa('diminuir'); // evitando de deixar pra trás monstros afetados
     parent::destruir($motivo);
 }
 
}
?>