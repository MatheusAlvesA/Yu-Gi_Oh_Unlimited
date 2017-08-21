<?php
class c_175 extends Monstro_normal {
    /* terminada dia 24/03/2017. terminada em 1 dia
     * Esta carta ganha 300 de ATK para cada (Dark Magician) ou (Magician of Black Chaos)
     * no Cemitério de qualquer dos jogadores.
    */
    
   function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
       $retorno = parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
       if($retorno === 1) {
           parent::manter('ataque_original', $this->atk);
           $this->duelo->set_engine($this->inst, $this->dono);
       }
       return $retorno;
   }
   
   function engine() {
       $N = $this->contar();
       
       $ataque_original = (int)parent::ler_variavel('ataque_original');
       
       $grav = new Gravacao;
       $grav->set_caminho($this->pasta.$this->inst.'.txt');
       $infos = $grav->ler(0);
       $infos[3] = $ataque_original + $N*300;
       $grav->set_array($infos);
       $grav->gravar();
       $this->atk = $infos[3];
       
       return true;
   }
   
   function contar() {
       $cmt_dono = $this->duelo->ler_cmt($this->dono);
       $cmt_oponente = $this->duelo->ler_cmt($this->duelo->oponente($this->dono));
       $N_dono = 0;
       $N_oponente = 0;
       
       
       for($loop = 1; $loop < $cmt_dono[0];$loop++) {
          if($cmt_dono[$loop] == 174 || $cmt_dono[$loop] == 454) $N_dono++;
       }
       for($loop = 1; $loop < $cmt_oponente[0];$loop++) {
          if($cmt_oponente[$loop] == 174 || $cmt_oponente[$loop] == 454) $N_oponente++;
       }
       
       return ($N_dono+$N_oponente);
   }
   
     function sofrer_efeito($efeito, &$inst) {
            if($efeito[0] == 'incrementar_ATK') {
                parent::manter('ataque_original', (int)  parent::ler_variavel('ataque_original')+$efeito[1]);
                return true;
            }
            else {return parent::sofrer_efeito($efeito, $inst);}
  }
   
}
?>