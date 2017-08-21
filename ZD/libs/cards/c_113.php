<?php
/* terminada dia 23/09/2016. terminada em 1 dia
 * Se esta carta ataca ou é atacada, seu oponente compra 1 carta no fim da Etapa de Dano.
 */
class c_113 extends Monstro_normal {

    function atacar(&$alvo, $lps, $checar = true) {
        $retorno = parent::atacar($alvo, $lps, $checar);
        if($retorno) $this->duelo->puxar_carta($this->duelo->oponente($this->dono));
        return $retorno;
    }
    function atacado($ataque) {
         $retorno = parent::atacado($ataque);
         $this->duelo->puxar_carta($this->duelo->oponente($this->dono));
         return $retorno;
    }
}
 ?>