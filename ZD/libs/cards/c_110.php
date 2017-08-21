<?php
class c_110 extends Magica {
/* terminada dia 21/09/2016. terminada em 1 dia
 * Vire todos os monstros no campo que estão virados para baixo para cima.
 * Inflija 500 de dano ao seu oponente para cada Effect Monster no campo.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  parent::avisar('Efeito da carta '.$this->nome.' foi ativado', 1);
  $efeito[0] = 'mudar_modo';
  $efeito[1] = MODOS::DEFESA;
  
  $campoDono = $this->duelo->ler_campo($this->dono);
  $campoOponente = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
  
  $monstro = new DB_cards();
  $monstros_effect = 0;
  for($x = 1; $x <= 5; $x++) {
      
      if($campoDono[$x][1] != 0) {
          $monstro->ler_id($campoDono[$x][1]);
          if($campoDono[$x][0] == MODOS::DEFESA_BAIXO) {
              $instancia = $this->duelo->regenerar_instancia_local($x, $this->dono);
              if($instancia->sofrer_efeito($efeito, $this) === 'destruido') {parent::destruir();}
              unset($instancia);
          }
          if($monstro->tipo == 'effect') $monstros_effect++;
      }
      
      if($campoOponente[$x][1] != 0) {
          $monstro->ler_id($campoOponente[$x][1]);
          if($campoOponente[$x][0] == MODOS::DEFESA_BAIXO) {
              $instancia = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
              if($instancia->sofrer_efeito($efeito, $this) === 'destruido') {parent::destruir();}
              unset($instancia);
          }
        if($monstro->tipo == 'effect') $monstros_effect++;
      }
      
  }
   parent::dano_direto($this->duelo->dir_duelo.$this->duelo->oponente($this->dono).'/lps.txt', $monstros_effect*500);
   parent::avisar($monstros_effect.' monstros effects encontrados. '.($monstros_effect*500).' de dano foi causado ao oponente', 1);
   parent::destruir();
   return true;
 }
}
?>