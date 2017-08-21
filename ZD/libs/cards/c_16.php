<?php
/* 
 * Selecione 1 monstro do seu lado do campo e envie-o para o cemitério.
 * Aumente seus LPs por uma quantia igual ao ATK original do monstro.
 */

class c_16 extends Armadilha {
	function invocar($local, $id, $modo, $dono, $tipo, $gatilho = false) {
  	parent::invocar($local, $id, $modo, $dono, $tipo);
 }

 function ativar_efeito() {
     if(!parent::checar_ativar()) {return false;}
     $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 0); // agendar tarefa de destruir caso não faça nada
  $r['bloqueado'] = false;
  $this->_ativar();
  return $r;
 }
 
 function _ativar() {  // unica função dessa armadilha
  if(parent::ler_variavel('carta_solicitada') != 0) {
     $monstro = &$this->duelo->regenerar_instancia_local(parent::ler_variavel('carta_solicitada'), $this->dono);
     $efeito[0] = 'destruir';
   if($monstro->sofrer_efeito($efeito, $this) === 'bloqueado') {parent::avisar('Efeito da carta armadilha '.$this->nome.' bloqueado', 1);$this->tarefa('0');return false;}
  parent::dano_direto($this->duelo->dir_duelo.$this->dono.'/lps.txt', (-1)*$monstro->atk);
  parent::avisar($monstro->atk. ' foram somados aos lps do duelista', 1);
  $this->tarefa('0');
  return true;
  }
  else {
   $campo = $this->duelo->ler_campo($this->dono);
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
      if($campo[$x][1] != 0) {$lista[$y] = $campo[$x][1];$y++;}
    }
    if(!isset($lista)) {
     parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
     parent::destruir();
     return false;
    }
    $this->duelo->solicitar_carta('Escolha um monstro', $lista, $this->dono, $this->inst);
    $this->mudar_modo(1);
    return true;
  }
 }
 
   function tarefa($txt) { //essa carta deve se destruir caso seja ativada e round passe
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     parent::destruir();
     return false;
 }
 
  function carta_solicitada($cartaS) {
   $campo = $this->duelo->ler_campo($this->dono);
  $y = 0;
  for($x = 1; $x <= 5; $x++) {
      if($campo[$x][1] != 0) {$lista[$y] = $campo[$x][1];$y++;}
    }
  if(!isset($lista)) {
   parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
   parent::destruir();
   return false;
  }
  if(!isset($lista[$cartaS])) {$this->tarefa('0');return 0;}
    for($x = 1; $x <= 5; $x++) {
      if($campo[$x][1] == $lista[$cartaS]) {$Pcarta = $x; break;}
    }
  parent::manter('carta_solicitada', $Pcarta); // posição da carta no campo
  $this->_ativar();
}
}
?>