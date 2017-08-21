<?php
class Monstro_normal extends Carta {
 var $nome;
 var $lv;
 var $atk;
 var $def;
 var $atributo;
 var $specie;
 var $inst;
 var $pasta;
 var $id;
 var $modo; //modos possiveis: 1 atk cima; 2 def cima; 3 atk baixo; 4 def baixo
 var $duelo;
 var $dono;
 
 var $MODOS;
 
	function __construct($duelo, $pasta, $inst = 0) {
            $this->MODOS = new MODOS;
		$this->pasta = $pasta;
	  $this->duelo = &$duelo;
	 if($inst) {
		 $this->inst = $inst;
		 $grav = new Gravacao();
		 $grav->set_caminho($this->pasta.$this->inst.'.txt');
		 $infos = $grav->ler(0);
		 unset($grav);
		$this->nome = $infos[1];
	  $this->lv = $infos[2];
	  $this->atk = $infos[3];
	  $this->def = $infos[4];
	  $this->atributo = $infos[5];
	  $this->specie = $infos[6];
	  $this->id = $infos[7];
	  $this->modo = $infos[8];
          $dono = explode('/', $pasta);
          $this->dono = $dono[2];
          if(parent::ler_variavel('equip') != 0 && !file_exists($this->pasta.parent::ler_variavel('equip').".txt")) {parent::manter('equip', 0);}
		}
		else {return 0;}
	}

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
       if($sacrificios < 2) {parent::avisar('Você não sacrificou monstros suficientes para invocar esta carta'); return 0;}
       else {$sacrificios = $sacrificios - 2;}
     }
   }
   $arq = fopen($this->pasta.'/sacrificios.txt', 'w');
   fwrite($arq, $sacrificios);
   fclose($arq);
  }
  elseif($tipo == 'especial' || $tipo == 'controle') { // processo de Invocação especial
    if($modo == 3) {return 0;}
    $this->inst = uniqid();
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
  unset($monstro);
  $this->dono = $dono;
  if($this->inst == '') $this->inst = uniqid();
  if($tipo == 'comum') file_put_contents($this->pasta.'/m_invocado.txt', $this->inst);
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
  if($flags['não_mudar_modo'] === true) parent::manter ('modo_alterado_em', parent::ler_turno());
		
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

function mudar_modo($modo) {
    $arq = fopen($this->pasta.'/phase.txt', 'r');
    $phase = fgets($arq);
    fclose($arq);
    if($phase != 3 && $phase != 5) {parent::avisar('Você não pode mudar a posição fora das MainPhases'); return 0;}
    if(parent::ler_variavel('invocado_em') == parent::ler_turno() && parent::ler_variavel('invocação') !== 'especial') {parent::avisar('Você não pode mudar a posição neste turno'); return 0;}
    if(parent::ler_variavel('atacou_em') == parent::ler_turno()) {parent::avisar('Você não pode mudar a posição depois de atacar'); return 0;}
    if(parent::ler_variavel('modo_alterado_em') == parent::ler_turno()) {parent::avisar('Você não pode mudar a posição mais de uma vez no mesmo turno'); return 0;}
    if($this->modo == 4 && $modo != 1) {parent::avisar('Movimento inválido. Você só pode alterar para modo de ataque neste momento'); return 0;}
    if($this->modo == 1 && $modo != 2) {parent::avisar('Movimento inválido. Você só pode alterar para modo de defesa neste momento'); return 0;}
    if($this->modo == 2 && $modo != 1) {parent::avisar('Movimento inválido. Você só pode alterar para modo de ataque neste momento'); return 0;}
    $grav = new Gravacao();
    $grav->set_caminho($this->pasta.$this->inst.'.txt');
    $infos = $grav->ler(0);
    $infos[8] = $modo;
    $grav->set_array($infos);
    $grav->gravar();
    unset($grav);
    $this->modo = $modo;
    parent::manter('modo_alterado_em', parent::ler_turno());
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
	   parent::avisar($this->nome.' atacou '.$alvo->nome.', mas nada aconteceu', 1);
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
         return $equipamento->atacar($alvo, $this, $lps);
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
   parent::destruir();
   $resposta['resultado'] = 'v';
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
   parent::destruir();
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
  }
  $resposta['resultado'] = 'n';
 }
 $resposta['sobra'] = $sobra * -1;
 return $resposta;
}
else {
 $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
 return $equipamento->atacado($ataque, $this);
}
}
	
  function sofrer_efeito($efeito, &$inst) {
          if(parent::ler_variavel('equip') == 0) { // se não estiver equipado
            if($efeito[0] == 'destruir') {
                parent::avisar($this->nome.' foi destruido pelo efeito da carta '.$inst->nome, 1);
                parent::destruir();
                return true;
            }
            else if($efeito[0] == 'remover_do_jogo') {
                parent::avisar($this->nome.' foi removido do jogo pelo efeito da carta '.$inst->nome, 1);
                parent::remover_do_jogo();
                return true;
            }
            else if($efeito[0] == 'mudar_modo') {
                $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
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
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
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
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
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
                $this->def += $efeito[1];
                return true;
            }
            else if($efeito[0] == 'voltar_deck') { // esse efeito retorna a carta para o deck
                parent::avisar($this->nome.' retornou para o deck pelo efeito da carta '.$inst->nome, 1);
                parent::remover_do_jogo();
                $this->duelo->colocar_no_deck($this->id, $this->dono);
                $this->duelo->embaralhar_deck($this->id, $this->dono);
                return true;
            }
            return false;
          }
          else {
             $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
             return $equipamento->monstro_sofrer_efeito($efeito, $inst, $this);
          }
  }
        
  function set_equip($inst) {
      if(parent::ler_variavel('equip') != 0) {return false;}
      parent::manter('equip', $inst);
      return true;
  }
  
  function get_atk() {
      if(parent::ler_variavel('equip') != 0) {
          $equip = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
          return $equip->get_atk($this);
      }
      else {return $this->atk;}
  }
  
    function get_def() {
      if(parent::ler_variavel('equip') != 0) {
          $equip = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
          return $equip->get_def($this);
      }
      else {return $this->def;}
  }
  
  function get_lv() {
      if(parent::ler_variavel('equip') != 0) {
          $equip = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
          return $equip->get_lv($this);
      }
      else {return $this->lv;}
  }
  
  function sacrificar() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
	$phase = fgets($arq);
	fclose($arq);
	if($phase != 3 && $phase != 5) {parent::avisar('Você não pode fazer sacrifícios fora das MainPhases'); return false;}
  $arq = fopen($this->pasta.'/sacrificios.txt', 'r');
	$sacrificios = fgets($arq);
	fclose($arq);
  $arq = fopen($this->pasta.'/sacrificios.txt', 'w');
	fwrite($arq, $sacrificios + 1);
	fclose($arq);
	return parent::destruir();
 }
 
 function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   $comandos_possiveis['ativar'] = false; // monstro normal não pode ativar efeito
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

class MODOS { // O codigo da classe monstro normal usa esse enum para se organizar melhor
    const ATAQUE = 1;
    const DEFESA = 2;
    const ATAQUE_BAIXO = 3;
    const DEFESA_BAIXO = 4;
}

?>