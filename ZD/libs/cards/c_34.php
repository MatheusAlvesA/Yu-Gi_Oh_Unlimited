<?php

/* 
 Envie 1 Carta de Monstro de sua mão para o cemitério para destruir 1 monstro 
 * virado para cima no lado do campo de seu oponente com um ATK igual ou menor 
 * que o ATK do monstro enviado. Durante a End Phase de um turno que essa carta 
 * destruiu um monstro como resultado de batalha, envie essa carta para o cemitério 
 * para Special Summon 1 (Armed Dragon LV7) de sua mão ou Deck.
 */

class c_34 extends Monstro_normal {
    
    function ativar_efeito() {
        $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_monstro';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
            $turno = $this->duelo->ler_turno();
            if(parent::ler_variavel('ativado_em') == $turno) {
             parent::avisar('Você só pode ativar esse efeito uma vez por turno!');
             return false;  
            }
      if(parent::ler_variavel('carta_solicitada') != 0) {
        if(parent::ler_variavel('carta_solicitada2') != 0) {
            parent::manter('ativado_em', $turno);
            parent::excluir_carta_hand('id', parent::ler_variavel('carta_solicitada'));
            $this->duelo->colocar_no_cemiterio(parent::ler_variavel('carta_solicitada'), $this->dono);
            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
            for($x = 1; $x < $campo[0][0] && $campo[$x][1] != parent::ler_variavel('carta_solicitada2'); $x++) {}
            $alvo = &$this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
            $efeito[0] = 'destruir';
            $efeito[1] = 'efeito_monstro';
            $efeito[2] = 'alvo_designado';
            $alvo->sofrer_efeito($efeito, $this);
            $this->limpar(); // limpando variaveis salvas
            return true;
        }
       else {
         $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
         $y = 0;
          $monstro = new DB_cards();
          $monstro->ler_id(parent::ler_variavel('carta_solicitada'));
         for($x = 1; $x <= 5; $x++) {
            $monstroa = new DB_cards();
            $monstroa->ler_id($campo[0][1]);
          if($monstro->atk > $monstroa->atk && ($campo[$x][0] == 1 || $campo[$x][0] == 2)) {
            $lista[$y] = $campo[$x][1];
            $y++;
          }
         }
         if(!isset($lista)) {
         parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
         return false;
         }
         $this->duelo->solicitar_carta('Escolha um alvo', $lista, $this->dono, $this->inst);
         return true;
       }
    }
    else {
     $hand = $this->duelo->ler_mao($this->dono);
     $carta = new DB_cards();
     $y = 0;
     for($x = 1; $x < $hand[0]; $x++) {
       $carta->ler_id($hand[$x]);
       if($carta->categoria == 'monster') {
           $lista[$y] = $hand[$x];
           $y++;
       }
     }
     if(!isset($lista)) {
      parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
      return false;
     }
     $this->duelo->solicitar_carta('Escolha um monstro da sua mão', $lista, $this->dono, $this->inst);
     return true;
    }
  }
    
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
   if($alvo->modo == MODOS::ATAQUE) parent::dano_direto($lps, $resposta['sobra']);
	 parent::avisar($this->nome.' atacou '.$alvo->nome.' que foi destruído', 1);
   parent::manter('atacou_em', parent::ler_turno());
    $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 0); // agendando efeito
   return true;
			}
	 }
   return false;
  }
  else {
      $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
      $resposta = $equipamento->atacar($alvo, $this, $lps);
      if($resposta['resultado'] == 'v') {$this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 0);} // agendando efeito
      return true;
  }
	}
    
 private function _ativar() {  // Efeito desse monstro
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'invocar_carta';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    $mao = $this->duelo->ler_mao($this->dono);
    $existe = false;
    for($x = 1; $x < $mao[0]; $x++) {
        if($mao[$x] == 35) { // id da carta LV5
            parent::excluir_carta_hand('id', 35);
            $existe = true;
            break;
        }
    }
    if(!$existe) {
        $deck = $this->duelo->ler_deck($this->dono);
     for($x = 1; $x < $deck[0]; $x++) {
        if($deck[$x] == 35) {
            parent::excluir_carta_deck('id', 35);
            $existe = true;
            break;
        }
     }
    }
    if($existe) {
     $campo = $this->duelo->ler_campo($this->dono);
     for($x = 1; $campo[$x][1] != 0 && $x <= 5; $x++) {}
    if($x > 5) {
     parent::avisar('Não existe espaço para invocar a carta');
     return false;
    }
     parent::avisar('Efeito do Monstro '.$this->nome.' ativado', 1);
     $this->duelo->invocar($this->dono, $x, 1, 35, 'LV5_sacrificado'); // invocando de forma especial com sacrificio
     return true;
    }
    else {
     parent::avisar('Armed Dragon LV7 não foi encontrado na sua mão ou deck');
     return false;
    }
  }
  
function tarefa($txt) { //essa carta deve se destruir caso seja ativada e round passe
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     parent::destruir();
     $this->_ativar();
     return true;
 }

  function carta_solicitada($cartaS) {
  if(parent::ler_variavel('carta_solicitada') == 0) {
  $hand = $this->duelo->ler_mao($this->dono);
  $carta = new DB_cards();
  $y = 0;
   for($x = 1; $x < $hand[0]; $x++) {
       $carta->ler_id($hand[$x]);
       if($carta->categoria == 'monster') {
           $lista[$y] = $hand[$x];
           $y++;
       }
    }
  if(!isset($lista)) {
   parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
   return false;
  }
  if(!isset($lista[$cartaS])) {return false;}
  parent::manter('carta_solicitada', $lista[$cartaS]);
  $this->ativar_efeito();
  return true;
   }
  else {
         $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
         $y = 0;
          $monstro = new DB_cards();
          $monstro->ler_id(parent::ler_variavel('carta_solicitada'));
         for($x = 1; $x <= 5; $x++) {
            $monstroa = new DB_cards();
            $monstroa->ler_id($campo[0][1]);
          if($monstro->atk > $monstroa->atk && ($campo[$x][0] == 1 || $campo[$x][0] == 2)) {
            $lista[$y] = $campo[$x][1];
            $y++;
          }
         }
         if(!isset($lista)) {
         parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
         return false;
         }
         if(!isset($lista[$cartaS])) {return false;}
         parent::manter('carta_solicitada2', $lista[$cartaS]);
         $this->ativar_efeito();
  }
}

private function limpar() {
    parent::manter('carta_solicitada', 0);
    parent::manter('carta_solicitada2', 0);
}

function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   $comandos_possiveis['ativar'] = true;
   if($this->modo == MODOS::ATAQUE && $phase == 4) $comandos_possiveis['atacar'] = true;
   else $comandos_possiveis['atacar'] = false;
   if($this->modo == MODOS::DEFESA && $phase != 4) $comandos_possiveis['posição_ataque'] = true;
   else $comandos_possiveis['posição_ataque'] = false;
   if($phase != 4 && $this->modo == MODOS::ATAQUE) $comandos_possiveis['posição_defesa'] = true;
   else $comandos_possiveis['posição_defesa'] = false;
   if($this->modo == MODOS::DEFESA_BAIXO && $phase != 4) $comandos_possiveis['flipar'] = true;
   else $comandos_possiveis['flipar'] = false;
   if($phase != 4) $comandos_possiveis['sacrificar'] = true;
   else $comandos_possiveis['sacrificar'] = false;
 return $comandos_possiveis;
}

}
?>