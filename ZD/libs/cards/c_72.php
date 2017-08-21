<?php
// terminado dia 02/07/2016 em um dia mesmo
// adicionado novo efeito dia 21/02/2017
/*O monstro equipado ganha 500 de ATK.
 Quando esta carta é destruida cause 500 de dano aos pontos de vida do oponente
 *  */

class c_72 extends Magica {

 function ativar_efeito() {
  if(!parent::checar_ativar()) {return false;}
  if(parent::ler_variavel('equipado_em') != 0) {return false;}
   $campo = $this->duelo->ler_campo($this->dono);
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][0] == 1 || $campo[$x][0] == 2) {
           $cartas[$y] = $campo[$x][1];
           $y++;
       }
   }
   if(!isset($cartas)) {
       parent::avisar('É necessário ter monstros no campo virados para cima');
       parent::destruir();
       return false;
   }
   $this->duelo->solicitar_carta('ESCOLHA UM MONSTRO', $cartas, $this->dono, $this->inst);
   return true;
 }
 
 function atacar(&$alvo, &$monstro, $lps, $flags = false) {
     $monstro->atk += 500; // efeito
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
    if($resposta['bloqueado']) {$monstro->manter('atacou_em', parent::ler_turno());return false;}
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
                      $monstro->manter('atacou_em', parent::ler_turno());
                      return $resposta;
                  }
		}
	  $ataque['tipo'] = 'i';
          $ataque['atacante'] = $monstro; //O alvo pode precisar saber quem está atacando
	  $ataque[1] = $monstro->atk-500;
          $ataque[2] = 500;
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
                    elseif(($flags === false || !$flags['SEM_DANO_DIRETO_POR_BATALHA']) && $alvo->modo == MODOS::ATAQUE) {parent::dano_direto($lps, $resposta['sobra']);}
   
	 parent::avisar($monstro->nome.' atacou '.$alvo->nome.' que foi destruído', 1);
   $monstro->manter('atacou_em', parent::ler_turno());
   return $resposta;
			}
	 }
   return false;
 }
 
 function atacado($ataque, &$monstro) {
   if($monstro->modo == 1) {$valor = $monstro->atk+500;}
   else {$valor = $monstro->def;}
   if($ataque['tipo'] == 'c')
    $sobra = $valor - $ataque[1];
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
    unset($grav);
   }
   elseif($monstro->modo == 1) {
    $monstro->destruir();
    $resposta['resultado'] = 'e';
   }
   else { // indentar e passar pra galera
     $grav = new Gravacao();
     $grav->set_caminho($monstro->pasta.$monstro->inst.'.txt');
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
  
 function monstro_sofrer_efeito($efeito, &$inst, &$monstro) {
  if($efeito[0] == 'destruir') {
    parent::avisar($monstro->nome.' foi destruido pelo efeito da carta '.$inst->nome, 1);
    $monstro->destruir('efeito');
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
                $monstro->modo = $efeito[1];
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
                $monstro->lv += $efeito[1];
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
                $monstro->atk += $efeito[1];
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
                $monstro->def += $efeito[1];
                return true;
            }
            else if($efeito[0] == 'voltar_deck') { // esse efeito retorna a carta para o deck
                parent::avisar($monstro->nome.' retornou para o deck pelo efeito da carta '.$inst->nome, 1);
                $monstro->remover_do_jogo();
                $this->duelo->colocar_no_deck($monstro->id, $this->dono);
                $this->duelo->embaralhar_deck(0, $this->dono);
                return true;
            }
  return false;
 }
 
 function get_atk(&$monstro) {
     return $monstro->atk + 500;
 }
  function get_def(&$monstro) {return $monstro->def;}
  function get_lv(&$monstro) {return $monstro->lv;}
 
 function monstro_destruido() {
    parent::dano_direto($this->duelo->dir_duelo.$this->duelo->oponente($this->dono).'/lps.txt', 500);
    parent::destruir();
    return true;
 }
 
 function carta_solicitada($cartaS) {
   if(parent::ler_variavel('equipado_em') != 0) {return false;}
   $campo = $this->duelo->ler_campo($this->dono);
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][0] == 1 || $campo[$x][0] == 2) {
           $cartas[$y] = $campo[$x][1];
           $y++;
       }
   }
   $quantas = 0;
   for($x = 0; $x < $y;$x++){
    if($cartas[$cartaS] == $cartas[$x]){
        $quantas++;
    }
   }
    for($x = 1; $x <= 5; $x++){
     if($cartas[$cartaS] == $campo[$x][1]){
        $quantas--;
        if($quantas == 0) break;
     }
    }
   $monstro = &$this->duelo->regenerar_instancia_local($x, $this->dono);
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
}

?>
