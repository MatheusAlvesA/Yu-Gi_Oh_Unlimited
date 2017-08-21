<?php
class c_107 extends Magica {
/* carta terminada dia 17/09/2016. terminada em 1 dia
 * Uma vez por turno, durante a sua StandbyPhase:
 * Você pode embaralhar um card de sua mão no seu deck, e então compre 1 carta.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
   parent::avisar('Efeito da carta '.$this->nome.' ativado', 1);
   $this->duelo->agendar_tarefa($this->inst, $this->dono, 'sb_phase', 'x');
   parent::mudar_modo(MODOS::ATAQUE);
   return true;
 }
 
 function tarefa($txt) {
     $hand = $this->duelo->ler_mao($this->dono);
     $y = 0;
     for($x = 1; $x < $hand[0]; $x++) {
        $lista[$y] = $hand[$x];
        $y++;
     }
     if(!$lista) return false;
     $this->duelo->solicitar_carta('ESCOLHA PARA ATIVAR', $lista, $this->dono, $this->inst);
     return true;
 }
         
  function carta_solicitada($cartaS) {
     $hand = $this->duelo->ler_mao($this->dono);
     $y = 0;
     for($x = 1; $x < $hand[0]; $x++) {
        $lista[$y] = $hand[$x];
        $y++;
     }
     if(!$lista || !$lista[$cartaS]) return false;

     $this->trocar($lista[$cartaS]);
     parent::avisar('Carta trocada pelo efeito da '.$this->nome, 1);
     return true;
 }

 function trocar($carta) {
     parent::excluir_carta_hand('id', $carta);
         $grav = new Gravacao();
	 $grav->set_caminho($this->duelo->dir_duelo.$this->dono."/deck.txt");
	 $array = $grav->ler(0);
         $aleatoria = rand(1, $array[0]-1);
         $carta_nova = $array[$aleatoria];
         $array[$aleatoria] = $carta;
	 $grav->set_array($array);
 	 $grav->gravar();
	 unset($grav);
         $this->duelo->colocar_carta_hand($carta_nova, $this->dono);
    return true;
 }
 
}
?>