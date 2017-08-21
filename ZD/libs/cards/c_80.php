<?php

/* terminado dia 10/07/2016 em 1 dia
 * Se você controla um monstro (Blackwing) virado para cima,
 * você pode Normal Summon esta carta sem Tributar. Quando essa carta é Normal Summoned, 
 * você pode alterar a Posição de Batalha de 1 monstro que o seu oponente controle.
 */

class c_80 extends Monstro_normal {

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
    $campo = $this->duelo->ler_campo($dono);
    $efeito_on = false;
     for($x = 1; $x <= 5; $x++) {
         if(($campo[$x][1] >= 79 && $campo[$x][1] <= 85) && ($campo[$x][0] == MODOS::ATAQUE || $campo[$x][0] == MODOS::DEFESA)) {
             $efeito_on = true;
             break;
         }
     }
     if(!$efeito_on) {
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
  unset($monstro);
  $this->dono = $dono;
  $this->inst = uniqid();
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
  $arq = fopen($this->pasta.'_'.$this->inst.'.txt', 'w');
  fwrite($arq, 'invocado_em;'.parent::ler_turno());
  fclose($arq);
		
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
  if($flags['não_mudar_modo'] === true) parent::manter ('modo_alterado_em', parent::ler_turno());
  if($tipo == 'comum') $this->_ativar();
    return 1;
 }
    
 function _ativar() {
     if(parent::ler_variavel('NEGAR_EFEITO') == 1) {return false;}
   $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0) {
         switch ($campo[$x][0]) {
             case 1:
                 $lista[$y] = $campo[$x][1];
             break;
             case 2:
                 $lista[$y] = $campo[$x][1];
             break;
             default :
                 $lista[$y] = 'db';
         }
           $y++;
       }
   }
   if(!$lista || parent::ler_variavel('ativou') === 'sim') {parent::avisar('aaa');return false;}
   
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
   if(!$lista || !$lista[$cartaS] || parent::ler_variavel('ativou') === 'sim') {return false;}
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
    if($alvo->modo == MODOS::ATAQUE || $alvo->modo == MODOS::DEFESA_BAIXO) $modo = MODOS::DEFESA;
    else $modo = MODOS::ATAQUE;
      $grav = new Gravacao();
      $grav->set_caminho($alvo->pasta.$alvo->inst.'.txt');
      $infos = $grav->ler(0);
      $infos[8] = $modo;
      $grav->set_array($infos);
      $grav->gravar();
      unset($grav);
      parent::avisar('Efeito da carta '.$this->nome.' ativado, '.$alvo->nome.' foi afetado.', 1);
      parent::manter('ativou', 'sim');
      return true;
 }
 
}
?>