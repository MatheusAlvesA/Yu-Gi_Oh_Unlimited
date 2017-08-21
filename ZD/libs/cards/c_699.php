<?php
/* terminada dia 25/03/2017 terminada em 1 dia
 * Se esta é a única carta na sua mão, você a pode Summon virada para cima em Modo de Ataque sem Tributar monstros.
 * Isso é tratado como uma Normal Summon.
 */

class c_699 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo === 'comum') {
          $hand = $this->duelo->ler_mao($dono);
          if($hand[0] == 1) {
             if(!file_exists($this->pasta.'/sacrificios.txt')) fclose(fopen ($this->pasta.'/sacrificios.txt', 'w+'));
             $sacrificios = (int)file_get_contents($this->pasta.'/sacrificios.txt');
             file_put_contents($this->pasta.'/sacrificios.txt', $sacrificios+2);;
          }
      }
      return parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
    } 
}
?>