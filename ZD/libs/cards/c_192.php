<?php
//terminada dia 07/04/2017, terminada em 1 dia
/*
 * Se o ataque do monstro atacante for menor que a defesa deste monstro,
 * o monstro atacante é destruído. (O cálculo de danos é aplicado normalmente)
 */

class c_192 extends Monstro_normal {
    
function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
    $r = parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
    if($r !== 0) {
        file_put_contents($this->pasta.'_script_'.$this->inst.'.txt', '');
        file_put_contents($this->pasta.'script_'.$this->inst.'.txt', '$atacante = file_get_contents("'.$this->pasta.'_script_'.$this->inst.'.txt'.'");
if($atacante != "" && file_exists($this->dir_duelo.$this->oponente('.$this->dono.')."/".$atacante.".txt")) {
        $efeito[0] = "destruir";
        $monstro = $this->regenerar_instancia($atacante, $this->oponente('.$this->dono.'));
        $monstro->sofrer_efeito($efeito);
        file_put_contents("'.$this->pasta.'_script_'.$this->inst.'.txt'.'", "");
}
if(!file_exists("'.$this->pasta.$this->inst.'.txt'.'")) {
    @unlink("'.$this->pasta.'_script_'.$this->inst.'.txt'.'");
    @unlink("'.$this->pasta.'script_'.$this->inst.'.txt'.'");
}
');
        
        $this->duelo->set_engine('script_'.$this->inst, $this->dono);
    }
    return $r;
}
    
function atacado($ataque) {
  if(parent::ler_variavel('equip') == 0) { // se não estiver equipado
    if($this->modo == 1) {$valor = $this->atk;}
    else {$valor = $this->def;}
    if($ataque['tipo'] == 'c') {
      $sobra = $valor - $ataque[1];
    }
    elseif($ataque['tipo'] == 'i') {
      $atk = 0;
      for($x = 1; $x < count($ataque); $x++) {$atk = $atk + $ataque[$x];}
      $sobra = $valor - $atk;
    }
    if($sobra < 0) {
      parent::destruir();
      $resposta['resultado'] = 'v';
    }
    elseif($sobra > 0) {
      $resposta['resultado'] = 'd';
      $grav = new Gravacao();
      $grav->set_caminho($this->pasta.$this->inst.'.txt');
      $infos = $grav->ler(0);
      if($infos[8] == 4) {
        $infos[8] = 2;
        $grav->set_array($infos);
        $grav->gravar();
      }
      unset($grav);
    }
    elseif($this->modo == 1) {
      parent::destruir();
      $resposta['resultado'] = 'e';
    }
    else {
     $grav = new Gravacao();
     $grav->set_caminho($this->pasta.$this->inst.'.txt');
     $infos = $grav->ler(0);
     if($infos[8] == 4) {
       $infos[8] = 2;
       $grav->set_array($infos);
       $grav->gravar();
       $resposta['resultado'] = 'n';
     }
    }
    $resposta['sobra'] = $sobra * -1;
    if($ataque['atacante']->atk < $this->def) $this->efeito($ataque['atacante']); //efeito
    return $resposta;
   }
   else {
    $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
    $r = $equipamento->atacado($ataque, $this);
    if($ataque['atacante']->atk < $this->def) $this->efeito($ataque['atacante']); //efeito
    return $r;
   }
}

private function efeito($monstro) {
    file_put_contents($this->pasta.'_script_'.$this->inst.'.txt', $monstro->inst);
    return true;
}

}

?>