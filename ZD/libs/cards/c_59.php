<?php

/* 
 * Você pode Special Summon esta carta da sua mão para o seu lado do campo por 
 * remover do jogo 1 monstro Machine-Type e 1 monstro Beast-Warrior-Type da sua mão, 
 * campo e/ou Cemitério. Quando esta carta batalha, qualquer Dano de Batalha infligido ao seu oponente torna-se 0.
 */

class c_59 extends Monstro_normal {
    
 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
   if($this->inst) {return 0;}
   
   $monstro = new DB_cards();
   $monstro->ler_id($id);
   if($tipo == 'comum') { // processo de Invocação comum
   if($modo != 1 && $modo != 4) {return 0;}
   if(!file_exists($this->pasta.'/sacrificios.txt')) {
     $arq = fopen($this->pasta.'/sacrificios.txt', 'w');
     fwrite($arq, '0');
     fclose($arq);
   }
	$arq = fopen($this->pasta.'/phase.txt', 'r');
	$phase = fgets($arq);
	fclose($arq);
	if($phase != 3 && $phase != 5) {parent::avisar('Você não pode invocar fora das MainPhases'); return 0;}
	 if(file_exists($this->pasta.'/m_invocado.txt') && !($flags !== false && $flags['ignorar']['invocou'] === true)) {parent::avisar('Você não pode invocar mais de um monstro por turno'); return 0;}
	$arq = fopen($this->pasta.'/sacrificios.txt', 'r');
	$sacrificios = fgets($arq);
	fclose($arq);
	if($monstro->lv > 4) {
		if($monstro->lv == 5 || $monstro->lv == 6) {
			if($sacrificios < 1) {parent::avisar('Você não sacrificou monstros suficientes para invocar esta carta'); return 0;}
     else {$sacrificios = $sacrificios - 1;}
			}
		if($monstro->lv >= 7) {
			if($sacrificios < 2) {
                            $this->dono = $dono; // essa variavel será nescesária
                            $this->inst = uniqid(); // essa tambem
                            if(!$this->_ativar()) {
                             parent::avisar('Você não sacrificou monstros suficientes para invocar esta carta e não possui os monstros nescessários para ativar sua invocação especial'); $this->inst = false; return 0;
                            }
                        }
			 else {$sacrificios = $sacrificios - 2;}
			}
		}
 	$arq = fopen($this->pasta.'/sacrificios.txt', 'w');
	fwrite($arq, $sacrificios);
	fclose($arq);
	}
	elseif($tipo == 'especial' || $tipo == 'controle') { // processo de Invocação especial
		 if($modo == 3) {return 0;}
                 parent::manter('invocação', 'especial');
		}
		 $this->nome = $monstro->nome;
		 $this->lv = $monstro->lv;
		 $this->atk = $monstro->atk;
		 $this->def = $monstro->def;
		 $this->atributo = $monstro->atributo;
		 $this->specie = $monstro->specie;
		 $this->id = $monstro->id;
		 $this->modo = $modo;
                 if($tipo == 'comum') file_put_contents($this->pasta.'/m_invocado.txt', $this->inst);
		 unset($monstro);
                 
   $gatilho[0] = 'monstro';
   $gatilho[1] = 'invocação';
   $gatilho[2] = $tipo;
   if(parent::checar($gatilho)) {
    $carta = &parent::checar($gatilho);
    $resposta = $carta->acionar($gatilho, $this);
    if($resposta['bloqueado']) {
      return false;
    }
   }

		 $infos = "$this->nome\n$this->lv\n$this->atk\n$this->def\n$this->atributo\n$this->specie\n$this->id\n$this->modo";
		 $arq = fopen($this->pasta.$this->inst.'.txt', 'w');
		 fwrite($arq, $infos);
	 	 fclose($arq);
                 parent::manter('invocado_em',parent::ler_turno());
		
		 $grav = new Gravacao();
	  	$grav->set_caminho($this->pasta.'/campo.txt');
	   $campo = $grav->ler(0);
		 	$campo[$local] = $this->inst;
	  $grav->set_array($campo);
	  $grav->gravar();
	  unset($grav);
	
