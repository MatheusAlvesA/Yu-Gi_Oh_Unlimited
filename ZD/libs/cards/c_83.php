<?php

/* terminado dia 12/07/2016 terminado em 1 dia
 * Se você não controla cartas (incluindo traps/spells e monstros), 
 * você pode Special Summon esta carta da sua mão para o seu lado do campo.
 * Apenas durante a Damage Step, se esta carta estiver sendo atacada virada para cima no campo, 
 * o monstro atacante perde 300 de ATK.
 */

class c_83 extends Monstro_normal {
    
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
     $campo = $this->duelo->ler_campo($dono);
     $quantos = 0;
     for($x = 1; $x <= 11; $x++) {
         if($campo[$x][1] != 0) {
           $quantos++;
         }
     }
     if($quantos == 0){
       return parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
     }
     
     return parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
    }
 
function atacado($ataque) {
    if(parent::ler_variavel('NEGAR_EFEITO') == 1) {return parent::atacado($ataque);}
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
 if($this->modo != MODOS::DEFESA_BAIXO) { // efeito
     $resposta['sobra'] -= 300;
     if($resposta['sobra'] < 0) $resposta['sobra'] = 0;
 }
 return $resposta;
}
else {
 $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
 $resposta = $equipamento->atacado($ataque, $this);
  if($this->modo != MODOS::DEFESA_BAIXO) { // efeito
     $resposta['sobra'] -= 300;
     if($resposta['sobra'] < 0) $resposta['sobra'] = 0;
 }
 return $resposta;
}
}

}
?>