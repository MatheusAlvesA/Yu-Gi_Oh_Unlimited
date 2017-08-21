<?php
class c_211 extends Armadilha {
/* Terminada em 23/04/2017. Terminada em 1 dia
 * Enquanto esta carta permanecer virada para cima no campo,
 * todos os monstros tipo Dragon virados para cima são alteradas para a posição de defesa
 * e não pode mudar a sua Posição de Batalha.
 */
 function ativar_efeito() {  // unica função dessa armadilha
  if(!parent::checar_ativar()) {return false;}
  if($this->modo == MODOS::ATAQUE) return false;
  
        $gatilho[0] = 'armadilha';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'mudar_modo';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
              
  parent::avisar('Efeito da armadilha '.$this->nome.' ativado!',1);
  $this->duelo->set_engine($this->inst, $this->dono);
  parent::mudar_modo(MODOS::ATAQUE);
  return true;
 }
 
 function engine() {
       $efeito[0] = 'mudar_modo';
       $efeito[1] = MODOS::DEFESA;
       $carta = new DB_cards;
       $campo = $this->duelo->ler_campo($this->dono);
       $campo_oponente = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
       
        for($x = 1; $x <= 5; $x++) {
            //inicio da leitura dos campos em busca de dragões em modo de ataque
            if((int)$campo[$x][0] != 0) {
                $carta->ler_id($campo[$x][1]);
                if($carta->specie === 'dragon' && (int)$campo[$x][0] == MODOS::ATAQUE) {
                    $monstro = $this->duelo->regenerar_instancia_local($x, $this->dono);
                    if($monstro->sofrer_efeito($efeito)) $monstro->manter('modo_alterado_em', parent::ler_turno());
                }
            }
            
            if((int)$campo_oponente[$x][0] != 0) {
                $carta->ler_id($campo_oponente[$x][1]);
                if($carta->specie === 'dragon' && (int)$campo_oponente[$x][0] == MODOS::ATAQUE) {
                    $monstro = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
                    if($monstro->sofrer_efeito($efeito)) $monstro->manter('modo_alterado_em', parent::ler_turno());
                }
            }
            // fim da leitura dos campos
        }
    return true;
 }
 
}
?>