<?php

/* terminado em 10/07/2016 em 1 dia
 * Ative durante o seu turno. Selecione 1 monstro (Blackwing)
 * de 2000 ou menos de ATK em seu Cemitério, e o Special Summon. 
 * Você não pode Normal Summon ou Set no turno que ativar essa carta.
 */

class c_77 extends Armadilha {

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {
      return false;
  }
 
  $vetor_cmt = $this->duelo->ler_cmt($this->dono);
  $i = new DB_cards;
  $y = 0;
  for($x = 1; $x < $vetor_cmt[0];$x++) {
      $i->ler_id($vetor_cmt[$x]);
      if(($i->id >= 79 && $i->id <= 85) && $i->atk <= 2000) {
          $lista[$y] = $vetor_cmt[$x];
          $y++;
      }
  }
  if(!is_array($lista)) {
      parent::avisar('Você não possui monstros do tipo Blackwing com ataque compativel no seu cemitério');
      return false;
  }
  
  $this->duelo->solicitar_carta('RESSUSCITAR MONSTRO:', $lista, $this->dono, $this->inst);
  
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'battle_phase', 'x');
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm2_phase', 'x');
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
  $this->mudar_modo(1);
   return true;
 }
 
 function carta_solicitada($cartaS) {
  $vetor_cmt = $this->duelo->ler_cmt($this->dono);
  $i = new DB_cards;
  $y = 0;
  for($x = 1; $x < $vetor_cmt[0];$x++) {
      $i->ler_id($vetor_cmt[$x]);
      if(($i->id >= 79 && $i->id <= 85) && $i->atk <= 2000) {
          $lista[$y] = $vetor_cmt[$x];
          $y++;
      }
  }
    if(!$lista[$cartaS]) {
      $this->destruir();
      return false;
    }
    
    $campo = $this->duelo->ler_campo($this->dono);
    for($x = 1;$x <= 5 && $campo[$x][1] != 0;$x++);
    if($x > 5) {
        parent::avisar('Você não tem espaço no capo para executar essa invocação');
        $this->destruir();
        return false;
    }
    $i->ler_id($lista[$cartaS]);
    parent::avisar('Efeito da carta '.$this->nome.' foi ativado '.$i->nome.' foi invocado', 1);
    $this->duelo->apagar_cmt($lista[$cartaS], $this->dono); // tirando do cemitério
    $this->duelo->invocar($this->dono, $x, MODOS::ATAQUE, $lista[$cartaS], 'especial'); // invocando em campo
    $temp = $this->duelo->regenerar_instancia_local($x, $this->dono);
    file_put_contents($this->pasta.'/m_invocado.txt', $temp->inst);
    
     $this->destruir();
     return true;
 }
         
 function tarefa($txt) {
     $this->destruir();
 }
 
 function destruir($motivo = 0) {
     $this->duelo->apagar_tarefa($this->dono, 'battle_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm2_phase', $this->inst);
     $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
     parent::destruir($motivo);
     return true;
 }
 
}
?>