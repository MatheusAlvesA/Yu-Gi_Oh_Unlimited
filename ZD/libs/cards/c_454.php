<?php
//terminada dia 01/07/2016
// terminada em 1 minuto por já existir outras iguais
/*
 * Esta carta só pode ser Ritual Summon com a carta spell ritual, (Black Magic Ritual).
 */

class c_454 extends Monstro_normal {
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
