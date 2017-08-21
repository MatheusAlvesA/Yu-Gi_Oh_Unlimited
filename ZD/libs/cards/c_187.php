<?php
class c_187 extends Magica {
/* carta terminada dia 30/03/2017. terminada em 1 dia
 * Selecione uma Carta Spell/Trap no campo do adversário.
 * Se a carta selecionada for um Spell ela é destruida. Se for uma Trap, devolva á sua posição original.*/

function ativar_efeito() {  // Efeito desse monstro
    if(!parent::checar_ativar()) {return false;}
    
    $gatilho[0] = 'magica';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'destruir';
    $gatilho[3] = 'magica';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
  if(parent::ler_variavel('carta_solicitada') == 0) {
   $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   $y = 0;
   for($x = 6; $x < $campo[0][0]; $x++) {
       if($campo[$x][0] == 1) {
           $lista[$y] = $campo[$x][1];
           $y++;
       }
       elseif($campo[$x][0] != 0) {
           $lista[$y] = 'ub';
           $y++;
       }
    }
    if(!isset($lista)) {
     parent::avisar('Não existem cartas magicas ou armadilhas no campo do oponente');
     parent::mudar_modo(MODOS::DEFESA_BAIXO);
     return false;
    }
    parent::mudar_modo(MODOS::ATAQUE);
    $this->duelo->solicitar_carta('Escolha uma carta', $lista, $this->dono, $this->inst);
    $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
    return true;
  }
  else {
      $carta = new DB_cards();
      $carta->ler_id(parent::ler_variavel('carta_solicitada_id'));
      if($carta->categoria != 'spell') {
       parent::avisar('Efeito do Monstro '.$this->nome.' ativado, a carta '.$carta->nome.' foi selecionada, mas não é uma mágica', 1);
       parent::destruir();
       return false;
      }
     $alvo = $this->duelo->regenerar_instancia_local(parent::ler_variavel('carta_solicitada'), $this->duelo->oponente($this->dono));
     $efeito[0] = 'destruir';
     $efeito[1] = 'efeito';
     $efeito[2] = 'magica';
     if($alvo->sofrer_efeito($efeito, $this)) {
      parent::destruir();
      return true;
     }
     parent::destruir();
     return false;
   }
   
   return true;
  }
 
 function carta_solicitada($cartaS) {
   $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   $y = 0;
   for($x = 6; $x < $campo[0][0]; $x++) {
       if($campo[$x][0] != 0) {
           $lista[$y] = $campo[$x][1];
           $y++;
       }
    }
    if(!isset($lista) || !$lista[$cartaS]) {
     return false;
    }
    
    $tool = new biblioteca_de_efeitos;
    for($x = 6; $x < $campo[0][0]; $x++) $campo_refinado[$x-6] = $campo[$x][1];
    $local = $tool->local_original($lista, $campo_refinado, $cartaS);
    
  parent::manter('carta_solicitada', $local+6);
  parent::manter('carta_solicitada_id', $lista[$cartaS]);
  $this->ativar_efeito();
}

   function tarefa($txt) { //essa carta deve se destruir caso seja ativada quando destruida em batalha
     $this->duelo->apagar_tarefa($this->dono, 'start_phase', $this->inst);
     parent::destruir();
     return true;
 }
  
}
?>