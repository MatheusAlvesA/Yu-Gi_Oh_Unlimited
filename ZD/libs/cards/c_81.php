<?php

/* terminado dia 11/07/2016 terminado em 1 dia
 * Esta carta pode atacar seu oponente diretamente. 
 * Quando esta carta ataca seu oponente diretamente e inflige Dano de Batalha ao seu oponente, 
 * você pode mudar 1 monstro na Posição de Ataque o seu oponente controla para a Posição de Defesa. 
 * O monstro alvo não pode mudar a sua posição de batalha até a End Phase do próximo turno.
 */

class c_81 extends Monstro_normal {
    
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

         if($alvo == 'direto_n') { // efeito
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
           if($alvo->modo == MODOS::ATAQUE) parent::dano_direto($lps, $resposta['sobra']);
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' que foi destruído', 1);
           parent::manter('atacou_em', parent::ler_turno());
           return true;
	  }
	 }
         return false;
        }
        else {
         $flags['ATAQUE_DIRETO_EFEITO'] = true;
         $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
         return $equipamento->atacar($alvo, $this, $lps, $flags);
        }
       }
    
 function _ativar() {
     if(parent::ler_variavel('NEGAR_EFEITO') == 1) {return false;}
   $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0 && $campo[$x][0] == MODOS::ATAQUE) {
           $lista[$y] = $campo[$x][1];
           $y++;
       }
   }
   if(!$lista) {return false;}
   
  $this->duelo->solicitar_carta("ESCOLHA UM MONSTRO", $lista, $this->dono, $this->inst);
  return true;
 }

 function carta_solicitada($cartaS) {
   $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0) {
           $lista[$y] = $campo[$x][1];
           $y++;
       }
   }
   if(!$lista || !$lista[$cartaS]) {return false;}
   $quantas = 0;
   for($x = 0;$x <= $cartaS;$x++) {
       if($lista[$x] == $lista[$cartaS]) $quantas++;
   }
   for($x = 1;$x <= 5 && $quantas >= 1;$x++) {
       if($campo[$x][1] == $lista[$cartaS]) $quantas--;
   }
   
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    $alvo = $this->duelo->regenerar_instancia_local($x-1, $this->duelo->oponente($this->dono));
      $grav = new Gravacao();
      $grav->set_caminho($alvo->pasta.$alvo->inst.'.txt');
      $infos = $grav->ler(0);
      $infos[8] = MODOS::DEFESA;
      $grav->set_array($infos);
      $grav->gravar();
      unset($grav);
      $alvo->manter('modo_alterado_em', parent::ler_turno()+1);
      parent::avisar('Efeito da carta '.$this->nome.' ativado, '.$alvo->nome.' foi afetado.', 1);
      return true;
 }
 
}
?>