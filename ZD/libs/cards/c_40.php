<?php

/* Selecione 1 monstro (Six Samura) que você controla. Special Summon 1 monstro
 * (SixSamurai) do seu Deck para o seu lado do campo cujos ATK seja igual ao 
 * do monstro selecionado, mas com um nome diferente. Durante a EndPhase deste turno, 
 * destrua o monstro selecionado.
 */

class c_40 extends Magica {

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  if($this->modo == 1) {return false;}
  if(parent::ler_variavel('carta_solicitada') != 0) {
      if(parent::ler_variavel('carta_solicitada2') != 0) {
        $campo = $this->duelo->ler_campo($this->dono);
        for($x = 1; $x < 5 && $campo[$x][1] != 0; $x++) {}
        if($x >= 5) {
            parent::avisar("Não existem espaços vazios no seu campo");
            $this->tarefa();
            return false;
        }
        else {
         parent::excluir_carta_deck('id', $lista[$cartaS]);
         $this->duelo->invocar($this->dono, $x, 1, parent::ler_variavel('carta_solicitada2'), 'especial');
         parent::avisar("Efeito da carta ".$this->nome." ativado", 1);
         $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
         $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'destruir');
         return true;
        }
      }
      else {
        $deck = $this->duelo->ler_deck($this->dono);
        $carta = new DB_cards();
        $alvo = new DB_cards();
        $carta->ler_id(parent::ler_variavel('carta_solicitada'));
        $aux = 0;
       for($x = 1; $x < $deck[0]; $x++) {
         for($y = 1; $y < $this->lista_SixSamurai[0]; $y++) {
          $alvo->ler_id($this->lista_SixSamurai[$y]);
           if($deck[$x] == $this->lista_SixSamurai[$y] && $carta->atk == $alvo->atk && $carta->nome != $alvo->nome) {
              $lista[$aux] = $this->lista_SixSamurai[$y];
              $aux++;
          }
         }
        }
        if(count($lista) <= 0) {
         parent::avisar('Você não tem monstros Six Samurai com ataque equivalente no seu Deck');
         $this->tarefa('0'); // apagando efeito e se destruindo
         return false;
        }
       $this->duelo->solicitar_carta('Escolha um Samurai', $lista, $this->dono, $this->inst);
       return true;
     }
  }
  else {
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 0); // agendar tarefa de destruir caso não faça nada
  $campo = $this->duelo->ler_campo($this->dono);
  $aux = 0;
   for($x = 1; $x < $campo[0][0]; $x++) {
      for($y = 1; $y < $this->lista_SixSamurai[0]; $y++) {
          if($campo[$x][1] == $this->lista_SixSamurai[$y]) {
              $lista[$aux] = $this->lista_SixSamurai[$y];
              $aux++;
          }
      }
   }
  if(count($lista) <= 0) {
    parent::avisar('Você não controla monstros Six Samurai');
    $this->tarefa('0'); // apagando efeito e se destruindo   
  }
    $this->duelo->solicitar_carta('Escolha um Samurai', $lista, $this->dono, $this->inst);
    $this->mudar_modo(1);
    return true;
  }
 }
 
  function carta_solicitada($cartaS) {
  if(parent::ler_variavel('carta_solicitada') == 0) {
    $campo = $this->duelo->ler_campo($this->dono);
    $aux = 0;
   for($x = 1; $x < $campo[0][0]; $x++) {
      for($y = 1; $y < $this->lista_SixSamurai[0]; $y++) {
          if($campo[$x][1] == $this->lista_SixSamurai[$y]) {
              $lista[$aux] = $this->lista_SixSamurai[$y];
              $aux++;
          }
      }
   }
   if(count($lista) <= 0 || !$lista[$cartaS]) {
    parent::avisar('Não foi possivel ativar o efeito da carta '.$this->nome);
    $this->tarefa('0'); // apagando efeito e se destruindo
    return false;
   }
       parent::manter('carta_solicitada', $lista[$cartaS]);
       $this->ativar_efeito();
       return true;
  }
  else {
        $deck = $this->duelo->ler_deck($this->dono);
        $carta = new DB_cards();
        $alvo = new DB_cards();
        $carta->ler_id(parent::ler_variavel('carta_solicitada'));
        $aux = 0;
       for($x = 1; $x < $deck[0]; $x++) {
         for($y = 1; $y < $this->lista_SixSamurai[0]; $y++) {
          $alvo->ler_id($this->lista_SixSamurai[$y]);
           if($deck[$x] == $this->lista_SixSamurai[$y] && $carta->atk == $alvo->atk && $carta->nome != $alvo->nome) {
              $lista[$aux] = $this->lista_SixSamurai[$y];
              $aux++;
          }
         }
        }
        if(count($lista) <= 0 || !$lista[$cartaS]) {
         parent::avisar('Você não tem monstros Six Samurai com ataque equivalente no seu Deck');
         $this->tarefa('0'); // apagando efeito e se destruindo
         return false;
        }
       parent::manter('carta_solicitada2', $lista[$cartaS]);
       $this->ativar_efeito();
       return true;
  }
 }
 
  function tarefa($txt) { //essa carta deve se destruir caso seja ativada e round passe
      if($txt === 'destruir') {
          $campo = $this->duelo->ler_campo($this->dono);
          $id = parent::ler_variavel('carta_solicitada');
          for($x = 1; $x < 5 && $campo[$x][1] != $id; $x++) {}
          if($x >= 5) {return false;}
          $alvo = &$this->duelo->regenerar_instancia_local($x, $this->dono);
          $efeito[0] = 'destruir';
          $efeito[1] = 'efeito';
          $efeito[2] = 'magica';
          $efeito[3] = 'alvo_designado';
          $efeito[4] = 'self';
          $alvo->sofrer_efeito($efeito, $this);
          unset($alvo);
          $this->tarefa('0');
          return true;
      }
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     parent::destruir();
     return false;
 }
}
?>