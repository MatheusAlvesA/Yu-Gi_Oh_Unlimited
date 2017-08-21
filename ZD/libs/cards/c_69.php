<?php
/*
 * Este monstro somente pode ser Ritual Summoned com a Carta Spell Ritual (BlackLuster Ritual).
 */

class c_69 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'ritual' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do BlackLuster Ritual');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    } 
}
?>