     if($this->modo != 4) {
      parent::avisar($this->nome.' foi invocado', 1);
     }
     else {parent::avisar('Um monstro face para baixo foi invocado', 1);}
     return 1;
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
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' e perdeu', 1);
	   return true;
	  }
            if($resposta['resultado'] == 'D') { // uma derrota, mas deve ser removido do jogo
                parent::manter('atacou_em', parent::ler_turno());
                if($alvo->modo == 1) {parent::remover_do_jogo();}
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
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' que foi destruído', 1);
           parent::manter('atacou_em', parent::ler_turno());
           return true;
	  }
	 }
         return false;
        }
        else {
         $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
         $flag['SEM_DANO_DIRETO_POR_BATALHA'] = true;
         return $equipamento->atacar($alvo, $this, $lps, $flag);
        }
       }
       
  private function _ativar() {
      $hand = $this->duelo->ler_mao($this->dono);
      $carta = new DB_cards();
      $y = 0;
      for($x = 1; $x < $hand[0]; $x++) {
          $carta->ler_id($hand[$x]);
          if($carta->specie == 'machine') {
              $lista_machine_hand[$y] = $hand[$x];
              $y++;
          }
      }
     $campo = $this->duelo->ler_campo($this->dono);
     $y = 0;
     for($x = 1; $x <= 5; $x++) {
       $carta->ler_id($campo[$x][1]);
       if($carta->specie == 'machine' && $campo[$x][1] != 0) {
           $lista_machine_campo[$y] = $campo[$x][1];
           $y++;
       }
     }
        
     $cmt = $this->duelo->ler_cmt($this->dono);
     $y = 0;
     for($x = 1; $x < $cmt[0]; $x++) {
      $carta->ler_id($cmt[$x]);
      if($carta->specie == 'machine') {
           $lista_machine_cmt[$y] = $cmt[$x];
           $y++;
      }
     }
     if(!isset($lista_machine_hand) && !isset($lista_machine_campo) && !isset($lista_machine_cmt)) {return false;}
      // checando best-warrior
      $hand = $this->duelo->ler_mao($this->dono);
      $carta = new DB_cards();
      $y = 0;
      for($x = 1; $x < $hand[0]; $x++) {
          $carta->ler_id($hand[$x]);
          if($carta->specie == 'beast-warrior' && $hand[$x] != 59) {
              $lista_beastwarrior_hand[$y] = $hand[$x];
              $y++;
          }
      }
     $campo = $this->duelo->ler_campo($this->dono);
     $y = 0;
     for($x = 1; $x <= 5; $x++) {
       $carta->ler_id($campo[$x][1]);
       if($carta->specie == 'beast-warrior' && $campo[$x][1] != 59 && $campo[$x][1] != 0) {
           $lista_beastwarrior_campo[$y] = $campo[$x][1];
           $y++;
       }
     }
        
     $cmt = $this->duelo->ler_cmt($this->dono);
     $y = 0;
     for($x = 1; $x < $cmt[0]; $x++) {
      $carta->ler_id($cmt[$x]);
      if($carta->specie == 'beast-warrior' && $cmt[$x] != 59) {
           $lista_beastwarrior_cmt[$y] = $cmt[$x];
           $y++;
      }
     }
     if(!isset($lista_beastwarrior_hand) && !isset($lista_beastwarrior_campo) && !isset($lista_beastwarrior_cmt)) {return false;}
$this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'destruir');
$this->duelo->agendar_tarefa($this->inst, $this->dono, 'battle_phase', 'destruir');
$this->duelo->agendar_tarefa($this->inst, $this->dono, 'm1_phase', 'destruir');
$this->duelo->agendar_tarefa($this->inst, $this->dono, 'm2_phase', 'destruir');

$y = 0;
if(isset($lista_machine_hand)) {
    $texto = 'MAQUINA: MÃO';
    for($x = 0; $x < count($lista_machine_hand); $x++) {
        $lista[$y] = $lista_machine_hand[$x];
        $y++;
    }
}
if(isset($lista_machine_campo)) {
    if($y > 0) {
        $texto .= '|CAMPO';
        $lista[$y] = 'divisor';
        $y++;
    }
    else {$texto = 'MAQUINA: CAMPO';}
    for($x = 0; $x < count($lista_machine_campo); $x++) {
        $lista[$y] = $lista_machine_campo[$x];
        $y++;
    }
}
if(isset($lista_machine_cmt)) {
    if($y > 0) {
        $texto .= '|CEMITÉRIO';
        $lista[$y] = 'divisor';
        $y++;
    }
    else {$texto = 'MAQUINA: CEMITÉRIO';}
    for($x = 0; $x < count($lista_machine_cmt); $x++) {
        $lista[$y] = $lista_machine_cmt[$x];
        $y++;
    }
}
$this->duelo->solicitar_carta($texto, $lista, $this->dono, $this->inst);
return true;
  }
       
