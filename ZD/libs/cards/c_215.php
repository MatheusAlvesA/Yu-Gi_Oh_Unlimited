<?php
class c_215 extends Magica {
 /*carta terminada em 04/05/2017. criada em 1 dia
  *Remova do duelo, do seu lado do campo ou do seu Cemitério,
  * Monstros Materiais de Fusão que estão listados em um Fusion Monster Dragon-tipo,
  * e Special Summon aquele Fusion Monster do seu Extra Deck.]
  * (Esta Special Summon é tratado como um Fusion Summon).
  */

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar() || parent::ler_variavel('ativado_em') != 0) {return false;}

   $lista = $this->lista(); // pegando a lista de fusões disponíveis
 $hand = $this->duelo->ler_cmt($this->dono); // formar lista de monstros na mão
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
     $this->avisar('Não existem monstros capases de formar uma fusão no seu cemitério ou deck');
     $this->destruir();
     return false;
 }
 
 $fusoes = array();
 $loop = 0;
 for($x = 1; $x < $lista[0];$x++) {
     if($this->disponivel($lista[$x], $lista_hand, $lista_campo)) {
         $fusoes[$loop] = $lista[$x];
         $loop++;
     }
 }
 if(count($fusoes) == 0) { // não tem componentes pra fusão
     $this->avisar('Não existem monstros capases de formar uma fusão no seu cemitério ou deck');
     $this->destruir();
     return false;
 }
 if(count($fusoes) == 1) { // se só tem um então não precisa perguntar
     $this->fusionar($fusoes[0]);
     return true;
 }
// essas tarefas garantem que a carta não vai ficar no campo caso não seja ativada corretamente
 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'destruir');
 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'battle_phase', 'destruir');
 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm1_phase', 'destruir');
 $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm2_phase', 'destruir');

 for($x = 0;$x < count($fusoes);$x++) {
     $convertido[$x] = $fusoes[$x]->qual;
 }

 $this->duelo->solicitar_carta('ESCOLHA UM DRAGÃO', $convertido, $this->dono, $this->inst);
  
  return true;
 }
 
 
