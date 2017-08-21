<?php

/* carta terminada dia 23/09/2016. terminada em 1 dia
 * Você pode Ritual Summon essa carta utilizando (Resurrection of Chakra).
 */

class c_114 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'Resurrection of Chakra' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do Resurrection of Chakra');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    } 
}
?>