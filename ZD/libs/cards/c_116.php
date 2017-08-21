<?php
class c_116 extends Monstro_normal {
/* terminado dia 25/09/2016. terminado em 1 dia
 * O ATK desta carta torna-se o número de Cartas de Monstro no seu Cemitério x 300.*/

    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
        parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
        $this->duelo->set_engine($this->inst);
    }
    
 function engine() {
  $cmt = $this->duelo->ler_cmt($this->dono);
  $carta = new DB_cards();
  
  $adicional = 0;
  for($x = 1; $x < $cmt[0]; $x++) {
      $carta->ler_id($cmt[$x]);
      if($carta->categoria === 'monster') $adicional += 300;
  }
        $grav = new Gravacao();
	$grav->set_caminho($this->pasta.$this->inst.'.txt');
	$infos = $grav->ler(0);
        $infos[3] = $adicional;
        $grav->set_array($infos);
        $grav->gravar();
	unset($grav);
      
   return true;
 }

}
?>