function carta_solicitada($cartaS) {
 if(parent::ler_variavel('carta_solicitada') == 0) {
  $hand = $this->duelo->ler_mao($this->dono);
  $carta = new DB_cards();
  $y = 0;
 for($x = 1; $x < $hand[0]; $x++) {
  $carta->ler_id($hand[$x]);
  if($carta->specie == 'machine') {
   $lista_machine_hand[$y] = $hand[$x];
   $y++;
  }
 }
 $campo = $this->duelo->ler_campo($this->dono);
 $y = 0;
 for($x = 1; $x <= 5; $x++) {
  $carta->ler_id($campo[$x][1]);
  if($carta->specie == 'machine' && $campo[$x][1] != 0) {
   $lista_machine_campo[$y] = $campo[$x][1];
   $y++;
  }
 }
        
 $cmt = $this->duelo->ler_cmt($this->dono);
 $y = 0;
 for($x = 1; $x < $cmt[0]; $x++) {
  $carta->ler_id($cmt[$x]);
  if($carta->specie == 'machine') {
    $lista_machine_cmt[$y] = $cmt[$x];
    $y++;
  }
 }
 if(!isset($lista_machine_hand) && !isset($lista_machine_campo) && !isset($lista_machine_cmt)) {return false;}
     
 $y = 0;
 if(isset($lista_machine_hand)) {
  for($x = 0; $x < count($lista_machine_hand); $x++) {
    $lista[$y] = $lista_machine_hand[$x];
    $y++;
  }
 }
 if(isset($lista_machine_campo)) {
  if($y > 0) {
   $lista[$y] = 'divisor';
   $y++;
  }
  for($x = 0; $x < count($lista_machine_campo); $x++) {
    $lista[$y] = $lista_machine_campo[$x];
    $y++;
  }
 }
 if(isset($lista_machine_cmt)) {
  if($y > 0) {
    $lista[$y] = 'divisor';
    $y++;
  }
  for($x = 0; $x < count($lista_machine_cmt); $x++) {
    $lista[$y] = $lista_machine_cmt[$x];
    $y++;
  }
 }
 if($lista[$cartaS] == 'divisor' || !$lista[$cartaS]) {return false;}
 $onde = 1;
 for($x = 0; $x < $cartaS; $x++) {if($lista[$x] == 'divisor') {$onde++;}}
 if(isset($lista_machine_hand)) {
  if($onde == 1) {
    for($x = 1; $hand[$x] != $lista_machine_hand[$cartaS]; $x++){}
    $this->duelo->apagar_carta_hand($this->dono, $x);
  }
 }
 if(isset($lista_machine_campo)) {
  if($onde == 1 && !isset($lista_machine_hand)) {
    $quantas = 0;
    for($x = 0; $x <= $cartaS; $x++){
     if($lista_machine_campo[$x] == $lista_machine_campo[$cartaS]) {$quantas++;}
    }
    for($x = 1; $x <= 5; $x++) {
     if($campo[$x][1] == $lista_machine_campo[$cartaS]) {
      if($quantas > 1) {$quantas--;}
      else {break;}
     }
    }
    $alvo = $this->duelo->regenerar_instancia_local($x, $this->dono);
    $alvo->remover_do_jogo();
  }
    elseif($onde == 2 && isset($lista_machine_hand)) {
        $cartaS = $cartaS - count($lista_machine_hand) - 1;
        $quantas = 0;
        for($x = 0; $x <= $cartaS; $x++){
         if($lista_machine_campo[$x] == $lista_machine_campo[$cartaS]) {$quantas++;}
        }
        for($x = 1; $x <= 5; $x++) {
         if($campo[$x][1] == $lista_machine_campo[$cartaS]) {
             if($quantas > 1) {$quantas--;}
             else {break;}
         }
        }
        $alvo = $this->duelo->regenerar_instancia_local($x, $this->dono);
        $alvo->remover_do_jogo();
    }
}
if(isset($lista_machine_cmt)) {
    if($onde == 1 && !isset($lista_machine_hand) && !isset($lista_machine_campo)) {
        $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
    }
    elseif($onde == 2 && isset($lista_machine_hand)) {
        $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
    }
    elseif($onde == 2 && !isset($lista_machine_hand)) {
        $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
    }
    elseif($onde == 3) {
        $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
    }
}
parent::manter('carta_solicitada', 1);
//solicitado o warrior beast
      $hand = $this->duelo->ler_mao($this->dono);
      $carta = new DB_cards();
      $y = 0;
      for($x = 1; $x < $hand[0]; $x++) {
          $carta->ler_id($hand[$x]);
          if($carta->specie == 'beast-warrior' && $hand[$x] != 59) {
              $lista_beastwarrior_hand[$y] = $hand[$x];
              $y++;
          }
      }
     $campo = $this->duelo->ler_campo($this->dono);
     $y = 0;
     for($x = 1; $x <= 5; $x++) {
       $carta->ler_id($campo[$x][1]);
       if($carta->specie == 'beast-warrior' && $campo[$x][1] != 59 && $campo[$x][1] != 0) {
           $lista_beastwarrior_campo[$y] = $campo[$x][1];
           $y++;
       }
     }
        
     $cmt = $this->duelo->ler_cmt($this->dono);
     $y = 0;
     for($x = 1; $x < $cmt[0]; $x++) {
      $carta->ler_id($cmt[$x]);
      if($carta->specie == 'beast-warrior' && $cmt[$x] != 59) {
           $lista_beastwarrior_cmt[$y] = $cmt[$x];
           $y++;
      }
     }

unset($lista);
     $y = 0;
if(isset($lista_beastwarrior_hand)) {
    $texto = 'BEAST: MÃO';
    for($x = 0; $x < count($lista_beastwarrior_hand); $x++) {
        $lista[$y] = $lista_beastwarrior_hand[$x];
        $y++;
    }
}
if(isset($lista_beastwarrior_campo)) {
    if($y > 0) {
        $texto .= '|CAMPO';
        $lista[$y] = 'divisor';
        $y++;
    }
    else {$texto = 'BEAST: CAMPO';}
    for($x = 0; $x < count($lista_beastwarrior_campo); $x++) {
        $lista[$y] = $lista_beastwarrior_campo[$x];
        $y++;
    }
}
if(isset($lista_beastwarrior_cmt)) {
    if($y > 0) {
        $texto .= '|CEMITÉRIO';
        $lista[$y] = 'divisor';
        $y++;
    }
    else {$texto = 'BEAST: CEMITÉRIO';}
    for($x = 0; $x < count($lista_beastwarrior_cmt); $x++) {
        $lista[$y] = $lista_beastwarrior_cmt[$x];
        $y++;
    }
}
$this->duelo->solicitar_carta($texto, $lista, $this->dono, $this->inst);
  return true;
   }
   else { // segunda carta foi escolhida
      $hand = $this->duelo->ler_mao($this->dono);
      $carta = new DB_cards();
      $y = 0;
      for($x = 1; $x < $hand[0]; $x++) {
          $carta->ler_id($hand[$x]);
          if($carta->specie == 'beast-warrior' && $hand[$x] != 59) {
              $lista_beastwarrior_hand[$y] = $hand[$x];
              $y++;
          }
      }
     $campo = $this->duelo->ler_campo($this->dono);
     $y = 0;
     for($x = 1; $x <= 5; $x++) {
       $carta->ler_id($campo[$x][1]);
       if($carta->specie == 'beast-warrior' && $campo[$x][1] != 59 && $campo[$x][1] != 0) {
           $lista_beastwarrior_campo[$y] = $campo[$x][1];
           $y++;
       }
     }
        
     $cmt = $this->duelo->ler_cmt($this->dono);
     $y = 0;
     for($x = 1; $x < $cmt[0]; $x++) {
      $carta->ler_id($cmt[$x]);
      if($carta->specie == 'beast-warrior' && $cmt[$x] != 59) {
           $lista_beastwarrior_cmt[$y] = $cmt[$x];
           $y++;
      }
     }
     if(!isset($lista_beastwarrior_hand) && !isset($lista_beastwarrior_campo) && !isset($lista_beastwarrior_cmt)) {return false;}
     
     $y = 0;
if(isset($lista_beastwarrior_hand)) {
    for($x = 0; $x < count($lista_beastwarrior_hand); $x++) {
        $lista[$y] = $lista_beastwarrior_hand[$x];
        $y++;
    }
}
if(isset($lista_beastwarrior_campo)) {
    if($y > 0) {
        $lista[$y] = 'divisor';
        $y++;
    }
    for($x = 0; $x < count($lista_beastwarrior_campo); $x++) {
        $lista[$y] = $lista_beastwarrior_campo[$x];
        $y++;
    }
}
if(isset($lista_beastwarrior_cmt)) {
    if($y > 0) {
        $lista[$y] = 'divisor';
        $y++;
    }
    for($x = 0; $x < count($lista_beastwarrior_cmt); $x++) {
        $lista[$y] = $lista_beastwarrior_cmt[$x];
        $y++;
    }
}
if($lista[$cartaS] == 'divisor' || !$lista[$cartaS]) {return false;}
$onde = 1;
for($x = 0; $x < $cartaS; $x++) {if($lista[$x] == 'divisor') {$onde++;}}
if(isset($lista_beastwarrior_hand)) {
    if($onde == 1) {
        for($x = 1; $hand[$x] != $lista_beastwarrior_hand[$cartaS]; $x++){}
        $this->duelo->apagar_carta_hand($this->dono, $x);
        parent::manter('carta_solicitada2', 1);
        return true;
    }
}
if(isset($lista_beastwarrior_campo)) {
    if($onde == 1 && !isset($lista_beastwarrior_hand)) {
        $quantas = 0;
        for($x = 0; $x <= $cartaS; $x++){
         if($lista_beastwarrior_campo[$x] == $lista_beastwarrior_campo[$cartaS]) {$quantas++;}
        }
        for($x = 1; $x <= 5; $x++) {
         if($campo[$x][1] == $lista_beastwarrior_campo[$cartaS]) {
             if($quantas > 1) {$quantas--;}
             else {break;}
         }
        }
        $alvo = $this->duelo->regenerar_instancia_local($x, $this->dono);
        $alvo->remover_do_jogo();
        parent::manter('carta_solicitada2', 1);
        return true;
    }
    elseif($onde == 2 && isset($lista_beastwarrior_hand)) {
        $cartaS = $cartaS - count($lista_beastwarrior_hand) - 1;
        $quantas = 0;
        for($x = 0; $x <= $cartaS; $x++){
         if($lista_beastwarrior_campo[$x] == $lista_beastwarrior_campo[$cartaS]) {$quantas++;}
        }
        for($x = 1; $x <= 5; $x++) {
         if($campo[$x][1] == $lista_beastwarrior_campo[$cartaS]) {
             if($quantas > 1) {$quantas--;}
             else {break;}
         }
        }
        $alvo = $this->duelo->regenerar_instancia_local($x, $this->dono);
        $alvo->remover_do_jogo();
        parent::manter('carta_solicitada2', 1);
        return true;
    }
}
if(isset($lista_beastwarrior_cmt)) {
    if($onde == 1 && !isset($lista_beastwarrior_hand) && !isset($lista_beastwarrior_campo)) {
        $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
        parent::manter('carta_solicitada2', 1);
        return true;
    }
    elseif($onde == 2 && isset($lista_beastwarrior_hand)) {
        $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
        parent::manter('carta_solicitada2', 1);
        return true;
    }
    elseif($onde == 2 && !isset($lista_beastwarrior_hand)) {
        $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
        parent::manter('carta_solicitada2', 1);
        return true;
    }
    elseif($onde == 3) {
        $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
        parent::manter('carta_solicitada2', 1);
        return true;
    }
}
   }
 }
 
 function tarefa($txt) {
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm1_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm2_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'battle_phase', $this->inst);
     if(parent::ler_variavel('carta_solicitada2') == 0) {parent::destruir();}
     return true;
 }
}
?>