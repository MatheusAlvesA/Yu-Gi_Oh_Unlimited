<?php

/* terminado em 30/03/2017; em 1 dia
 * Ative somente quando um monstro do seu oponente declara um ataque.
 * Remova do jogo todos os monstros na posição de Defesa que o seu oponente controla.
 */

class c_178 extends Armadilha {

function invocar($local, $id, $modo, $dono, $tipo, $gatilho = 'ataque_monstro') {
    return parent::invocar($local, $id, MODOS::ATAQUE_BAIXO, $dono, $tipo, 'ataque_monstro');
}

 function acionar($gatilho) {
     parent::avisar('Efgeito da armadilha '.$this->nome.' ativado!',1);
        $gatilho[0] = 'armadilha';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'remover_do_jogo';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       
            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
              $efeito[0] = 'remover_do_jogo';
              $efeito[1] = 'efeito_armadilha';
              $id_oponente = $this->duelo->oponente($this->dono);
            for($x = 1; $x <= 5; $x++) {
             if((int)$campo[$x][0] === MODOS::DEFESA) {
               $alvo = &$this->duelo->regenerar_instancia_local($x, $id_oponente);
               $alvo->sofrer_efeito($efeito, $this);
             }
            }
            
  parent::destruir();
  $r['bloqueado'] = false;
  return $r;
 }
    
 function ativar_efeito() {  // unica função dessa mágica
     parent::avisar('Esse efeito só pode ser ativado quando um monstro atacar');
   return true;
 }

}
?>