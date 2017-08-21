<?php

class c_240 extends Monstro_normal {
/* carta terminada dem 18/05/2017 terminada em 1 dia
 * Esta carta ganha 400 de ATK e DEF para cada carta na sua mão.
*/

 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
     $r = parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
     if($r) {
         parent::manter('atk_o', $this->atk);
         parent::manter('def_o', $this->def);
         $this->duelo->set_engine($this->inst, $dono);
     }
     return $r;
 }
 
 function engine() {
     $hand = $this->duelo->ler_mao($this->dono);
     $incremento = 400*($hand[0]-1);
     $this->set_atk(parent::ler_variavel('atk_o')+$incremento);
     $this->set_def(parent::ler_variavel('def_o')+$incremento);
     return true;
 }
 
 private function set_atk($atk) {
    $grav = new Gravacao();
    $grav->set_caminho($this->pasta.$this->inst.'.txt');
    $infos = $grav->ler(0);
    $infos[3] = $atk;
    $grav->set_array($infos);
    $grav->gravar();
    unset($grav);
    $this->atk = $atk;
    return true;
 }
 
 private function set_def($def) {
    $grav = new Gravacao();
    $grav->set_caminho($this->pasta.$this->inst.'.txt');
    $infos = $grav->ler(0);
    $infos[4] = $def;
    $grav->set_array($infos);
    $grav->gravar();
    unset($grav);
    $this->atk = $def;
    return true;
 }
 
   function sofrer_efeito($efeito, &$inst) {
            if($efeito[0] == 'incrementar_ATK') {
                parent::manter('atk_o', parent::ler_variavel('atk_o')+$efeito[1]);
                $this->atk += $efeito[1];
                return true;
            }
            if($efeito[0] == 'incrementar_DEF') {
                parent::manter('atk_o', parent::ler_variavel('def_o')+$efeito[1]);
                $this->def += $efeito[1];
                return true;
            }
      return parent::sofrer_efeito($efeito, $inst);
  }
 
}