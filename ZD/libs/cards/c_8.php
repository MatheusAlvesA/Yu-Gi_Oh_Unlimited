<?php
class c_8 extends Magica {
/*Ative por selecionar 1 monstro (Blackwing ids:79-85) no seu Cemitério.
 * Receba dano igual ao ATK do monstro selecionado e, 
 * depois disso, retorne aquele monstro para a sua mão.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  if(parent::ler_variavel('carta_solicitada') != 0) {
      $this->colocar_hand(parent::ler_variavel('carta_solicitada'), $this->dono);
      $monstro = new DB_cards();
      $monstro->ler_id(parent::ler_variavel('carta_solicitada'));
      parent::dano_direto($this->duelo->dir_duelo.'/'.$this->dono.'/lps.txt', $monstro->atk);
      parent::avisar("Efeito da carta ".$this->nome." ativado, o monstro ".$monstro->nome." foi colocado na mão do duelista e ".$monstro->atk." pontos foram removidos dos LP's.", 1);
      $this->tarefa("0");
      return true;
  }
  else {
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 0); // agendar tarefa de destruir caso não faça nada
  $cemiterio = $this->duelo->ler_cmt($this->dono);
  $aux = 0;
   for($x = 1; $x < $cemiterio[0]; $x++) {
      for($y = 1; $y < $this->lista_Blackwing[0]; $y++) {
          if($cemiterio[$x] == $this->lista_Blackwing[$y]) {
              $lista[$aux] = $this->lista_Blackwing[$y];
              $aux++;
          }
      }
   }
  if(count($lista) <= 0) {
    parent::avisar('Você não possui monstros Blackwing no cemitério');
    $this->tarefa('0'); // apagando efeito e se destruindo   
    return false;
  }
    $this->duelo->solicitar_carta('Escolha um monstro', $lista, $this->dono, $this->inst);
    $this->mudar_modo(1);
    return true;
  }
 }
 
  function carta_solicitada($cartaS) {
       $cemiterio = $this->duelo->ler_cmt($this->dono);
       $aux = 0;
   for($x = 1; $x < $cemiterio[0]; $x++) {
      for($y = 1; $y < $this->lista_Blackwing[0]; $y++) {
          if($cemiterio[$x] == $this->lista_Blackwing[$y]) {
              $lista[$aux] = $this->lista_Blackwing[$y];
              $aux++;
          }
      }
   }
   if(count($lista) <= 0 || !($lista[$cartaS] >= 79 && $lista[$cartaS] <= 85)) {
    parent::avisar('Não foi possivel ativar o efeito da carta '.$this->nome);
    $this->tarefa('0'); // apagando efeito e se destruindo
    return false;
   }
    
   $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
       parent::manter('carta_solicitada', $lista[$cartaS]);
       $this->ativar_efeito();
 }
 
  function tarefa($txt) { //essa carta deve se destruir caso seja ativada e round passe
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     parent::destruir();
     return false;
 }
}
?>