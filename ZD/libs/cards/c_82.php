<?php

/* terminado dia 12/07/2016 terminado em 1 dia
 * Se seu unico monstro no campo for 1 (Blackwing) virado para cima que não seja 
 * Blackwing-Gladius the MidnightSun, você pode Special Summon essa carta (da sua mão). 
 * Uma vez por turno, essa carta não pode ser destruida por batalha.
 */

class c_82 extends Monstro_normal {
    
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
     $campo = $this->duelo->ler_campo($dono);
     $carta = new DB_cards;
     $quantos = 0;
     for($x = 1; $x <= 5; $x++) {
         if($campo[$x][1] != 0) {
           $quantos++;
           $qual = $campo[$x][1];
         }
     }
     if($quantos == 1) {
         $carta->ler_id($qual);
         if($carta->id >= 79 && $carta->id <= 85 && $carta->id != 82) {
             return parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
         }
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
   if((int)parent::ler_variavel('atacado_em') === (int)parent::ler_turno()) {
       parent::destruir();
       $resposta['resultado'] = 'v';
   }
   else {
    parent::manter('atacado_em', parent::ler_turno());
    $resposta['resultado'] = 'n';
    if($this->modo == MODOS::DEFESA_BAIXO) {
         $grav = new Gravacao();
         $grav->set_caminho($this->pasta.$this->inst.'.txt');
         $infos = $grav->ler(0);
         $infos[8] = 2;
         $grav->set_array($infos);
         $grav->gravar();
    }
   }
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
   if((int)parent::ler_variavel('atacado_em') === (int)parent::ler_turno()) parent::destruir();
   else parent::manter('atacado_em', parent::ler_turno());
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
 return $resposta;
}
else {
 $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
 
 if((int)parent::ler_variavel('atacado_em') !== (int)parent::ler_turno()) parent::manter ('travar_destruir', 1);
 $resposta = $equipamento->atacado($ataque, $this);
 if((int)parent::ler_variavel('atacado_em') !== (int)parent::ler_turno()) parent::manter ('travar_destruir', 0);
 
  if($resposta['resultado'] == 'v' && file_exists($this->pasta.$this->inst.'.txt')) { // ativar efeito
     $resposta['resultado'] = 'n';
     parent::manter('atacado_em', parent::ler_turno());
 }
 
 return $resposta;
}
}

}
?>