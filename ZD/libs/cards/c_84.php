<?php

/* terminado dia 13/07/2016 terminado em 1 dia
 * Quando esta carta destrói um monstro do oponente por batalha e o envia para o Cemitério, 
 * você pode Special Summon 1 monstro (Blackwing) com 1500 ou menos de ATK do seu Deck. 
 * Os efeitos daquele monstro são negados.
 */

class c_84 extends Monstro_normal {
    
 	function atacar(&$alvo, $lps, $checar = true) {
            if(parent::ler_variavel('NEGAR_EFEITO') == 1) {return parent::atacar($alvo, $lps, $checar);}
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
	   if($alvo->modo == 1) {
               parent::manter('atacou_em', parent::ler_turno());
                 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'battle_phase', 'x');
                 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm2_phase', 'x');
                 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
                 parent::manter('destruido', 1);
                 $this->_ativar();
           }
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' e ambos foram destruídos', 1);
	   return true;
	  }
            elseif($resposta['resultado'] == 'E') { // foi um empate, mas deve ser removido
             if($alvo->modo == 1) {
               parent::manter('atacou_em', parent::ler_turno());
                 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'battle_phase', 'x');
                 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm2_phase', 'x');
                 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
                 parent::manter('destruido', 1);
                 parent::manter('remover', 1);
                 $this->_ativar();
                }
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
           $this->_ativar();
           return true;
	  }
	 }
         return false;
        }
        else {
         $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
         return $equipamento->atacar($alvo, $this, $lps);
        }
       }
    
 function tarefa($txt) {
     $this->duelo->apagar_tarefa($this->dono, 'battle_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm2_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     if(parent::ler_variavel('remover') !== false) parent::remover_do_jogo();
     else $this->destruir();
 }
 
 function sacrificar() {
     if(parent::ler_variavel('destruido') == 1) {$this->tarefa('x'); return false;}
  $arq = fopen($this->pasta.'/phase.txt', 'r');
	$phase = fgets($arq);
	fclose($arq);
	if($phase != 3 && $phase != 5) {parent::avisar('Você não pode fazer sacrifícios fora das MainPhases'); return false;}
  $arq = fopen($this->pasta.'/sacrificios.txt', 'r');
	$sacrificios = fgets($arq);
	fclose($arq);
  $arq = fopen($this->pasta.'/sacrificios.txt', 'w');
	fwrite($arq, $sacrificios + 1);
	fclose($arq);
	return parent::destruir();
 }
 
 function _ativar() {
  $vetor_deck = $this->duelo->ler_deck($this->dono);
  $i = new DB_cards;
  $y = 0;
  for($x = 1; $x < $vetor_deck[0];$x++) {
      $i->ler_id($vetor_deck[$x]);
      if(($i->id >= 79 && $i->id <= 85) && $i->atk <= 1500) {
          $lista[$y] = $vetor_deck[$x];
          $y++;
      }
  }
  if(!is_array($lista)) {
      parent::avisar('Você não possui monstros do tipo Blackwing com ataque menor que 1500 no seu deck');
      return false;
  }
  
  $this->duelo->solicitar_carta('Escolha um monstro', $lista, $this->dono, $this->inst);
  return true;
 }
 
  function carta_solicitada($cartaS) {
    $vetor_deck = $this->duelo->ler_deck($this->dono);
    $i = new DB_cards;
    $y = 0;
    for($x = 1; $x < $vetor_deck[0];$x++) {
      $i->ler_id($vetor_deck[$x]);
      if(($i->id >= 79 && $i->id <= 85) && $i->atk <= 1500) {
          $lista[$y] = $vetor_deck[$x];
          $y++;
      }
    }
    if(!$lista[$cartaS]) {
      $this->tarefa('x');
      return false;        
    }
    $campo = $this->duelo->ler_campo($this->dono);
    for($x = 1; $x <= 5 && $campo[$x][1] != 0; $x++);
    if($x > 5) {
        parent::avisar('Não existe espaço em campo para invocar esse monstro');
        return false;
    }
    parent::avisar('Efeito da carta '.$this->nome.' foi ativado', 1);
    parent::excluir_carta_deck('id', $lista[$cartaS], $this->dono); // tirando do deck
    $this->duelo->invocar($this->dono, $x, MODOS::ATAQUE, $lista[$cartaS], 'especial'); // invocando
    $monstro = $this->duelo->regenerar_instancia_local($x, $this->dono);
    $monstro->manter('NEGAR_EFEITO', 1);
     return true;
 }
 
}
?>