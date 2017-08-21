<?php

/*
 * Esta carta não pode ser Normal Summoned ou invocada virada para baixo. 
 * Essa carta não pode ser Special Summoned exceto pelo efeito de (Armed Dragon LV5). 
 * Envie 1 Carta de Monstro de sua mão para o cemitério para destruir todos os 
 * monstros virados para cima do lado do campo de seu oponente com um ATK igual 
 * ou menor que o ATK do monstro enviado.
 */

class c_35 extends Monstro_normal {
    
    
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'LV5_sacrificado' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do Armed Dragon LV5');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    }
    
    function ativar_efeito() {
        $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_monstro';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
            $turno = $this->duelo->ler_turno();
            if(parent::ler_variavel('ativado_em') == $turno) {
             parent::avisar('Você só pode ativar esse efeito uma vez por turno!');
             return false;  
            }
      if(parent::ler_variavel('carta_solicitada') != 0) {
            parent::manter('ativado_em', $turno);
            parent::excluir_carta_hand('id', parent::ler_variavel('carta_solicitada'));
            $this->duelo->colocar_no_cemiterio(parent::ler_variavel('carta_solicitada'), $this->dono);
            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
            $monstro = new DB_cards();
            $monstro->ler_id(parent::ler_variavel('carta_solicitada'));
            parent::avisar('Efeito do monstro Armed Dragon LV7 ativado, foi selecionada a carta '.$monstro->nome, 1);
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_monstro';
            for($x = 1; $x <= 5; $x++) {
             if($campo[$x][1] != 0) {
               $monstro_alvo = new DB_cards();
               $monstro_alvo->ler_id($campo[$x][1]);
              if($monstro->atk > $monstro_alvo->atk && $campo[$x][0] != 4) {
               $alvo = &$this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
               $alvo->sofrer_efeito($efeito, $this);
              }
             }
            }
            parent::manter('carta_solicitada', 0);
            return true;
       }
    else {
     $hand = $this->duelo->ler_mao($this->dono);
     $carta = new DB_cards();
     $y = 0;
     for($x = 1; $x < $hand[0]; $x++) {
       $carta->ler_id($hand[$x]);
       if($carta->categoria == 'monster') {
           $lista[$y] = $hand[$x];
           $y++;
       }
     }
     if(!isset($lista)) {
      parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
      return false;
     }
     $this->duelo->solicitar_carta('Escolha um monstro da sua mão', $lista, $this->dono, $this->inst);
     return true;
    }
  }

  function carta_solicitada($cartaS) {
  if(parent::ler_variavel('carta_solicitada') == 0) {
  $hand = $this->duelo->ler_mao($this->dono);
  $carta = new DB_cards();
  $y = 0;
   for($x = 1; $x < $hand[0]; $x++) {
       $carta->ler_id($hand[$x]);
       if($carta->categoria == 'monster') {
           $lista[$y] = $hand[$x];
           $y++;
       }
    }
  if(!isset($lista)) {
   parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
   return false;
  }
  if(!isset($lista[$cartaS])) {return false;}
  parent::manter('carta_solicitada', $lista[$cartaS]);
  $this->ativar_efeito();
  return true;
   }
 }
 
 function sacrificar() {
     if(parent::sacrificar()) {
         $codigo = "@unlink(".'"'.$this->pasta.'Armed_Dragon_LV7_sacrificado.txt");'."\n";
         $codigo .= "@unlink(".'"'.$this->pasta.'script_'.$this->inst.'.txt'.'");';
         file_put_contents($this->pasta.'script_'.$this->inst.'.txt', $codigo);
      fclose(fopen($this->pasta.'Armed_Dragon_LV7_sacrificado.txt', 'w'));
      $this->duelo->agendar_tarefa('script_'.$this->inst.'.txt', $this->dono, 'end_phase', 0, true);
     }
 }
 
 function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   if(parent::ler_variavel('ativado_em') != $this->duelo->ler_turno()) $comandos_possiveis['ativar'] = true;
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
?>