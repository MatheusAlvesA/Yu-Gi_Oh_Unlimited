<?php
/* terminada dia 17/09/2016. terminada em 3 dias
 * Ative somente quando um monstro do seu oponente declara um ataque.
 * Você pode mudar o alvo do ataque para outro monstro no seu lado do campo.
 *  */
class c_105 extends Armadilha {
	function invocar($local, $id, $modo, $dono, $tipo, $gatilho = 'ataque_monstro') {
  	parent::invocar($local, $id, $modo, $dono, $tipo, 'ataque_monstro');
        $this->duelo->set_engine($this->inst);
 }

 function acionar($gatilho, $monstro) {
  $r['bloqueado'] = true;
  $erro['bloqueado'] = false;
  $campo = $this->duelo->ler_campo($this->dono);
  $y = 0;
  for($x = 1; $x <= 5; $x++) {
     if($campo[$x][1] != 0) {$lista[$y] = $campo[$x][1];$y++;}
  }
  if(!isset($lista) || parent::ler_variavel('monstro') != 0) return $erro;
  parent::manter('monstro', $monstro->inst);
  parent::manter('original', $gatilho[4]);
  // houston temos um problema
  // a função de conversar com um jogador que não tem posse do turno não existe
  $this->duelo->solicitar_carta('ESCOLHA UM NOVO ALVO PARA O ATAQUE', $lista, $this->dono, $this->inst);
  return $r;
 }
 
 function carta_solicitada($cartaS) {
     $atacante = $this->duelo->regenerar_instancia(parent::ler_variavel('monstro'), $this->duelo->oponente($this->dono));
     if($cartaS < 0) {
        $atacante->manter('atacou_em', $this->duelo->ler_turno()-42);
        $resultado = $atacante->atacar($this->duelo->regenerar_instancia(parent::ler_variavel('original'), $this->dono), $this->duelo->dir_duelo.$this->duelo->oponente($this->dono).'/lps.txt');
        parent::manter('monstro', 0);
        parent::manter('original', 0);
        return $resultado;
     }
  $campo = $this->duelo->ler_campo($this->dono);
  $y = 0;
  for($x = 1; $x <= 5; $x++) {
     if($campo[$x][1] != 0) {$lista[$y] = $campo[$x][1];$y++;}
  }
 if(!isset($lista) || !$lista[$cartaS] || $cartaS > 5 || $cartaS < 0) {
     parent::destruir();
     return false;
 }
 
  $quantas = 0;
  for($x = 0; $x <= $cartaS; $x++){
    if($lista[$x] == $lista[$cartaS]) {$quantas++;}
  }
    for($x = 1; $x <= 5; $x++) {
         if($campo[$x][1] == $lista[$cartaS]) {
             if($quantas > 1) {$quantas--;}
             else {break;}
         }
     }
     $alvo = $this->duelo->regenerar_instancia_local($x, $this->dono);
    parent::avisar('Efeito da carta '.$this->nome.' foi ativado, o ataque foi redirecionado para '.$alvo->nome, 1);
   $atacante->manter('atacou_em', $this->duelo->ler_turno()-42);
   $resultado = $atacante->atacar($alvo, $this->duelo->dir_duelo.$this->duelo->oponente($this->dono).'/lps.txt');
   parent::out_gatilho();
   parent::destruir();
   return $resultado;
 }
 
 function engine() {
    if(parent::ler_variavel('monstro') == '0') return false;;
    if($this->passou(10)) {
        return $this->carta_solicitada(-1);
    }
    else return false;
}

function passou($limite) {
    if(!file_exists($this->pasta.'tempo_'.$this->inst.'.txt')) file_put_contents($this->pasta.'tempo_'.$this->inst.'.txt', time());
    if((time() - file_get_contents($this->pasta.'tempo_'.$this->inst.'.txt')) > $limite) {
        @unlink($this->pasta.'tempo_'.$this->inst.'.txt');
        return true;
    }
    else return false;
}


}
?>