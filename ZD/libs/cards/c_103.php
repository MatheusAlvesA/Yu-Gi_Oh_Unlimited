<?php
class c_103 extends Magica {
/* terminado dia 12/09/2016. terminado em 1 dia
 * Cada monstro virado para cima que o seu oponente controla perde 100 de ATK x seu próprio Nível.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  parent::avisar('Efeito da carta '.$this->nome.' foi ativado', 1);
  
  $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
  $campoCods = $this->duelo->ler_campo_cods($this->duelo->oponente($this->dono));
  for($x = 1; $x <= 5; $x++) {
      if($campo[$x][1] != 0 && $campo[$x][0] != MODOS::DEFESA_BAIXO) {
          $resposta = $this->afetar($campoCods[$x]);
          if(is_array($resposta) && $resposta['bloqueado'] === true) break;
      }
  }

   parent::destruir();
   return true;
 }
 
 function afetar($inst) {
  $efeito[0] = 'incrementar_ATK';
  $efeito[1] = -100;
  $monstro = $this->duelo->regenerar_instancia($inst, $this->duelo->oponente($this->dono));
  $efeito[1] *= $monstro->lv;
  if($monstro->atk + $efeito[1] < 0) $efeito[1] = -1*$monstro->atk;
  return $monstro->sofrer_efeito($efeito, $this);
 }

}
?>