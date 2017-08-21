<?php
class c_196 extends Armadilha {
/* Terminada em 13/04/2017. Terminada em 1 dia
 * Destrua 1 monstro virado para cima que você controle para infligir 1000 de dano em ambos os jogadores.
 */
 function ativar_efeito() {  // unica função dessa armadilha
  if(!parent::checar_ativar()) {return false;}
        $gatilho[0] = 'armadilha';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'dano_direto';
        $gatilho[3] = 'ambos';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }

            $campo = $this->duelo->ler_campo($this->dono);
            $lista = array();
            $y = 0;
            for($x = 1; $x <= 5; $x++) {
             if((int)$campo[$x][0] != 0 && (int)$campo[$x][0] != MODOS::DEFESA_BAIXO) {
               $lista[$y] = (int)$campo[$x][1];
               $y++;
             }
            }
            
  if($y < 1) {
      parent::avisar('Você não tem monstros virados para cima');
      return false;
  }
  
  parent::avisar('Efeito da armadilha '.$this->nome.' ativado!',1);
  parent::mudar_modo(MODOS::ATAQUE);
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
  
  $this->duelo->solicitar_carta('Escolha um tributo', $lista, $this->dono, $this->inst);
  return true;
 }
 
 function carta_solicitada($cartaS) {
            $campo = $this->duelo->ler_campo($this->dono);
            $campo_r = array();
            $lista = array();
            $y = 0;
            for($x = 1; $x <= 5; $x++) {
             $campo_r[$x-1] = (int)$campo[$x][1];
             if((int)$campo[$x][0] != 0 && (int)$campo[$x][0] != MODOS::DEFESA_BAIXO) {
               $lista[$y] = (int)$campo[$x][1];
               $y++;
             }
            }
            
  if($y < 1 || !isset($lista[$cartaS])) {
      parent::avisar('Você não tem monstros virados para cima');
      return false;
  }
  
  $tool = new biblioteca_de_efeitos;
  $local = $tool->local_original($lista, $campo_r, $cartaS);

  $efeito[0] = 'destruir';
  $efeito[1] = 'efeito_armadilha';
  if(!$this->duelo->regenerar_instancia_local($local+1, $this->dono)->sofrer_efeito($efeito, $this)) return false;
  
  $this->duelo->alterar_lp(-1000, $this->duelo->oponente($this->dono));
  $this->duelo->alterar_lp(-1000, $this->dono);
  parent::destruir();
  return true;
 }
         
 function tarefa($txt) {
     parent::destruir();
 }
 
}
?>