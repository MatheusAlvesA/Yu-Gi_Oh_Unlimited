<?php
class c_3 extends Monstro_normal {
   
	function atacar(&$alvo, $lps, $checar = true) {
	 $arq = fopen($this->pasta.'/phase.txt', 'r');
	 $phase = fgets($arq);
	 fclose($arq);
	 if($phase != 4) {parent::avisar('Você não pode atacar fora da BattlePhase'); return 0;}
	 if(parent::ler_turno() <= 1) {parent::avisar('Você não pode atacar neste turno'); return 0;}
	 if(parent::ler_variavel('atacou_em') == parent::ler_turno()) {parent::avisar('Você não pode atacar mais de uma vez com o mesmo monstro no mesmo turno'); return 0;}
   if($this->modo != 1) {parent::avisar('Movimento inválido. Você só pode atacar se estiver em modo de ataque'); return 0;}
   if(parent::ler_variavel('equip') == 0) { // se não tiver equipamento
   if($alvo == 'direto_n') {parent::avisar('Você não pode atacar o oponente diretamente se o campo dele não estiver vazio'); return 0;}
   if($alvo == 'direto_s') {
	  $gatilho[0] = 'monstro';
	  $gatilho[1] = 'ataque';
	  $gatilho[2] = 'comum';
	  $gatilho[3] = 'direto';
	  if(parent::checar($gatilho)) {
		 $carta = &parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		 if($resposta['bloqueado']) {
                      parent::manter('atacou_em', parent::ler_turno());
                      return false;
                  }
		}
          $this->duelo->alterar_lp((-1)*$this->atk, $this->duelo->oponente($this->dono));
          parent::manter('atacou_em', parent::ler_turno()); 
	  parent::avisar($this->nome.' atacou os pontos de vida diretamente', 1);
	  return true;
	 }
	 else {
		$gatilho[0] = 'monstro';
	  $gatilho[1] = 'ataque';
	  $gatilho[2] = 'comum';
	  $gatilho[3] = 'monstro';
	  $gatilho[4] = $alvo->inst;
	  if(parent::checar($gatilho)) {
		 $carta = parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		  if($resposta['bloqueado']) {
                      parent::manter('atacou_em', parent::ler_turno());
                      return false;
                  }
		}
	  $ataque['tipo'] = 'c';
          $ataque['atacante'] = $this; //O alvo pode precisar saber quem está atacando
	  $ataque[1] = $this->atk;
	  $resposta = $alvo->atacado($ataque);
	  if($resposta['resultado'] == 'd') {
           parent::manter('atacou_em', parent::ler_turno());
           if($alvo->modo == 1) {$this->_ativar();parent::destruir();}
           else {
               $tool = new biblioteca_de_efeitos;
               $tool->dano_direto($this->dono, (-1)*$resposta['sobra']);
           }
		 parent::avisar($this->nome.' atacou '.$alvo->nome.' e perdeu', 1);
		 return true;
            }
            if($resposta['resultado'] == 'D') { // uma derrota, mas deve ser removido do jogo
                parent::manter('atacou_em', parent::ler_turno());
                if($alvo->modo == 1) {parent::remover_do_jogo();}
                else {
                    $tool = new biblioteca_de_efeitos;
                    $tool->dano_direto($this->dono, (-1)*$resposta['sobra']);
                }
             parent::avisar($this->nome.' atacou '.$alvo->nome.', mas perdeu e foi removido do jogo', 1);
             return true;
            }
		elseif($resposta['resultado'] == 'e') {
                 $this->_ativar(); // ativando o efeito
		 if($alvo->modo == 1) {parent::destruir();}
		 parent::avisar($this->nome.' atacou '.$alvo->nome.' e ambos foram destruídos', 1);
		 return true;
			}
            elseif($resposta['resultado'] == 'E') { // foi um empate, mas deve ser removido
             if($alvo->modo == 1) {parent::remover_do_jogo();}
             parent::avisar($this->nome.' atacou '.$alvo->nome.' e ambos foram destruídos. Essa carta foi removida do jogo', 1);
             return true;
            }
		elseif($resposta['resultado'] == 'n') {
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' mas não ouve efeito', 1);
		 parent::manter('atacou_em', parent::ler_turno()); 
		 return true;
		}
		elseif($resposta['resultado'] == 'v') {
   if($alvo->modo == MODOS::ATAQUE) parent::dano_direto($lps, $resposta['sobra']);
	 parent::avisar($this->nome.' atacou '.$alvo->nome.' que foi destruído', 1);
   parent::manter('atacou_em', parent::ler_turno());
   return true;
			}
	 }
   return false;
   }
   else {
        $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
        $resposta = $equipamento->atacar($alvo, $this, $lps);
        if($resposta['resultado'] == 'd' || $resposta['resultado'] == 'e') {$this->_ativar();} // ativando o efeito
        return $resposta;
   }
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
     $this->_ativar(); // ativando o efeito
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
     $this->_ativar(); // ativando o efeito
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
    return $resposta;
    }
    else {
        $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
        $resposta = $equipamento->atacado($ataque, $this);
        if($resposta['resultado'] == 'v' || $resposta['resultado'] == 'e') {$this->_ativar();} // ativando o efeito
        return $resposta;
    }
 }
    
 private function _ativar() {  // Efeito desse monstro
/*Quando esta carta é destruída e enviada para o cemitério por batalha, ambos os jogadores levam 500 de dano.*/
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'dano_lps';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    $this->duelo->alterar_lp((-500), $this->dono);
    $this->duelo->alterar_lp((-500), $this->duelo->oponente($this->dono));
    parent::avisar('Efeito do Monstro '.$this->nome.' ativado, ambos levam 500 de dano.', 1);
    return true;
  }
}
?>