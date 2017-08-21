<?php

/* carta terminada dia 18/02/2017. terminada em 3 dias
 * Uma vez por turno, você pode remover 1 monstro Constellar do seu cemitério,
 * então selecionar 1 monstro Constellar no seu cemitério e adicionar a sua mão.
 * Se o fizer também essa carta ganha o seguinte efeito:
 * Normal Summon 1 monstro Constellar em adição a sua Invocação padrão do turno.
 * (Esse efeito pode ser usado apenas uma vez por turno, independente se você possuir mais de uma cópia dessa carta)
 */
class c_136 extends Monstro_normal {
	
	function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
		if(file_exists($this->pasta.'constellar_invoc_adicional.txt')) {
			@unlink($this->pasta.'constellar_invoc_adicional.txt');
			$flags['ignorar']['invocou'] = true;
		}
		return parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
 	}
	
function ativar_efeito() {  // Efeito desse monstro
    if(parent::ler_variavel('ativado_em') == $this->ler_turno()) {
        parent::avisar('Esse efeito já foi ativado nesse turno');
        return false;
    }
    
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'ressucitar_carta';
    $gatilho[3] = 'mão';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    
    // formando lista do cemiterio
        $cmt = $this->duelo->ler_cmt($this->dono);
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
        if($this->isConstellar($cmt[$x])) { // id de um monstro constellar
            $lista_cmt[$y] = $cmt[$x];
            $y++;
        }
    }
    
 if(!isset($lista_cmt) || count($lista_cmt) < 2) {
     parent::avisar('Não é possível ativar esse efeito sem ao menos dois constelares em seu cemitério');
     return false;
 }
    
    $this->duelo->solicitar_carta("Escolha um Constellar para remover do jogo", $lista_cmt, $this->dono, $this->inst);
   return true;
  }
 
  function carta_solicitada($cartaS) {
  	//nesse caso pecisa guardar a carta a ser destruida e perguntar pela carta a ser ressucitada
if(parent::ler_variavel('ser_destruida') == '') {
    // formando lista do cemiterio
        $cmt = $this->duelo->ler_cmt($this->dono);
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
        if($this->isConstellar($cmt[$x])) { // id de um monstro constellar
            $lista_cmt[$y] = $cmt[$x];
            $y++;
        }
    }
    
 if(!isset($lista_cmt) || !isset($lista_cmt[$cartaS])) {
     parent::avisar('Não existem outros Constelares em seu campo ou cemitério');
     return false;
 }
 
 parent::manter('ser_destruida', $cartaS);
    // a carta a ser sacrificada está definida. hora de perguntar qual vai ser resucitada
    $y=0;
    for($x=0;$x<count($lista_cmt);$x++) {
    	if($x != $cartaS) {
    		$nova_lista[$y] = $lista_cmt[$x];
    		$y++;
    	}
    }
    
        $this->duelo->solicitar_carta("Escolha um Constellar para retornar a mão", $nova_lista, $this->dono, $this->inst);
}
else {
        //todo agora receber o que é pra ressucitar
    $cmt = $this->duelo->ler_cmt($this->dono);
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
        if($this->isConstellar($cmt[$x])) { // id de um monstro constellar
            $lista_cmt[$y] = $cmt[$x];
            $y++;
        }
    }
    
 if(!isset($lista_cmt)) {
     parent::avisar('Não existem outros Constelares em seu cemitério');
     return false;
 }
 
     $y=0;
    for($x=0;$x<count($lista_cmt);$x++) {
    	if($x != parent::ler_variavel('ser_destruida')) {
    		$nova_lista[$y] = $lista_cmt[$x];
    		$y++;
    	}
    }
    if(!isset($nova_lista[$cartaS])) return false;
    
    // temos as duas cartas, ativando efeito
    $this->duelo->apagar_cmt($lista_cmt[parent::ler_variavel('ser_destruida')], $this->dono);
    $this->duelo->apagar_cmt($nova_lista[$cartaS], $this->dono);
    $this->duelo->colocar_carta_hand($nova_lista[$cartaS], $this->dono);
    file_put_contents($this->pasta.'constellar_invoc_adicional.txt', $this->inst);
    
    $sacrificio = new DB_cards;
    $sacrificio->ler_id($lista_cmt[parent::ler_variavel('ser_destruida')]);
    $monstro = new DB_cards;
    $monstro->ler_id($nova_lista[$cartaS]);
    
    parent::avisar('Efeito da carta '.$this->nome.' ativado. '.$sacrificio->nome.' foi removido e '.$monstro->nome.' foi movido para a mão. Um constellar pode ser normal summon adicionalmente nesse turno', 1);
    parent::manter('ser_destruida', '');
    parent::manter('ativado_em', parent::ler_turno());
    $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
}
      return true;
  }
  
  function tarefa($txt){
         parent::manter('ser_destruida', '');
  	if(file_exists($this->pasta.'constellar_invoc_adicional.txt')) @unlink($this->pasta.'constellar_invoc_adicional.txt');
  }
  
   function isConstellar($id) {
     if($id >= 124 && $id <= 139 && $id != 131 && $id != 137) return true;
     else return false;
 }
 
 function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   if(parent::ler_variavel('ativado_em') == $this->ler_turno()) $comandos_possiveis['ativar'] = false;
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