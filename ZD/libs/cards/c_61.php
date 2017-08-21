<?php
class c_61 extends Magica {
/* Esta carta é usada para Ritual Summon de (Fiends Mirror). 
 * Você também deve oferecer monstros cujos somados níveis seja igual a 6 
 * ou mais como um tributo a partir do campo ou da sua mão.*/

function ativar_efeito() {  // unica função dessa mágica
 $hand = $this->duelo->ler_mao($this->dono); // formar lista de monstros na mão
 $carta = new DB_cards();
 $y = 0;
 for($x = 1; $x < $hand[0]; $x++) { // percorrer a mão
  $carta->ler_id($hand[$x]);
  if($carta->categoria == 'monster') { // só registra os monstros
   $lista_hand[$y] = $hand[$x];
   $y++;
  }
 }

 $campo = $this->duelo->ler_campo($this->dono); // formar lista de mostros no campo
 $y = 0;
 for($x = 1; $x <= 5; $x++) {// percorrer o campo de mosntros
  if($campo[$x][1] != 0) { // não precisa checar se é mosntro pois só existem mostros no campo 1 a 5
   $lista_campo[$y] = $campo[$x][1];
   $y++;
  }
 }

 if(!isset($lista_hand) && !isset($lista_campo)) { // nesse caso não tem como ativar o efeito
     $this->avisar('Não existem monstros sufuciente para ativar esse ritual');
     $this->destruir();
     return false;
     
 }
// essas tarefas garantem que a carta não vai ficar no campo caso não seja ativada corretamente
 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'destruir');
 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'battle_phase', 'destruir');
 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm1_phase', 'destruir');
 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm2_phase', 'destruir');

 $y = 0;
 if(isset($lista_hand)) {
   $texto = 'MONSTROS: MÃO';
  for($x = 0; $x < count($lista_hand); $x++) {
   $lista[$y] = $lista_hand[$x];
   $y++;
  }
 }
 if(isset($lista_campo)) {
  if($y > 0) {
   $texto .= '|CAMPO';
   $lista[$y] = 'divisor';
   $y++;
  }
  else {$texto = 'MONSTROS: CAMPO';}
  for($x = 0; $x < count($lista_campo); $x++) {
   $lista[$y] = $lista_campo[$x];
   $y++;
  }
 }

 $this->duelo->solicitar_carta($texto, $lista, $this->dono, $this->inst);
 return true;
}
 
