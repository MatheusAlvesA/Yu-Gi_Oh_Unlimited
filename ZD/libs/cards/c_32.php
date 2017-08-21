<?php

/* 
 * Esta carta não pode ser Normal Summoned ou Set. Essa carta não pode ser 
 * Special Summoned exceto por se Tributar 1 (Armed Dragon LV7) no seu lado do campo. 
 * Por enviar 1 carta da sua mão para o Cemitério, destrua todos os monstros 
 * virados para cima no lado do campo do seu oponente.
 */

class c_32 extends Monstro_normal {
    
    
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if(!file_exists($this->pasta.'Armed_Dragon_LV7_sacrificado.txt') && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do sacrificio do Armed Dragon LV7');
          parent::destruir();
          return false;
      }
      @unlink($this->pasta.'Armed_Dragon_LV7_sacrificado.txt');
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
     file_put_contents($this->pasta.'/m_invocado.txt', $this->inst);
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
            parent::avisar('Efeito do monstro Armed Dragon LV10 ativado, foi selecionada a carta '.$monstro->nome, 1);
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_monstro';
            for($x = 1; $x <= 5; $x++) {
             if($campo[$x][1] != 0) {
               $monstro_alvo = new DB_cards();
               $monstro_alvo->ler_id($campo[$x][1]);
              if($campo[$x][0] != 4) {
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
           $lista[$y] = $hand[$x];
           $y++;
     }
     if(!isset($lista)) {
      parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
      return false;
     }
     $this->duelo->solicitar_carta('Escolha uma carta da sua mão', $lista, $this->dono, $this->inst);
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
           $lista[$y] = $hand[$x];
           $y++;
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
 
 function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   $comandos_possiveis['ativar'] = true;
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