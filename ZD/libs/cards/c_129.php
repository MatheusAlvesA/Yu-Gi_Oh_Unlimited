<?php

/* carta terminada dia 01/10/2016. terminada em 2 dias
 * Até duas vezes por turno: Você pode escolher 1 Monstro (Constellar) no campo para ativar 1 desses efeitos.
 * Ou aumente seus Level em 1.
 * Ou reduza seu Level em 1.
 */
class c_129 extends Monstro_normal {
	
		function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
		if(file_exists($this->pasta.'constellar_invoc_adicional.txt')) {
			@unlink($this->pasta.'constellar_invoc_adicional.txt');
			$flags['ignorar']['invocou'] = true;
		}
		return parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
 	}
    
  function ativar_efeito() {  // Efeito desse monstro
      if(parent::ler_variavel('ativado_em') == parent::ler_turno() && parent::ler_variavel('ativado_vezes') >= 2) return false;
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'alterar_lv_monstro_self';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    
    $cmt = $this->duelo->ler_campo($this->dono);
    $y = 0;
    for($x = 1; $x <= 5; $x++) {
        if($this->isConstellar($cmt[$x][1])) { // id de um monstro constellar
            $lista[$y] = $cmt[$x][1];
            $y++;
        }
    }
    if(!isset($lista)) return false;
    
    $this->duelo->solicitar_carta("Escolha um Constellar", $lista, $this->dono, $this->inst);
   return true;
  }
 
  function carta_solicitada($cartaS) {
        if(parent::ler_variavel('ativado_em') < parent::ler_turno()) parent::manter ('ativado_vezes', 0);
  if(parent::ler_variavel('carta_solicitada') == 0) {
    $cmt = $this->duelo->ler_campo($this->dono);
    $y = 0;
    for($x = 1; $x <= 5; $x++) {
        if($this->isConstellar($cmt[$x][1])) { // id de um monstro constellar
            $lista[$y] = $cmt[$x][1];
            $y++;
        }
    }
    if(!isset($lista) || !$lista[$cartaS]) return false;
  $quantas = 0;
   for($x = 0;$x <= $cartaS;$x++) {
       if($lista[$x] == $lista[$cartaS]) $quantas++;
   }
   for($x = 1;$x <= 5 && $quantas >= 1;$x++) {
       if($cmt[$x][1] == $lista[$cartaS]) $quantas--;
   }
   
   parent::manter('carta_solicitada', $x-1);
   unset($lista);
   $lista[0] = 'up'; // subir nivel
   $lista[1] = 'down'; // descer nivel
   $this->duelo->solicitar_carta("Escolha uma ação", $lista, $this->dono, $this->inst);
  } else {
    $carta = $this->duelo->regenerar_instancia_local(parent::ler_variavel('carta_solicitada'), $this->dono);
    $efeito[0] = 'incrementar_LV';
    if($cartaS == 0) {
        $efeito[1] = 1;
        $txt = 'aumentado';
    }
    else {
        if($carta->get_lv() > 0) $efeito[1] = -1;
        else $efeito[1] = 0;
        $txt = 'diminuido';
    }
    $carta->sofrer_efeito($efeito, $this);
    parent::avisar('Efeito do monstro '.$this->nome.' ativado, '.$carta->nome.' teve seu LV '.$txt, 1);
    parent::manter('ativado_vezes', (int)parent::ler_variavel('ativado_vezes') + 1);
    parent::manter('ativado_em', parent::ler_turno());
    parent::manter('carta_solicitada', 0);
  }
      return true;
  }
  
function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);

   if(parent::ler_variavel('ativado_em') == parent::ler_turno() && parent::ler_variavel('ativado_vezes') >= 2) $comandos_possiveis['ativar'] = false;
   else $comandos_possiveis['ativar'] = true;
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
     if($id >= 124 && $id <= 139 && $id != 131 && $id != 137) return true;
     else return false;
 }
   // o atacar e o atacado estão sendo sobrecarregados para ativar o efeito da 131
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
          $ataque['atacante'] = $this; //m o alvo pode precisar saber quem está atacando
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
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' e ambos foram destruídos', 1);
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
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' que foi destruído', 1);
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