function carta_solicitada($cartaS) {
 $lista = $this->lista(); // pegando a lista de fusões disponíveis
 $hand = $this->duelo->ler_cmt($this->dono); // formar lista de monstros na mão
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
     $this->avisar('Não existem monstros capases de formar uma fusão no seu cemitério ou deck');
     $this->destruir();
     return false;
 }
 
 $fusoes = array();
 $loop = 0;
 for($x = 1; $x < $lista[0];$x++) {
     if($this->disponivel($lista[$x], $lista_hand, $lista_campo)) {
         $fusoes[$loop] = $lista[$x];
         $loop++;
     }
 }
 for($x = 0;$x < count($fusoes);$x++) {
     $convertido[$x] = $fusoes[$x]->qual;
 }
 // o jogador selecionou um carta que não está nessa lista
 if(!$convertido[$cartaS]) {return false;} // o efeito é cancelado
 
  $this->fusionar($fusoes[$cartaS]);
  return true;
 }

 function fusionar($monstro) { // essa função é executada deposi dos sacrificios serem feitos
    $gatilho[0] = 'magica';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'invocar_carta';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {$this->tarefa('x');return false;}
    }
     $hand = $this->duelo->ler_cmt($this->dono);
     $campo = $this->duelo->ler_campo($this->dono);
     for($x = 1; $campo[$x][1] != 0 && $x <= 5; $x++); // procurando slot para colocar o monstro
    if($x > 5) {
     parent::avisar('Não existe espaço para invocar o monstro');
     $this->tarefa('x');
     return false;
    }
    
    // todas as checagens foram feitas agora é hora de ativar
     parent::avisar('Polimerização realizada!', 1);
     //apagando os componentes da fusão
     for($x = 1;$x < $monstro->componentes[0];$x++) {
         $y = 1;
         for(;$y < $hand[0] && $y != -1;$y++) {
             if($hand[$y] == $monstro->componentes[$x]) {
                 $this->duelo->apagar_cmt($monstro->componentes[$x], $this->dono); // apagando a carta do cemitério
                 $hand = $this->duelo->ler_cmt($this->dono); // um foi apagado
                 $y = -2; // deve parar de procurar
             }
         }
         if($y != -1) {
            for($y = 1; $y <= 5; $y++) {// percorrer o campo de mosntros
              if($campo[$y][1] == $monstro->componentes[$x]) { // não precisa checar se é mosntro pois só existem mostros no campo 1 a 5
                $alvo = $this->duelo->regenerar_instancia_local($y, $this->dono);
                $alvo->remover_do_jogo(); // destruindo a carta
                $campo = $this->duelo->ler_campo($this->dono);
              }
            }
         }
     }
     for($local = 1; $campo[$local][1] != 0; $local++); // procurando lugar
     $this->duelo->invocar($this->dono, $local, 1, $monstro->qual, 'especial'); // invocando de forma ritual
     parent::destruir();
     return true;
 }
 
 function tarefa($txt) {
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm1_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm2_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'battle_phase', $this->inst);
     parent::destruir();
     return true;
 }
 
 function disponivel($monstro, $hand, $campo) { // essa função retorna se é possivel realizar a fusão passada por parametro
     for($x = 1;$x < $monstro->componentes[0];$x++) {
         $encontrou = false;
         // procurando na mão
         for($y = 0; $y < count($hand) && $y != (-1);$y++) {
             if($hand[$y] == $monstro->componentes[$x]) { // se encontrou o componente
                 $hand[$y] = 0; // usado
                 $y = -2;// saiu e encontrou
             }
             if($y == -2) $encontrou = true;
         }
         // se não achou procura no campo
         if(!$encontrou) {
            for($y = 0; $y < count($campo) && $y != (-1);$y++) {
                if($campo[$y] == $monstro->componentes[$x]) { // se encontrou o componente
                    $campo[$y] = 0; // usado
                    $y = -2;// saiu e encontrou
                }
                if($y == -2) $encontrou = true;
            }
         }
         // se entrar nessa condição então um dos compotentes não está na mão ou campo
         if(!$encontrou) return false;
     }
     return true;
 }
         
 function lista() {
     $lista = array();
     $lista[0] = 8;
     // dragão caveira negra
     $lista[1] = new combinacao();
     $lista[1]->qual = 47;
     $lista[1]->componentes = array();
     $lista[1]->componentes[0] = 3;
     $lista[1]->componentes[1] = 694;
     $lista[1]->componentes[2] = 594;
      // Blue-Eyes Ultimate Dragon
     $lista[2] = new combinacao();
     $lista[2]->qual = 94;
     $lista[2]->componentes = array();
     $lista[2]->componentes[0] = 4;
     $lista[2]->componentes[1] = 95;
     $lista[2]->componentes[2] = 95;
     $lista[2]->componentes[3] = 95;
     // Darkfire Dragon
     $lista[3] = new combinacao();
     $lista[3]->qual = 182;
     $lista[3]->componentes = array();
     $lista[3]->componentes[0] = 3;
     $lista[3]->componentes[1] = 257;
     $lista[3]->componentes[2] = 557;
     // Gaia the Dragon Champion
     $lista[4] = new combinacao();
     $lista[4]->qual = 288;
     $lista[4]->componentes = array();
     $lista[4]->componentes[0] = 3;
     $lista[4]->componentes[1] = 289;
     $lista[4]->componentes[2] = 151;
     // Kaiser Dragon
     $lista[5] = new combinacao();
     $lista[5]->qual = 402;
     $lista[5]->componentes = array();
     $lista[5]->componentes[0] = 3;
     $lista[5]->componentes[1] = 805;
     $lista[5]->componentes[2] = 244;
     // Meteor B. Dragon
     $lista[6] = new combinacao();
     $lista[6]->qual = 484;
     $lista[6]->componentes = array();
     $lista[6]->componentes[0] = 3;
     $lista[6]->componentes[1] = 594;
     $lista[6]->componentes[2] = 485;
     // Thousand Dragon
     $lista[7] = new combinacao();
     $lista[7]->qual = 738;
     $lista[7]->componentes = array();
     $lista[7]->componentes[0] = 3;
     $lista[7]->componentes[1] = 744;
     $lista[7]->componentes[2] = 48;
     
     return $lista;
     /*
     // Cyber End Dragon
     $lista[5] = new combinacao();
     $lista[5]->qual = 154;
     $lista[5]->componentes = array();
     $lista[5]->componentes[0] = 4;
     $lista[5]->componentes[1] = 153;
     $lista[5]->componentes[2] = 153;
     $lista[5]->componentes[3] = 153;
     // Cyber Twin Dragon
     $lista[6] = new combinacao();
     $lista[6]->qual = 157;
     $lista[6]->componentes = array();
     $lista[6]->componentes[0] = 3;
     $lista[6]->componentes[1] = 153;
     $lista[6]->componentes[2] = 153;
    // Five-Headed Dragon
     $lista[11] = new combinacao();
     $lista[11]->qual = 261;
     $lista[11]->componentes = array();
     $lista[11]->componentes[0] = 5dragões diferentes */
 }
}
?>