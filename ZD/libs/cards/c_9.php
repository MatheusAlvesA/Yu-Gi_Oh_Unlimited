<?php
class c_9 extends Monstro_normal {
    /*Durante uma batalha entre esta carta atacando e um monstro na Posição 
     * de Defesa cuja DEF é menor que o ATK desta carta, inflija a diferença 
     * como Dano de Batalha ao seu oponente. Quando esta carta inflige Dano 
     * de Batalha ao seu oponente, compre 1 carta.*/

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
          $this->_ativar();
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
		 return true;
		}
		elseif($resposta['resultado'] == 'v') {
                    if($alvo->modo == 2 || $alvo->modo == 4) {$resposta['sobra'] = $this->atk - $alvo->def;}
   parent::dano_direto($lps, $resposta['sobra']);
   if($resposta["sobra"] > 0) {$this->_ativar();}
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
         if($alvo == 'direto_s') {$this->_ativar(0);}
         elseif($resposta['resultado'] == 'v') {
             if($alvo->modo == 2 || $alvo->modo == 4) {
              $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
              $this->atk = $equipamento->get_atk();
              $resposta['sobra'] = $this->atk - $alvo->def;
              parent::dano_direto($lps, $resposta['sobra']);
              if($resposta["sobra"] > 0) {$this->_ativar();}
             }
         }
         return true;
        }
      }
    
 private function _ativar($valor) {  // Efeito desse monstro
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'puxar_carta';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    $this->duelo->puxar_carta($this->dono);
    parent::avisar('Efeito do Monstro '.$this->nome.' ativado, uma carta foi puxada.', 1);
    return true;
  }
}
?>