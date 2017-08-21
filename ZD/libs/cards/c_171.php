<?php
class c_171 extends Magica {
/* carta terminada dia 23/03/2017. terminada em 1 dia
 * Destrua todos os monstros no campo.*/

function ativar_efeito() {  // Efeito desse monstro
    if(!parent::checar_ativar()) {return false;}
    parent::avisar($this->nome.' ativado !!!', 1);
    $this->auto_obliterar();
    $this->obliterar();
    parent::destruir();
   return true;
  }
 
     private function obliterar() {
        $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_monstro';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_especial';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       
            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_monstro';
              $id_oponente = $this->duelo->oponente($this->dono);
            for($x = 1; $x <= 5; $x++) {
             if($campo[$x][1] != 0) {
               $alvo = &$this->duelo->regenerar_instancia_local($x, $id_oponente);
               $alvo->sofrer_efeito($efeito, $this);
             }
            }
            return true;
  }

   private function auto_obliterar() {
        $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_monstro';
        $gatilho[3] = 'self';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_especial';
        $gatilho[3] = 'self';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       
            $campo = $this->duelo->ler_campo($this->dono);
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_monstro';
            for($x = 1; $x <= 5; $x++) {
             if($campo[$x][1] != 0) {
               $alvo = &$this->duelo->regenerar_instancia_local($x, $this->dono);
               $alvo->sofrer_efeito($efeito, $this);
             }
            }
            return true;
  }
  
}
?>