<?php
//terminada dia 01/03/2016
// terminada em 1 minuto por já existir outras iguais
/*
 * Este monstro só pode ser invocado por ritual com a carta mágica de ritual (Turtle Oath).
 * Você também deve oferecer monstros cujo total Level em estrelas igual ou superior a 8
 * como um tributo a partir do campo ou a sua mão.
 */

class c_145 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'ritual' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do Turtle Oath');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    } 
}

?>
