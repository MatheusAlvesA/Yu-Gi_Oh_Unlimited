<?php
class c_241 extends Monstro_normal {
/* carta terminada dem 18/05/2017 terminada em 1 dia
 * Quando você possuir (Right Leg of the Forbidden One), (Left Leg of the Forbidden One),
 * (Right Arm of the Forbidden One), (Left Arm of the Forbidden One) em adição a essa carta em seu campo,
 * você vence o Duelo (A menos que seu oponente tenha Obelisk The Tormentor supremo em campo).
*/

 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
     $r = parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
     if($r && ($flags === FALSE || $flags['engine'] !== 'n')) {
         $this->duelo->set_engine($this->inst, $dono);
         parent::manter('momento', 0);
     }
     return $r;
 }
 
 function engine() {
     if(parent::ler_variavel('momento') != 0) {
         if(time()-parent::ler_variavel('momento') >= 10) {
             $this->duelo->alterar_lp(-1*$this->duelo->ler_lps($this->duelo->oponente($this->dono)), $this->duelo->oponente($this->dono));
         }
         return true;
     }
     if($this->checar_exodia()) {
         file_put_contents($this->pasta.'campo.txt', "0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0");
         
         $this->duelo->invocar($this->dono, 1, MODOS::ATAQUE, 609, 'especial');
         $this->duelo->invocar($this->dono, 5, MODOS::ATAQUE, 425, 'especial');
         $flag = array();
         $flag['engine'] = 'n';
         $this->duelo->invocar($this->dono, 3, MODOS::ATAQUE, 241, 'especial', $flag);
         $this->duelo->invocar($this->dono, 2, MODOS::ATAQUE, 608, 'especial');
         $this->duelo->invocar($this->dono, 4, MODOS::ATAQUE, 424, 'especial');
         
         parent::manter('momento', time());
         parent::avisar('EXODIA INVOCADO !!!', 1);
     }
     return true;
 }
 
 private function checar_exodia() {
     $l_hand = false;
     $l_leg = false;
     $r_hand = false;
     $r_leg = false;
     
     $hand = $this->duelo->ler_mao($this->dono);
     $campo = $this->duelo->ler_campo($this->dono);
     
     for($x = 1;$x < $hand[0]; $x++) {
             switch ($hand[$x]) {
                case 609:
                    $r_leg = 'H';
                    break;
                case 425:
                    $l_leg = 'H';
                    break;
                case 608:
                    $r_hand = 'H';
                    break;
                case 424:
                    $l_hand = 'H';
                    break;
             }
     }
     
     for($x = 1;$x <= 5; $x++) {
         if($campo[$x][1] !== 0) {
             switch ($campo[$x][1]) {
                case 609:
                    $r_leg = 'C';
                    break;
                case 425:
                    $l_leg = 'C';
                    break;
                case 608:
                    $r_hand = 'C';
                    break;
                case 424:
                    $l_hand = 'C';
                    break;
             }
         }
     }
     
     if($l_hand === false || $l_leg === false || $r_hand === false || $r_leg === false) return false;
     
     $tool = new biblioteca_de_efeitos;
     if($l_hand === 'H') $tool->apagar_carta_mao(424, $this->dono, $this->duelo);
     if($l_leg === 'H') $tool->apagar_carta_mao(425, $this->dono, $this->duelo);
     if($r_hand === 'H') $tool->apagar_carta_mao(608, $this->dono, $this->duelo);
     if($r_leg === 'H') $tool->apagar_carta_mao(609, $this->dono, $this->duelo);
     
     return true;
 }
 
}