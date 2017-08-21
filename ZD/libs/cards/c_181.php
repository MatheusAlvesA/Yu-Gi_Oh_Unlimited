<?php
class c_181 extends Magica {
/* carta terminada dia 30/03/2017. terminada em 1 dia
 * Vire todos os monstros virados para baixo que o seu oponente controle para cima.*/

function ativar_efeito() {  // Efeito desse monstro
    if(!parent::checar_ativar()) {return false;}
    parent::avisar('Efeito da carta '.$this->nome.' ativado', 1);
    parent::destruir();
    $this->revelar();
   return true;
  }
 
     private function revelar() {
        $gatilho[0] = 'magica';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'mudar_modo';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       
            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
              $efeito[0] = 'mudar_modo';
              $efeito[1] = MODOS::DEFESA;
              $id_oponente = $this->duelo->oponente($this->dono);
            for($x = 1; $x <= 5; $x++) {
             if((int)$campo[$x][0] === MODOS::DEFESA_BAIXO) {
               $alvo = &$this->duelo->regenerar_instancia_local($x, $id_oponente);
               $alvo->sofrer_efeito($efeito, $this);
             }
            }
            
            return true;
  }
  
}
?>