<?php

/*
 * Este monstro só pode ser invocado por ritual coma carta mágica de ritual, (Beastly Mirror Ritual).
 */

class c_251 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'ritual' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do Beastly Mirror Ritual');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    } 
}
?>