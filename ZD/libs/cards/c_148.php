<?php

/* terminado dia 01/03/2017. em 1 minuto por já ter a armed ninja igual
 * FLIP: Destrua 1 Carta Trap no campo. Se a carta estiver virada para baixo,
 * veja-a e a destrua se for uma Cartas Trap. Se for uma Carta Spell, a retorne á sua posição original.
 */

class c_148 extends Monstro_normal {
    
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
     if($this->modo == 4) { // ativando o efeito
         parent::manter('auto_destruir', time());
         $this->duelo->set_engine($this->inst, $this->dono);
         $this->_ativar();
     }
     else {parent::destruir();}
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
      $this->_ativar(); // ativando o efeito
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
                    if($this->modo == 4) $this->_ativar(); //ativando efeito
                   }
     $resposta['resultado'] = 'n';
    }
    $resposta['sobra'] = $sobra * -1;
    return $resposta;
   }
   else {
       parent::manter('travar_destruir', 1);
       $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
       $resposta = $equipamento->atacado($ataque, $this);
       parent::manter('travar_destruir', 0);
       if($resposta['resultado'] == 'v' && $this->modo == 4) {
          parent::manter('auto_destruir', 1);
          $this->_ativar();
          $this->duelo->agendar_tarefa($this->inst, $this->dono, 'start_phase', '0');
       } // ativando efeito
      elseif($resposta['resultado'] == 'v' && $this->modo != 4) {
        parent::destruir();
       }
       elseif($resposta['resultado'] == 'd' && $this->modo == 4) {
        $this->_ativar(); // ativando o efeito
       }
       elseif($resposta['resultado'] == 'e') {parent::destruir();}
       return $resposta;
   }
 }
 public function engine() {
     $inicio = (int)parent::ler_variavel('auto_destruir');
     $agora  = time();
     if($agora-$inicio > 7) $this->tarefa('destruir');
 }
 private function _ativar() {  // Efeito FLIP desse monstro
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'destruir';
    $gatilho[3] = 'magica';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
  if(parent::ler_variavel('carta_solicitada') == 0) {
   $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   $y = 0;
   for($x = 6; $x < $campo[0][0]; $x++) {
       if($campo[$x][0] == 1) {
           $lista[$y] = $campo[$x][1];
           $y++;
       }
       elseif($campo[$x][0] != 0) {
           $lista[$y] = 'ub';
           $y++;
       }
    }
    if(!isset($lista)) {
     parent::avisar('Não existem cartas magicas ou armadilhas no campo do oponente');
     if(parent::ler_variavel('auto_destruir') != 0) {$this->tarefa('0');}
     return false;
    }
    $this->duelo->solicitar_carta('Escolha uma carta', $lista, $this->dono, $this->inst);
    return true;
  }
  else {
      $carta = new DB_cards();
      $carta->ler_id(parent::ler_variavel('carta_solicitada'));
      if($carta->categoria != 'trap') {
       parent::avisar('Efeito do Monstro '.$this->nome.' ativado, a carta '.$carta->nome.' foi selecionada, mas não é uma armadilha', 1);
       if(parent::ler_variavel('auto_destruir') != 0) {$this->tarefa('0');}
       return false;
      }
    $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
     for($x = 6; $x < $campo[0][0] && $campo[$x][1] != parent::ler_variavel('carta_solicitada'); $x++) {}
     $alvo = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
     $efeito[0] = 'destruir';
     $efeito[1] = 'efeito';
     $efeito[2] = 'monstro';
     if($alvo->sofrer_efeito($efeito, $this)) {
      parent::avisar('Efeito do Monstro '.$this->nome.' ativado, a carta '.$carta->nome.' foi selecionada e destruida', 1);
      if(parent::ler_variavel('auto_destruir') != 0) {$this->tarefa('0');}
      return true;
     }
     if(parent::ler_variavel('auto_destruir') != 0) {$this->tarefa('0');}
     return false;
   }
  }
  
   function carta_solicitada($cartaS) {
   $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   $y = 0;
   for($x = 6; $x < $campo[0][0]; $x++) {
       if($campo[$x][0] != 0) {
           $lista[$y] = $campo[$x][1];
           $y++;
       }
    }
    if(!isset($lista) || !$lista[$cartaS]) {
     return false;
    }
  parent::manter('carta_solicitada', $lista[$cartaS]);
  $this->_ativar();
}

	function mudar_modo($modo) {
	 $arq = fopen($this->pasta.'/phase.txt', 'r');
	 $phase = fgets($arq);
	 fclose($arq);
	 if($phase != 3 && $phase != 5) {parent::avisar('Você não pode mudar a posição fora das MainPhases'); return 0;}
	 if(parent::ler_variavel('invocado_em') == parent::ler_turno() && parent::ler_variavel('invocação') != 'especial') {parent::avisar('Você não pode mudar a posição neste turno'); return 0;}
	 if(parent::ler_variavel('atacou_em') == parent::ler_turno()) {parent::avisar('Você não pode mudar a posição depois de atacar'); return 0;}
	 if(parent::ler_variavel('modo_alterado_em') == parent::ler_turno()) {parent::avisar('Você não pode mudar a posição mais de uma vez no mesmo turno'); return 0;}
   if($this->modo == 4 && $modo != 1) {parent::avisar('Movimento inválido. Você só pode alterar para modo de ataque neste momento'); return 0;}
   if($this->modo == 1 && $modo != 2) {parent::avisar('Movimento inválido. Você só pode alterar para modo de defesa neste momento'); return 0;}
   if($this->modo == 2 && $modo != 1) {parent::avisar('Movimento inválido. Você só pode alterar para modo de ataque neste momento'); return 0;}
   $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[8] = $modo;
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                if($this->modo == 4) {$this->_ativar();} // ativando o efeito
   $this->modo = $modo;
   parent::manter('modo_alterado_em', parent::ler_turno());
   return 1;
	}

    function tarefa($txt) { //essa carta deve se destruir caso seja ativada quando destruida em batalha
     $this->duelo->apagar_tarefa($this->dono, 'start_phase', $this->inst);
     parent::destruir();
     return true;
 }

    function sofrer_efeito($efeito, &$inst) {
          if(parent::ler_variavel('equip') == 0) { // se não estiver equipado
            if($efeito[0] == 'destruir') {
                parent::avisar($this->nome.' foi destruido pelo efeito da carta '.$inst->nome, 1);
                parent::destruir();
                return true;
            }
            else if($efeito[0] == 'remover_do_jogo') {
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
                if($this->modo == MODOS::DEFESA_BAIXO && $efeito[1] != MODOS::DEFESA_BAIXO) $this->_ativar (); 
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