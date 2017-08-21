<?php
class c_194 extends Armadilha {
/* Terminada em 13/04/2017. Terminada em 1 dia
 * Todos os monstros que você controla são alterados para virados para cima em Modo de Defesa.
 */
 function ativar_efeito() {  // unica função dessa armadilha
  if(!parent::checar_ativar()) {return false;}
       parent::avisar('Efeito da armadilha '.$this->nome.' ativado!',1);
        $gatilho[0] = 'armadilha';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'mudar_modo';
        $gatilho[3] = 'self';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
            parent::mudar_modo(MODOS::ATAQUE);
            $campo = $this->duelo->ler_campo($this->dono);
              $efeito[0] = 'mudar_modo';
              $efeito[1] = MODOS::DEFESA;
              $efeito[2] = 'efeito_armadilha';
              $id = $this->dono;
            for($x = 1; $x <= 5; $x++) {
             if((int)$campo[$x][0] === MODOS::DEFESA_BAIXO) {
               $alvo = &$this->duelo->regenerar_instancia_local($x, $id);
               $alvo->sofrer_efeito($efeito, $this);
             }
            }
            
  parent::destruir();
  return true;
 }
}
?>