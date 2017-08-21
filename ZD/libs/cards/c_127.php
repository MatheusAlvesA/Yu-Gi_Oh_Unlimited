<?php

/* carta terminada dia 29/09/2016. terminada em 1 dia
 * Quando essa carta é Normal Summon: Você pode Special Summon 1 monstro (Constellar) da sua mão virado para cima.
 */
class c_127 extends Monstro_normal {

 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
 	
 		if(file_exists($this->pasta.'constellar_invoc_adicional.txt')) {
			@unlink($this->pasta.'constellar_invoc_adicional.txt');
			$flags['ignorar']['invocou'] = true;
		}
		
     $retorno = parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
     if($tipo == 'comum') $this->_ativar();
     return $retorno;
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
    $y = 0;
    $achou = false; // encontrar ele mesma na mão
    for($x = 1; $x < $mao[0]; $x++) {
        if($this->isConstellar($mao[$x])) { // id de um monstro constellar
         if(($mao[$x] == $this->id && $achou) || $mao[$x] != $this->id) {
            $lista[$y] = $mao[$x];
            $y++;
        } else {$achou = true;}
    }
    }
    if(!isset($lista)) return false;
    
    $this->duelo->solicitar_carta("Escolha um Constellar", $lista, $this->dono, $this->inst);
   return true;
  }
 
  function carta_solicitada($cartaS) {
    $mao = $this->duelo->ler_mao($this->dono);
    $y = 0;
    for($x = 1; $x < $mao[0]; $x++) {
        if($this->isConstellar($mao[$x])) { // id de um monstro constellar
            $lista[$y] = $mao[$x];
            $y++;
        }
    }
    if(!isset($lista) || !$lista[$cartaS]) return false;
    
    $campo = $this->duelo->ler_campo($this->dono);
    for($i = 1; $i <= 5 && $campo[$i][1] != 0; $i++);
    if($i > 5) {
        parent::avisar('Você não tem espaço em campo');
        return false;
    }
    
    parent::excluir_carta_hand('id', $lista[$cartaS]);
    $this->duelo->invocar($this->dono, $i, MODOS::ATAQUE, $lista[$cartaS], 'especial');
    $carta = new DB_cards;
    $carta->ler_id($lista[$cartaS]);
    parent::avisar('Efeito do monstro '.$this->nome.' ativado, '.$carta->nome.' foi invocado', 1);
    
      return true;
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