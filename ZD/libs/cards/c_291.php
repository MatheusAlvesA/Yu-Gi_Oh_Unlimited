<?php
//terminada dia 15/06/2017
// terminada em 1 minuto por já existir outras iguais
/*
 * Este carta só pode ser invocada por ritual com a carta mágica de ritual, (Garma Sword Oath).
 */

class c_291 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'ritual' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do Garma Sword Oath');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    } 
}

?>