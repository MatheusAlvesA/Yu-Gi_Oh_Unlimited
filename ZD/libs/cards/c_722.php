<?php
//terminada dia 10/03/2016
// terminada em 1 minuto por já existir outras iguais
/*
 * Este monstro de ritual só pode ser Ritual Summon com a carta spell ritual, (Curse of the Masked Beast).
 */

class c_722 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'ritual' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do Curse of the Masked Beast');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    } 
}

?>
