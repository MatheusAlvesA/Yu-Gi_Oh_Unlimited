<?php

/* carta terminada dia 19/05/2017. terminada em 1 dia
 * Quando esta carta é Normal Summoned ou modo especial:
 * você pode selecionar 1 monstro virado para cima que você controla, torna-se Nível 3.
 */
class c_247 extends Monstro_normal {

 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) { 	
     $r = parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
     if($r) {
         $campo = $this->duelo->ler_campo($dono);
         $lista = array();
         $y = 0;
         for($x = 1; $x <= 5; $x++) {
             if($campo[$x][1] != 0 && $x != $local) {
                 $lista[$y] = $campo[$x][1];
                 $y++;
             }
         }
         if($y > 0) {
             $this->duelo->solicitar_carta('Escolha um monstro', $lista, $dono, $this->inst);
             parent::manter('local', $local);
         }
     }
     return $r;
 }
 
 function carta_solicitada($cartaS) {
     if(parent::ler_variavel('local') == 0) return false;
         $campo = $this->duelo->ler_campo($this->dono);
         $lista = array();
         $campo_r = array();
         $y = 0;
         for($x = 1; $x <= 5; $x++) {
             $campo_r[$x] = $campo[$x][1];
             if($campo[$x][1] != 0 && $x != parent::ler_variavel('local')) {
                 $lista[$y] = $campo[$x][1];
                 $y++;
             }
         }
         if($y > 0 && isset($lista[$cartaS])) {
             $tool = new biblioteca_de_efeitos;
             $local = $tool->local_original($lista, $campo_r, $cartaS);
             $monstro = $this->duelo->regenerar_instancia_local($local, $this->dono);
             $efeito[0] = 'incrementar_LV';
             $efeito[1] = -1*($monstro->lv-3); // x + -1*(x-3) = x + (-x+3) = x - x + 3 = "3"
             $monstro->sofrer_efeito($efeito, $this);
             parent::manter('local', 0);
             parent::avisar('Efeito da carta '.$this->nome.' ativado', 1);
             return true;
         }
         return false;
 }
}
?>