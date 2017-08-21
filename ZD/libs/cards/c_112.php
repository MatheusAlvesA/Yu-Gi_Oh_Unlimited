<?php

/* terminado em 22/07/2016; em 1 dia
 * Inflija 100 de dano no seu oponente para cada carta no Cemitério dele.
 */

class c_112 extends Armadilha {

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) return false;
parent::mudar_modo(MODOS::ATAQUE);
  $vetor_cmt = $this->duelo->ler_cmt($this->duelo->oponente($this->dono));
  $i = new DB_cards;
  $dano = 0;
  for($x = 1; $x < $vetor_cmt[0];$x++) {
      $i->ler_id($vetor_cmt[$x]);
      if($i->categoria == 'monster') {
          $dano += 100;
      }
  }
  
  parent::dano_direto($this->duelo->dir_duelo.$this->duelo->oponente($this->dono).'/lps.txt', $dano);
  parent::avisar('Carta '.$this->nome.' ativada, '.$dano.' de dano foi causado ao oponente', 1);
  parent::destruir();
   return true;
 }
 
}
?>