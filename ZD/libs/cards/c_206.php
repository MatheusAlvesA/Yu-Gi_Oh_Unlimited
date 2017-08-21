<?php

/* carta terminada em 23/04/2017. terminada em 1 dia
 * Este monstro só pode ser invocado por ritual como Carta Mágica de ritual, (Revival of Dokurorider).
 * Você também deve oferecerm monstros cujo total Level em estrelas seja igual a 6 ou mais como um tributo
 * de seu campo ou a sua mão.
 */

class c_206 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'ritual' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do Revival of Dokurorider');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    } 
}
?>