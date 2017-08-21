<?php
class c_133 extends Monstro_normal {
/* carta terminada dem 12/02/2017 terminada dem 2 dias
 * Você pode Tributar essa carta, Special Summon 1 monstro (Constellar) da sua Mão ou Cemitério,
 * virado para cima em posição de Defesa, exceto (Constellar Resalhague).*/
    
	function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
		if(file_exists($this->pasta.'constellar_invoc_adicional.txt')) {
			@unlink($this->pasta.'constellar_invoc_adicional.txt');
			$flags['ignorar']['invocou'] = true;
		}
		return parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
 	}
    
  function ativar_efeito() {  // Efeito desse monstro
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'invocar';
    $gatilho[3] = 'self';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    
    $hand = $this->duelo->ler_mao($this->dono);
    $y = 0;
    for($x = 1; $x < $hand[0]; $x++) {
        if($this->isConstellar($hand[$x])) { // id de um monstro constellar
            $lista_hand[$y] = $hand[$x];
            $y++;
        }
    }
    $cmt = $this->duelo->ler_cmt($this->dono);
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
        if($this->isConstellar($cmt[$x])) { // id de um monstro constellar
            $lista_cmt[$y] = $cmt[$x];
            $y++;
        }
    }

    $texto = 'Escolha um Constellar';
    $y = 0;
    
    if(count($lista_hand) > 0) {
        $texto .= ' da MÃƒO';
        for($x = 0; $x < count($lista_hand); $x++) {
            $lista[$y] = $lista_hand[$x];
            $y++;
        }
    }
    if(count($lista_cmt) > 0) {
        if($y > 0) {
            $texto .= '|CEMITÃ‰RIO';
            $lista[$y] = 'divisor';
            $y++;
        }
        else $texto .= ' do CEMITÃ‰RIO';
        for($x = 0; $x < count($lista_cmt); $x++) {
            $lista[$y] = $lista_cmt[$x];
            $y++;
        }
    }
    
    if(!isset($lista)) {
        parent::avisar('NÃ£o existem monstros do tipo Constelar na sua mÃ£o ou cemitÃ©rio.');
        return false;
    }
    
    $this->duelo->solicitar_carta($texto, $lista, $this->dono, $this->inst);
   return true;
  }
 
  function carta_solicitada($cartaS) {
    $hand = $this->duelo->ler_mao($this->dono);
    $y = 0;
    for($x = 1; $x < $hand[0]; $x++) {
        if($this->isConstellar($hand[$x])) { // id de um monstro constellar
            $lista_hand[$y] = $hand[$x];
            $y++;
        }
    }
    $cmt = $this->duelo->ler_cmt($this->dono);
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
        if($this->isConstellar($cmt[$x])) { // id de um monstro constellar
            $lista_cmt[$y] = $cmt[$x];
            $y++;
        }
    }

    $y = 0;
    
    if(count($lista_hand) > 0) {
        for($x = 0; $x < count($lista_hand); $x++) {
            $lista[$y] = $lista_hand[$x];
            $y++;
        }
    }
    if(count($lista_cmt) > 0) {
        if($y > 0) {
            $lista[$y] = 'divisor';
            $divisor = $y;
            $y++;
        }
        for($x = 0; $x < count($lista_cmt); $x++) {
            $lista[$y] = $lista_cmt[$x];
            $y++;
        }
    }
    if(!isset($lista) || !isset($lista[$cartaS]) || !$lista[$cartaS] == 'divisor') {
        parent::avisar('Erro ao ativar');
        return false;
    }
    //ativando o efeito
   $campo = $this->duelo->ler_campo($this->dono);
    for($x = 1; $campo[$x][1] != 0 && $x <= 5; $x++) {}
    if($x > 5) {
        parent::avisar('NÃ£o existe espaÃ§o para invocar a carta');
        return false;
    }
    
   parent::avisar('Efeito da carta '.$this->nome.' ativado', 1);
   $flag['nÃ£o_mudar_modo'] = true;
   $this->duelo->invocar($this->dono, $x, MODOS::DEFESA, $lista[$cartaS], 'especial', $flag);
   if(isset($divisor)) {
    if($cartaS < $divisor) parent::excluir_carta_hand('id', $lista[$cartaS]);
    else $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
   }
   else {
       if(count($lista_hand) > 0) parent::excluir_carta_hand('id', $lista[$cartaS]);
       else $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
   }
   
   parent::destruir();
   return true;
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

 function isConstellar($id) {
     if($id >= 124 && $id <= 139 && $id != 131 && $id != 133 && $id != 137) return true;
     else return false;
 }
 // o atacar e o atacado estÃ£o sendo sobrecarregados para ativar o efeito da 131
 function atacar(&$alvo, $lps, $checar = true) {
	 $arq = fopen($this->pasta.'/phase.txt', 'r');
	 $phase = fgets($arq);
	 fclose($arq);
	 if($phase != 4) {parent::avisar('Você não pode atacar fora da BattlePhase'); return 0;}
	 if(parent::ler_turno() <= 1) {parent::avisar('Você não pode atacar neste turno'); return 0;}
	 if(parent::ler_variavel('atacou_em') == parent::ler_turno()) {parent::avisar('Você não pode atacar mais de uma vez com o mesmo monstro no mesmo turno'); return 0;}
         if($this->modo != 1) {parent::avisar('Movimento inválido. Você só pode atacar se estiver em modo de ataque'); return 0;}
         if(parent::ler_variavel('equip') == 0) { // se nÃ£o tiver equipamento
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
          $ataque['atacante'] = $this; //m o alvo pode precisar saber quem estÃ¡ atacando
	  $ataque[1] = $this->atk;
	  $resposta = $alvo->atacado($ataque);
	  if($resposta['resultado'] == 'd') {
           $this->constelarMeteor($alvo); // ativando o efeito da armadilha 131
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
             $this->constelarMeteor($alvo); // ativando o efeito da armadilha 131
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
	   if($alvo->modo == 1) {$this->destruir();}
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' e ambos foram destrídos', 1);
	   return true;
	  }
            elseif($resposta['resultado'] == 'E') { // foi um empate, mas deve ser removido
             if($alvo->modo == 1) {parent::remover_do_jogo();}
             parent::avisar($this->nome.' atacou '.$alvo->nome.' e ambos foram destruídos. Essa carta foi removida do jogo', 1);
             return true;
            }
	  elseif($resposta['resultado'] == 'n') {
           $this->constelarMeteor($alvo); // ativando o efeito da armadilha 131
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' mas não ouve efeito', 1);
	   parent::manter('atacou_em', parent::ler_turno()); 
	   return true;
	  }
	  elseif($resposta['resultado'] == 'v') {
           if($alvo->modo == MODOS::ATAQUE) parent::dano_direto($lps, $resposta['sobra']);
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' que foi destruido', 1);
           parent::manter('atacou_em', parent::ler_turno());
           return true;
	  }
	 }
         return false;
        }
        else {
         $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
         $r = $equipamento->atacar($alvo, $this, $lps);
         if($r['resultado'] !== 'v') $this->constelarMeteor($alvo);
         return $r;
        }
}
       
function atacado($ataque) {
if(parent::ler_variavel('equip') == 0) { // se nÃ£o estiver equipado
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
   $this->destruir();
   $resposta['resultado'] = 'v';
   $this->constelarMeteor($ataque['atacante']); // ativando o efeito da armadilha 131
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
   $this->destruir();
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
    $resposta['resultado'] = 'n';
    $this->constelarMeteor($ataque['atacante']); // ativando o efeito da armadilha 131
  }
 }
 $resposta['sobra'] = $sobra * -1;
 return $resposta;
}
else {
 $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
 $r = $equipamento->atacado($ataque, $this);
 if($r['resultado'] == 'v' || $r['resultado'] == 'v' ) $this->constelarMeteor ($ataque['atacante']); // ativando o efeito da armadilha 131
 return $r;
}
}
  function constelarMeteor($instancia) { // plugin que ativa o efeito da carta 131
     if(!file_exists($this->pasta.'131_efeito.txt')) return false;
     $meteor_cod = file_get_contents($this->pasta.'131_efeito.txt');
     $meteor = $this->duelo->regenerar_instancia($meteor_cod, $this->dono);
     $meteor->retornar($instancia);
     return true;
 }
}
?>