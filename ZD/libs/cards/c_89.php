<?php

/* terminado dia 07/09/2016 terminado em 2 dias
 * Uma vez por turno, você pode selecionar um monstro no campo do seu oponente.
 * O monstro selecionado não pode mudar a posição de batalha ou declarar um ataque
 * até o fim do próximo turno do seu oponente.
 */

class c_89 extends Monstro_normal {
    
 function ativar_efeito() {
     if(parent::ler_variavel('ativou_em') == $this->duelo->ler_turno()) {
         parent::avisar('Esse efeito já foi ativado nesse turno');
         return false;
     }
   $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0) {
           if($campo[$x][0] == MODOS::ATAQUE) $lista[$y] = $campo[$x][1];
           else if($campo[$x][0] == MODOS::DEFESA) $lista[$y] = 'd'.$campo[$x][1];
           else $lista[$y] = 'db';
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
      $alvo->manter('modo_alterado_em', parent::ler_turno()+1);
      $alvo->manter('atacou_em', parent::ler_turno()+1);
      if($alvo->modo == MODOS::DEFESA_BAIXO) parent::avisar('Efeito da carta '.$this->nome.' ativado, um mosntro virado para baixo foi afetado.', 1);
      else parent::avisar('Efeito da carta '.$this->nome.' ativado, '.$alvo->nome.' foi afetado.', 1);
      parent::manter('ativou_em', $this->duelo->ler_turno());
      return true;
 }
 
 function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   if(parent::ler_variavel('ativou_em') == $this->duelo->ler_turno()) $comandos_possiveis['ativar'] = false;
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
 
}
?>