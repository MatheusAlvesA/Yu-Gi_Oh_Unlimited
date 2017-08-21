<?php
class c_7 extends Magica {
/*Ative por Tributar 1 monstro de Nível 8 ou maior virado para cima. Compre 2 cartas.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 0); // agendar tarefa de destruir caso não fça nada
  //solicitar sacrificio de um monstro ao usuario se tudo der certo puxar as duas cartas
  if(parent::ler_variavel('carta_solicitada')) {
   $monstro = $this->duelo->regenerar_instancia(parent::ler_variavel('carta_solicitada'), $this->dono);
   if($monstro->lv >= 8) {
    $efeito[0] = 'destruir';
    if($monstro->sofrer_efeito($efeito, $this) === 'bloqueado') {parent::destruir(); return false;}
    $this->duelo->puxar_carta($this->dono);
    $this->duelo->puxar_carta($this->dono);
    parent::avisar('Efeito da carta '.$this->nome.' ativado.', 1);
   }
  }
   else {
       $campo = $this->duelo->ler_campo($this->dono);
       $carta = new DB_cards;
       $y = 0;
       for($x = 1; $x <= 5; $x++) {
           $carta->ler_id($campo[$x][1]);
           if($campo[$x][1] != 0 && $carta->categoria == 'monster' && $carta->lv >= 8) {
               $cartas[$y] = $campo[$x][1]; $y++;
           }
       }
       if(!isset($cartas)) {parent::avisar('Não foi possivel ativar o efeito da carta '.$this->nome);}
       else {
           for($x = 0; $x < count($cartas); $x++) {
               $lista[$x] = $cartas[$x];
           }
           $this->duelo->solicitar_carta('Sacrificar monstro', $lista, $this->dono, $this->inst);
           $this->mudar_modo(1);
           return true;
       }
   }
   $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
    parent::destruir();
    return true;
 }
 
 function carta_solicitada($cartaS) {
       $campo = $this->duelo->ler_campo($this->dono);
       $carta = new DB_cards;
       $y = 0;
       for($x = 1; $x <= 5; $x++) {
           $carta->ler_id($campo[$x][1]);
           if($campo[$x][1] != 0 && $carta->categoria == 'monster' && $carta->lv >= 8) {
               $cartas[$y] = $campo[$x][1]; $y++;
           }
       }
       for($x = 1; $x <= 5; $x++) {
           if($campo[$x][1] == $cartas[$cartaS]) {
               $monstro = $this->duelo->regenerar_instancia_local($x, $this->dono);
               break;
           }
       }
       parent::manter('carta_solicitada', $monstro->inst);
       $this->ativar_efeito();
 }
 
 function tarefa($txt) { //essa carta deve se destruir caso seja ativada e round passe
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     parent::destruir();
 }
}
?>
