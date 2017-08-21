<?php

/*Ative somente se o único monstro virado para cima que você controla é um monstro (X-Saber).
 *Após a ativação, esta carta é tratada como um Equip Card e é equipada naquele monstro. 
 *Ele ganha 800 de ATK. Se o monstro equipado destruir um monstro do seu oponente em batalha, compre 1 carta.
 */

class c_41 extends Armadilha {

 function ativar_efeito() {  // unica função dessa armadilha
  if(!parent::checar_ativar()) {return false;}
  if(parent::ler_variavel('equipado_em') != 0) {return false;}
   $campo = $this->duelo->ler_campo($this->dono);
   $upCartas = 0;
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][0] == 1) {
           $upCartas++;
           $carta = $campo[$x][1];
           $posicao = $x;
       }
   }
    $XSaber = false;
     for($y = 1; $y < $this->lista_XSaber[0]; $y++) {
        if($carta == $this->lista_XSaber[$y]) {
            $XSaber = true;
        }
   }
   if($upCartas != 1 || !$XSaber) {
       parent::avisar('É necessário ter apenas um monstro virado para cima X-Saber no campo');
       parent::destruir();
       return false;
   }
   $monstro = &$this->duelo->regenerar_instancia_local($posicao, $this->dono);
   if(!$monstro->set_equip($this->inst)) {
       parent::avisar('O monstro não pode receber equipamento');
       parent::destruir();
       return false;
   }
   parent::avisar('A carta '.$this->nome.' foi ativada, o monstro '.$monstro->nome.' foi equipado', 1);
   $this->mudar_modo(1);
   parent::manter('equipado_em', $monstro->inst);
   return true;
 }
 
 function atacar(&$alvo, &$monstro, $lps, $flags = false) {
     $monstro->atk += 800; // efeito
   if($alvo == 'direto_n' && $flags['ATAQUE_DIRETO_EFEITO'] !== true) {parent::avisar('Você não pode atacar o oponente diretamente se o campo dele não estiver vazio'); return 0;}
   elseif($alvo == 'direto_n') $alvo = 'direto_s';
   if($alvo == 'direto_s') {
   $gatilho[0] = 'monstro';
   $gatilho[1] = 'ataque';
   $gatilho[2] = 'incrementado';
   $gatilho[3] = 'direto';
   if(parent::checar($gatilho)) {
    $carta = &parent::checar($gatilho);
    $resposta = $carta->acionar($gatilho, $monstro);
    if($resposta['bloqueado']) {return false;}
   }
          $monstro->duelo->alterar_lp((-1)*$monstro->atk, $monstro->duelo->oponente($monstro->dono));
          $monstro->manter('atacou_em', parent::ler_turno()); 
	  parent::avisar($monstro->nome.' atacou os pontos de vida diretamente', 1);
	  return true;
	 }
	 else {
		$gatilho[0] = 'monstro';
	  $gatilho[1] = 'ataque';
	  $gatilho[2] = 'incrementado';
	  $gatilho[3] = 'monstro';
	  $gatilho[4] = $alvo->inst;
	  if(parent::checar($gatilho)) {
		 $carta = parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $monstro);
		  if($resposta['bloqueado']) {
                      parent::manter('atacou_em', parent::ler_turno());
                      return $resposta;
                  }
		}
	  $ataque['tipo'] = 'i';
          $ataque['atacante'] = $monstro; //O alvo pode precisar saber quem está atacando
	  $ataque[1] = $monstro->atk-800;
          $ataque[2] = 800;
	  $resposta = $alvo->atacado($ataque);
	  if($resposta['resultado'] == 'd') {
           $monstro->manter('atacou_em', parent::ler_turno());
           if($alvo->modo == 1) {$monstro->destruir();}
           elseif($flags === false || !$flags['SEM_DANO_AO_DONO_POR_BATALHA']) {
               $tool = new biblioteca_de_efeitos;
               $tool->dano_direto($monstro->dono, (-1)*$resposta['sobra']);
           }
		 parent::avisar($monstro->nome.' atacou '.$alvo->nome.' e perdeu', 1);
		 return $resposta;
		}
		elseif($resposta['resultado'] == 'e') {
		 if($alvo->modo == 1) {$monstro->destruir();}
		 parent::avisar($monstro->nome.' atacou '.$alvo->nome.' e ambos foram destruídos', 1);
                 $monstro->duelo->puxar_carta($monstro->dono); // efeito
		 return $resposta;
			}
		elseif($resposta['resultado'] == 'n') {
	   parent::avisar($monstro->nome.' atacou '.$alvo->nome.' mas não ouve efeito', 1);
		 $monstro->manter('atacou_em', parent::ler_turno()); 
		 return $resposta;
		}
		elseif($resposta['resultado'] == 'v') {
                   if($flags['SOBRA_ALTERADA']) {
                      if($resposta['sobra']+$flags['SOBRA_ALTERADA'] < 0) $resposta['sobra'] = 0; // efeito ativado
                      else $resposta['sobra'] += $flags['SOBRA_ALTERADA'];
                      if($alvo->modo == MODOS::ATAQUE) parent::dano_direto($lps, $resposta['sobra']);
                    }
                    elseif($flags === false || !$flags['SEM_DANO_DIRETO_POR_BATALHA']) {parent::dano_direto($lps, $resposta['sobra']);}
	 parent::avisar($monstro->nome.' atacou '.$alvo->nome.' que foi destruído', 1);
   $monstro->manter('atacou_em', parent::ler_turno());
   $monstro->duelo->puxar_carta($monstro->dono); // efeito
   return $resposta;
			}
	 }
   return false;
 }
 
 function atacado($ataque, &$monstro) {
  if($monstro->modo == 1) {$valor = $monstro->atk;}
  else {$valor = $monstro->def;}
  if($ataque['tipo'] == 'c') {
    $sobra = $valor - $ataque[1];
  }
  elseif($ataque['tipo'] == 'i') {
   $atk = 0;
   for($x = 1; $x < count($ataque); $x++) {$atk = $atk + $ataque[$x];}
   $sobra = $valor - $atk;
  }
  if($sobra < 0) {
   $monstro->destruir();
   $resposta['resultado'] = 'v';
  }
  elseif($sobra > 0) {
   $resposta['resultado'] = 'd';
   $grav = new Gravacao();
   $grav->set_caminho($monstro->pasta.$monstro->inst.'.txt');
   $infos = $grav->ler(0);
   if($infos[8] == 4) {
    $infos[8] = 2;
    $grav->set_array($infos);
    $grav->gravar();
   }
   $this->duelo->puxar_carta($this->dono); // efeito
   unset($grav);
  }
  elseif($monstro->modo == 1) {
   $monstro->destruir();
   $resposta['resultado'] = 'e';
   $this->duelo->puxar_carta($this->dono); // efeito
  }
  else {
   $grav = new Gravacao();
   $grav->set_caminho($monstro->pasta.$monstro->inst.'.txt');
   $infos = $grav->ler(0);
   if($infos[8] == 4) {
    $infos[8] = 2;
    $grav->set_array($infos);
    $grav->gravar();
   }
   $resposta['resultado'] = 'n';
  }
  $resposta['sobra'] = $sobra * -1;
   return $resposta;
  }
  
 function monstro_sofrer_efeito($efeito, &$inst, &$monstro) {
  if($efeito[0] == 'destruir') {
    parent::avisar($monstro->nome.' foi destruido pelo efeito da carta '.$inst->nome, 1);
    $monstro->destruir();
    return true;
  }
    else if($efeito[0] == 'remover_do_jogo') {
        parent::avisar($monstro->nome.' foi removido do jogo pelo efeito da carta '.$inst->nome, 1);
        $monstro->remover_do_jogo();
        return true;
    }
  else if($efeito[0] == 'mudar_modo') {
                $grav = new Gravacao();
		$grav->set_caminho($monstro->pasta.$monstro->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[8] = $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->modo = $efeito[1];
                return true;
  }
 else if($efeito[0] == 'incrementar_LV') {
                $grav = new Gravacao();
		$grav->set_caminho($monstro->pasta.$monstro->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[2] += $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->lv += $efeito[1];
                return true;
            }
 else if($efeito[0] == 'incrementar_ATK') {
                $grav = new Gravacao();
		$grav->set_caminho($monstro->pasta.$monstro->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[3] += $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->atk += $efeito[1];
                return true;
            }
            else if($efeito[0] == 'incrementar_DEF') {
                $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[4] += $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->atk += $efeito[1];
                return true;
            }
  return false;
 }
 
 function get_atk(&$monstro) {
     return $monstro->atk + 800;
 }
 
  function get_def(&$monstro) {
     return $monstro->def;
 }
  function get_lv(&$monstro) {return $monstro->lv;}
 
 function monstro_destruido() {
    parent::destruir();
    return true;
 }
}
?>