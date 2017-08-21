<?php
class c_176 extends Monstro_normal {
    /* terminada dia 25/03/2017. terminada em 1 dia
     * Quando esta carta é Normal Summoned ou Special Summoned,
     * você pode selecionar e adicionar 1 Carta Spell do seu Cemitério a sua mão.
     * Monstros que essa carta destroi por batalha são removidos do jogo ao invés de irem para o Cemitério.
     * Se essa carta é destruida por batalha, ela é removida do jogo.
    */
    
   function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
       $retorno = parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
       if($retorno === 1) {
           $cmt = $this->duelo->ler_cmt($dono);
           $carta = new DB_cards;
           $lista = array();
           $y = 0;
           for($x = 1; $x < $cmt[0];$x++) {
               $carta->ler_id($cmt[$x]);
               if($carta->categoria == 'spell') {
                   $lista[$y] = $carta->id;
                   $y++;
               }
           }
           if($y > 0) $this->duelo->solicitar_carta('Escolha uma mágica', $lista, $dono, $this->inst);
       }
       return $retorno;
   }
   
   function carta_solicitada($cartaS) {
       if(parent::ler_variavel('ativou_em') !== false) return false;
           $cmt = $this->duelo->ler_cmt($this->dono);
           $carta = new DB_cards;
           $lista = array();
           $y = 0;
           for($x = 1; $x < $cmt[0];$x++) {
               $carta->ler_id($cmt[$x]);
               if($carta->categoria == 'spell') {
                   $lista[$y] = $carta->id;
                   $y++;
               }
           }
           if((int)$cartaS < 0 || (int)$cartaS >= $y) {
               parent::avisar('falha ao ativar');
               return false;
           }
           
           $this->duelo->apagar_cmt($lista[(int)$cartaS], $this->dono);
           $this->duelo->colocar_carta_hand($lista[(int)$cartaS], $this->dono);
           $carta->ler_id($lista[(int)$cartaS]);
           parent::avisar('Efeito da carta '.$this->nome.' ativado. '.$carta->nome.' foi movida para a mão');
           parent::manter('ativou_em', parent::ler_turno());
           return true;
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
	  if($resposta['resultado'] == 'd' || $resposta['resultado'] == 'D' ) {
             parent::manter('atacou_em', parent::ler_turno());
             if($alvo->modo == 1) {parent::remover_do_jogo();}
             else {
                $tool = new biblioteca_de_efeitos;
                $tool->dano_direto($this->dono, (-1)*$resposta['sobra']);
              }
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' e perdeu', 1);
	   return true;
	  }
	  elseif($resposta['resultado'] == 'e' || $resposta['resultado'] == 'E' ) {
	   if($alvo->modo == 1) {parent::remover_do_jogo();} //efeito
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' e ambos foram destruídos', 1);
           $this->duelo->apagar_cmt($alvo->id, $alvo->dono); //efeito
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
           $this->duelo->apagar_cmt($alvo->id, $alvo->dono); // efeito
           return true;
	  }
	 }
         return false;
        }
        else {
         $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
         $resposta = $equipamento->atacar($alvo, $this, $lps);
         
         if($resposta['resultado'] == 'v')
             $this->duelo->apagar_cmt($alvo->id, $alvo->dono); //efeito
         if($resposta['resultado'] == 'e') {
             $this->duelo->apagar_cmt($this->id, $this->dono);
             $this->duelo->apagar_cmt($alvo->id, $alvo->dono);
         }
         if($resposta['resultado'] == 'd')
             $this->duelo->apagar_cmt($this->id, $this->dono);
         
         return $resposta;
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
   parent::remover_do_jogo();
   $resposta['resultado'] = 'v';
 }
 elseif($sobra > 0) {
   $resposta['resultado'] = 'D';
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
   parent::remover_do_jogo();
   $resposta['resultado'] = 'E';
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
  }
 }
 $resposta['sobra'] = $sobra * -1;
 return $resposta;
}
else {
 $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
 $resposta = $equipamento->atacado($ataque, $this);
 //efeito dessa carta
 if($resposta['resultado'] == 'v') $this->duelo->apagar_cmt($this->id, $this->dono);
 if($resposta['resultado'] == 'e') {
     $this->duelo->apagar_cmt($this->id, $this->dono);
     $resposta['resultado'] = 'E';
 }
  if($resposta['resultado'] == 'd') $resposta['resultado'] = 'D';
  // fim do efeito
    return $resposta;
}
}
}
?>