<?php

/* carta terminada dia 26/09/2016. terminada em 1 dia
 * Este carta só pode ser invocada por ritual com a carta Mágica de ritual, (Commencement Dance).
 * Você também deve oferecer um monstro como tributo cujo nível seja igual ou superior a 6 do campo ou da sua mão.
 */

class c_555 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'Commencement Dance' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do Commencement Dance');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    } 
}
?>