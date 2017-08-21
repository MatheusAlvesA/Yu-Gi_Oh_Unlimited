<?php
class c_5 extends Magica {
/*Destrua todos os monstros virados para cima Tipo Machine no campo.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  parent::avisar('Efeito da carta '.$this->nome.' foi ativado', 1);
  $efeito[0] = 'destruir';
  $campoDono = $this->duelo->ler_campo($this->dono);
  $campoOponente = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
  $monstro = new DB_cards();
  for($x = 1; $x <= 5; $x++) {
      if($campoDono[$x][1] != 0 && $campoDono[$x][0] != MODOS::DEFESA_BAIXO) {
          $monstro->ler_id($campoDono[$x][1]);
          if($monstro->specie == 'machine') {
              $instancia = $this->duelo->regenerar_instancia_local($x, $this->dono);
              if($instancia->sofrer_efeito($efeito, $this) === 'bloqueado') {parent::destruir();}
              unset($instancia);
          }
      }
      if($campoOponente[$x][1] != 0 && $campoOponente[$x][0] != MODOS::DEFESA_BAIXO) {
          $monstro->ler_id($campoOponente[$x][1]);
          if($monstro->specie == 'machine') {
              $instancia = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
              if($instancia->sofrer_efeito($efeito, $this) === 'bloqueado') {parent::destruir();}
              unset($instancia);
          }
      }
  }

   parent::destruir();
   return true;
 }
}
?>