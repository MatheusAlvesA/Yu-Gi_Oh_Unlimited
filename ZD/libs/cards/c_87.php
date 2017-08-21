<?php
/* terminado dia 04/09/2016 terminado em um dia
 * alterado dia 21/02/2017, é 30 e não 3
 * Ative apenas se há 30 ou mais cartas no seu Cemitério. Inflija 3000 de dano no seu oponente.
 */

class c_87 extends Armadilha {
	function invocar($local, $id, $modo, $dono, $tipo, $gatilho = false) {
  	parent::invocar($local, $id, $modo, $dono, $tipo);
 }

 function ativar_efeito() {
  if(!parent::checar_ativar()) {return false;}
  $gatilho[0] = 'armadilha';
  $gatilho[1] = 'efeito';
  $gatilho[2] = 'dano';
  $gatilho[3] = 'oponente';
  if(parent::checar($gatilho)) {
    $carta = &parent::checar($gatilho);
    $resposta = $carta->acionar($gatilho, $this);
    if($resposta['bloqueado']) {
      parent::destruir();
      return false;
    }
  }
     if($this->duelo->ler_Ncmt($this->dono) >= 30) {
         parent::dano_direto($this->duelo->dir_duelo.$this->duelo->oponente($this->dono).'/lps.txt', 3000);
         parent::avisar('Efeito da carta '.$this->nome.' foi ativado, 3000 de dano foi causado ao oponente', 1);
         parent::destruir();
     }
     else {
         parent::avisar('Você precisa ter 30 ou mais cartas no cemitério');
         return false;
     }
 }
}
?>
