<?php
class c_282 extends Armadilha {
 // carta terminada em 08/06/2017. criada em 1 dia
 /*
  * Após a ativação desta carta, envie toda a sua mão para o Cemitério.
  * Inflija 200 de dano no seu oponente para cada carta que você enviou para o Cemitério por esse efeito
  */

 function ativar_efeito() {  // unica função dessa armadilha
  if(!parent::checar_ativar()) {return false;}
  if(parent::ler_variavel('invocado_em') == parent::ler_turno()) {
      parent::avisar('Você não pode ativar essa armadilha imediatamente');
      return false;
  }
  
  parent::avisar('Efeito da carta armadilha '.$this->nome.' ativado', 1);
  
  $hand = $this->duelo->ler_mao($this->dono);
  $dano = 0;
  for($x = 1; $x < $hand[0];$x++) {
      $this->excluir_carta_hand('id', $hand[$x]);
      $this->duelo->colocar_no_cemiterio($hand[$x], $this->dono);
      $dano += 200;
  }
  $t = new biblioteca_de_efeitos();
  $t->dano_direto($this->duelo->oponente($this->dono), $dano);
  
  parent::destruir();
  return true;
 }
}
?>