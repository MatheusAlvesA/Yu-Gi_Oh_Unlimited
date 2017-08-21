<?php

/* carta termiada dia 20/04/2107. Terminada em 1 dia
 * Esta carta não pode ser destruída por Spell e Trap Cards que não designam alvo.
 * Além disso, esta carta não é destruída em batalha ao batalhar com um monstro que possua 1900 ou menos de ATK.
 */

class c_200 extends Monstro_normal {

function atacado($ataque) {
//ativando o efeito dessa carta
if($ataque['tipo'] == 'c') {
   if($ataque[1] <= 1900) parent::manter ('travar_destruir', 1); //efeito
 }
 elseif($ataque['tipo'] == 'i') {
   $atk = 0;
   for($x = 1; $x < count($ataque); $x++) {$atk = $atk + $ataque[$x];}
   if($atk <= 1900) parent::manter('travar_destruir', 1); //efeito
 }
 $retorno = null;//o valor a ser retornado após tudo
//executando o atacado normalmente
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
  }
  $resposta['resultado'] = 'n';
 }
 $resposta['sobra'] = $sobra * -1;
 $retorno = $resposta;
}
else {
 $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
 $retorno = $equipamento->atacado($ataque, $this);
}
//desativando o efeito
parent::manter('travar_destruir', 0);
return $retorno;
}

  function sofrer_efeito($efeito, &$inst) {
          if(parent::ler_variavel('equip') == 0) { // se não estiver equipado
            if($efeito[0] == 'destruir') {
                $tool = new biblioteca_de_efeitos;
                if(!TRAPouSPELLdesignaAlvo($inst->id)) {
                    parent::avisar($this->nome.' não pode ser destruido por esse efeito',1);
                    return false;
                }
                parent::avisar($this->nome.' foi destruido pelo efeito da carta '.$inst->nome, 1);
                parent::destruir();
                return true;
            }
            else if($efeito[0] == 'remover_do_jogo') {
                if(!TRAPouSPELLdesignaAlvo($inst->id)) {
                    parent::avisar($this->nome.' não pode ser destruido por esse efeito',1);
                    return false;
                }
                parent::avisar($this->nome.' foi removido do jogo pelo efeito da carta '.$inst->nome, 1);
                parent::remover_do_jogo();
                return true;
            }
            else if($efeito[0] == 'mudar_modo') {
                $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[8] = $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->modo = $efeito[1];
                return true;
            }
            else if($efeito[0] == 'incrementar_LV') {
                $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[2] += $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->lv += $efeito[1];
                return true;
            }
            else if($efeito[0] == 'incrementar_ATK') {
                $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[3] += $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->atk += $efeito[1];
                return true;
            }
            else if($efeito[0] == 'incrementar_DEF') {
                $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[4] += $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->def += $efeito[1];
                return true;
            }
            else if($efeito[0] == 'voltar_deck') { // esse efeito retorna a carta para o deck
                parent::avisar($this->nome.' retornou para o deck pelo efeito da carta '.$inst->nome, 1);
                parent::remover_do_jogo();
                $this->duelo->colocar_no_deck($this->id, $this->dono);
                $this->duelo->embaralhar_deck($this->id, $this->dono);
                return true;
            }
            return false;
          }
          else {
             $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
             return $equipamento->monstro_sofrer_efeito($efeito, $inst, $this);
          }
  }

}
?>