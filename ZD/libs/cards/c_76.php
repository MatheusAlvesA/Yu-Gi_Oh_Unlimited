<?php

/* terminado em 09/07/2016 em 1 dia
 * Quando um monstro (Blackwing) é Invocado Normal para o seu lado do campo, 
 * você pode adicionar 1 monstro (Blackwing) do seu Deck para a sua mão com ATK 
 * menor que o ATK daquele monstro. (Esse efeito pode ser ativado apenas uma vez por turno).
 */

class c_76 extends Magica {

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar() || !file_exists($this->pasta.'/m_invocado.txt')) { // se não puder invocar por um desses motivos
      parent::avisar('Você não pode ativar essa carta agora');
      return false;
  }
  if(!file_get_contents($this->pasta.'/m_invocado.txt')) { // se não tiver nada gravado lá
      parent::avisar('Você não pode ativar essa carta sem antes invocar um Blackwing');
      return false;
  }
  $monstro = $this->duelo->regenerar_instancia(file_get_contents($this->pasta.'/m_invocado.txt'), $this->dono); //  o ultimo monstro invocado
  if(!($monstro->id >= 79 && $monstro->id <= 85)) { // se o ultimo monstro não foi um blackwing
      parent::avisar('Você somente pode ativar essa carta quando invocar um Blackwing');
      return false;
  }
  
  $vetor_deck = $this->duelo->ler_deck($this->dono);
  $i = new DB_cards;
  $y = 0;
  for($x = 1; $x < $vetor_deck[0];$x++) {
      $i->ler_id($vetor_deck[$x]);
      if(($i->id >= 79 && $i->id <= 85) && $i->atk < $monstro->atk) {
          $lista[$y] = $vetor_deck[$x];
          $y++;
      }
  }
  if(!is_array($lista)) {
      parent::avisar('Você não possui monstros do tipo Blackwing com ataque menor no seu deck');
      return false;
  }
  
  $this->duelo->solicitar_carta('Escolha um monstro', $lista, $this->dono, $this->inst);
  
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'battle_phase', 'x');
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm2_phase', 'x');
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
  $this->mudar_modo(1);
   return true;
 }
 
 function carta_solicitada($cartaS) {
    $vetor_deck = $this->duelo->ler_deck($this->dono);
    $monstro = $this->duelo->regenerar_instancia(file_get_contents($this->pasta.'/m_invocado.txt'), $this->dono); //  o ultimo monstro invocado
    $i = new DB_cards;
    $y = 0;
    for($x = 1; $x < $vetor_deck[0];$x++) {
      $i->ler_id($vetor_deck[$x]);
      if(($i->id >= 79 && $i->id <= 85) && $i->atk < $monstro->atk) {
          $lista[$y] = $vetor_deck[$x];
          $y++;
      }
    }
    if(!$lista[$cartaS]) {
      $this->tarefa('x');
      parent::avisar('Erro ao Ativar, carta selecionada inválida');
      return false;        
    }
    parent::excluir_carta_deck('id', $lista[$cartaS]); // tirando do deck
    $this->duelo->colocar_carta_hand($lista[$cartaS], $this->dono); // e colocando no deck
     parent::avisar('Efeito da carta '.$this->nome.' foi ativado', 1);
     $this->destruir();
 }
         
 function tarefa($txt) {
     $this->destruir();
 }
 
 function destruir($motivo = 0) {
     $this->duelo->apagar_tarefa($this->dono, 'battle_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm2_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     parent::destruir($motivo);
     return true;
 }
 
}
?>