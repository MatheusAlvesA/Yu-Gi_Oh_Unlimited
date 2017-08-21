<?php
//terminada dia 01/06/2017
// terminada em 1 minuto por já existir outras iguais
/*
 * Este monstro só pode ser invocado por um ritual com a carta mágica de ritual, (Fortress Whales Oath).
 */

class c_275 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'ritual' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do Black Magic Ritual');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    } 
}

?>