function carta_solicitada($cartaS) {
 $hand = $this->duelo->ler_mao($this->dono); // formar lista de monstros na mão
 $carta = new DB_cards();
 $y = 0;
 for($x = 1; $x < $hand[0]; $x++) { // percorrer a mão
  $carta->ler_id($hand[$x]);
  if($carta->categoria == 'monster') { // só registra os monstros
   $lista_hand[$y] = $hand[$x];
   $y++;
  }
 }

 $campo = $this->duelo->ler_campo($this->dono); // formar lista de mostros no campo
 $y = 0;
 for($x = 1; $x <= 5; $x++) {// percorrer o campo de mosntros
  $carta->ler_id($campo[$x][1]);
  if($campo[$x][1] != 0) { // não precisa checar se é mosntro pois só existem mostros no campo 1 a 5
   $lista_campo[$y] = $campo[$x][1];
   $y++;
  }
 }
 // se por algum motivo as cartas mosntro não existem mais então o efeito é cancelado
 if(!isset($lista_hand) && !isset($lista_campo)) {return false;}
  $y = 0;
 if(isset($lista_hand)) {
  for($x = 0; $x < count($lista_hand); $x++) {
   $lista[$y] = $lista_hand[$x];
   $y++;
  }
 }
 if(isset($lista_campo)) {
  if($y > 0) {
   $lista[$y] = 'divisor';
   $y++;
  }
  for($x = 0; $x < count($lista_campo); $x++) {
   $lista[$y] = $lista_campo[$x];
   $y++;
  }
 }
 // o jogador selecionou um carta que não está nessa lista
 if($lista[$cartaS] == 'divisor' || !$lista[$cartaS]) {return false;} // o efeito é cancelado
 $onde = 1;
 for($x = 0; $x < $cartaS; $x++) {if($lista[$x] == 'divisor') {$onde++;}}
 if(isset($lista_hand) && $onde == 1) { // se a carta for da mão então a lista da mão deve estar setada
    for($x = 1; $hand[$x] != $lista_hand[$cartaS]; $x++); // convertendo a posição para a correta
    $carta = new DB_cards();
    $carta->ler_id($hand[$x]);
    $lv = $this->ler_variavel('lvs_acumulados');
    $lv += $carta->lv;
    $this->manter('lvs_acumulados', $lv); // guardando a quantidade de levels acumulados a té agora
    $this->duelo->apagar_carta_hand($this->dono, $x); // apagando a carta da mão
    $this->duelo->colocar_no_cemiterio($carta->id, $this->dono); // colocando a mesma carta no cemitério
 }
 if(isset($lista_campo)) {
  if($onde == 1 && !isset($lista_hand)) {
    $quantas = 0;
    for($x = 0; $x <= $cartaS; $x++){
     if($lista_campo[$x] == $lista_campo[$cartaS]) {$quantas++;} // preciso remover exatamente o que o jogador pediu na posição exata
    }
    for($x = 1; $x <= 5; $x++) {
     if($campo[$x][1] == $lista_campo[$cartaS]) {
      if($quantas > 1) {$quantas--;}
      else {break;}
     }
    }
    $alvo = $this->duelo->regenerar_instancia_local($x, $this->dono);
    $lv = $this->ler_variavel('lvs_acumulados');
    $lv += $alvo->lv;
    $this->manter('lvs_acumulados', $lv); // guardando a quantidade de levels acumulados a té agora
    $alvo->destruir(); // destruindo a carta
  }
    elseif($onde == 2 && isset($lista_hand)) {
        $cartaS = $cartaS - count($lista_hand) - 1;
        $quantas = 0;
        for($x = 0; $x <= $cartaS; $x++){
         if($lista_campo[$x] == $lista_campo[$cartaS]) {$quantas++;}
        }
        for($x = 1; $x <= 5; $x++) {
         if($campo[$x][1] == $lista_campo[$cartaS]) {
             if($quantas > 1) {$quantas--;}
             else {break;}
         }
        }
    $alvo = $this->duelo->regenerar_instancia_local($x, $this->dono);
    $lv = $this->ler_variavel('lvs_acumulados');
    $lv += $alvo->lv;
    $this->manter('lvs_acumulados', $lv); // guardando a quantidade de levels acumulados a té agora
    $alvo->destruir(); // destruindo a carta
    }
}

 if($this->ler_variavel('lvs_acumulados') < 6) { // ainda não tem nível acumuado suficiente
  $this->ativar_efeito();
  return true;
 }
 else { // caso já tenha acumulado o suficiente
     $this->ritual();
     return true;
 }

  $this->duelo->solicitar_carta($texto, $lista, $this->dono, $this->inst);
  return true;
 }

 function ritual() { // essa função é executada deposi dos sacrificios serem feitos
    $gatilho[0] = 'magica';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'invocar_carta';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {$this->tarefa('x');return false;}
    }
    
    $mao = $this->duelo->ler_mao($this->dono);
    $existe = false;
    for($x = 1; $x < $mao[0]; $x++) {
        if($mao[$x] == 251) { // id da carta Fiends Mirror
            parent::excluir_carta_hand('id', 251);
            $existe = true;
            break;
        }
    }

    if($existe) {
     $campo = $this->duelo->ler_campo($this->dono);
     for($x = 1; $campo[$x][1] != 0 && $x <= 5; $x++); // procurando slot para colocar o monstro
    if($x > 5) {
     parent::avisar('Não existe espaço para invocar o Fiends Mirror');
     $this->tarefa('x');
     return false;
    }
     parent::avisar('Carta ritual '.$this->nome.' ativada', 1);
     $this->duelo->invocar($this->dono, $x, 1, 251, 'ritual'); // invocando de forma ritual
     $this->tarefa('x');
     return true;
    }
    else {
     parent::avisar('Fiends Mirror não foi encontrado na sua mão');
     $this->tarefa('x'); // se destruindo
     return false;
    }
 }
 
 function tarefa($txt) {
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm1_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm2_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'battle_phase', $this->inst);
     parent::destruir();
     return true;
 }
 
}
?>