<?php

/* 
 * Se esta carta atacar, mude-a para Posição de Defesa após o Cálculo de Dano.
 */

class c_44 extends Monstro_normal {
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
          $this->mudar_modo(2, true); // ativando efeito
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
            if($alvo->modo == 1) {parent::destruir();}
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
                 $this->mudar_modo(2, true); // ativando efeito
		 return true;
		}
		elseif($resposta['resultado'] == 'v') {
   if($alvo->modo == MODOS::ATAQUE) parent::dano_direto($lps, $resposta['sobra']);
	 parent::avisar($this->nome.' atacou '.$alvo->nome.' que foi destruído', 1);
   parent::manter('atacou_em', parent::ler_turno());
   $this->mudar_modo(2, true); // ativando efeito
   return true;
			}
	 }
   return false;
   }
   else {
       $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
       $resposta = $equipamento->atacar($alvo, $this, $lps);
       if($alvo == 'direto_s') {$this->mudar_modo(2, true);}
       elseif($resposta['resultado'] == 'v' || $resposta['resultado'] == 'n') {$this->mudar_modo(2, true);}
       return true;
   }
	}
        
    function mudar_modo($modo, $efeito = false) {
        if(!$efeito) {
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
        }
   $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[8] = $modo;
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
   $this->modo = $modo;
   parent::manter('modo_alterado_em', parent::ler_turno());
   return 1;
	}
}
?>