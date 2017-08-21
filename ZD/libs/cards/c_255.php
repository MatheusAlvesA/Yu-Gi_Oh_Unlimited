<?php
class c_255 extends Armadilha {
/* Terminada em 26/05/2017. Terminada em 1 dia
 * Ativar apenas quando você não possuir cartas na sua mão.
 * Role um dado de seis lados 3 vezes,
 * e inflija dano no seu oponente igual ao resultado total dos dados x 100.
 */
 function ativar_efeito() {  // unica função dessa armadilha
  if(!parent::checar_ativar()) {return false;}
  if(parent::ler_variavel('invocado_em') == parent::ler_turno()) {
      parent::avisar('Você não pode ativar uma armadilha assim que a invocou');
      return false;
  }
  
        $gatilho[0] = 'armadilha';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'dano_direto';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
  
       $hand = $this->duelo->ler_mao($this->dono);
       if($hand[0] > 1) {
           parent::avisar('Você só pode ativar essa carta se não tiver cartas na mão');
           return false;
       }

  $dado_a = $this->duelo->dados();
  $dado_b = $this->duelo->dados();
  $dado_c = $this->duelo->dados();
  $soma = $dado_a+$dado_b+$dado_c;
  
  $tool = new biblioteca_de_efeitos;
  $tool->dano_direto($this->duelo->oponente($this->dono), $soma*100);
  
  parent::avisar('Efeito da armadilha '.$this->nome.' ativado. A soma dos dados foi '.$soma.'. '.($soma*100).' foi causado de dano',1);
  parent::destruir();
  return true;
 }

}
?>