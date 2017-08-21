<?php
class c_144 extends Magica {
/* carta terminada dia 28/02/2017. terminada em 1 dia
 * Descarte uma carta de sua mão. Invoque(Normal-Summon) um monstro level 6 ou menor sem sacrificar.*/

function ativar_efeito() {  // Efeito desse monstro
    if(!parent::checar_ativar()) {return false;}
    
    $gatilho[0] = 'magica';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'diminuir_sacrificio';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    
    $flag['local'] = 'hand';
    $flag['duelista'] = $this->dono;
    $ferramenta = new biblioteca_de_efeitos;
    $lista = $ferramenta->listar($this->duelo, $flag);
    
 if(!isset($lista)) {
     parent::avisar('Não é possível ativar esse efeito');
     return false;
 }
    
    $this->duelo->solicitar_carta("Escolha uma carta da mão", $lista, $this->dono, $this->inst);
    $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
   return true;
  }
 
function carta_solicitada($cartaS) {
    // formando lista do cemiterio
    $flag['local'] = 'hand';
    $flag['duelista'] = $this->dono;
    $ferramenta = new biblioteca_de_efeitos;
    $lista = $ferramenta->listar($this->duelo, $flag);
    
 if(!isset($lista) || !isset($lista[$cartaS])) {
     parent::avisar('Não foi posível ativar esse efeito');
     return false;
 }

 $this->duelo->apagar_carta_hand($this->dono, $cartaS+1);
  if(!file_exists($this->pasta.'/sacrificios.txt')) {
     $arq = fopen($this->pasta.'/sacrificios.txt', 'w');
     fwrite($arq, '0');
     fclose($arq);
   }
   file_put_contents($this->pasta.'/sacrificios.txt', file_get_contents($this->pasta.'/sacrificios.txt')+1);
   
   parent::avisar('Efeito da carta '.$this->nome.' ativado.');
   parent::destruir();
      return true;
  }
  
  function tarefa($txt){
      parent::destruir();
      return true;
  }
    
}
?>