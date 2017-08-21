<?php
class c_561 extends Magica {
/*carta terminada dia 28/12/2016 terminada em 2 dias
 * Envie Monstros Material de Fusão que estão listados em uma carta de monstro de Fusão,
 *da sua mão ou de seu lado do campo para o cemitério, e Special Summon o Monstro de Fusão.*/

function ativar_efeito() {  // unica função dessa mágica
 $lista = $this->lista(); // pegando a lista de fusões disponíveis
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
     $this->avisar('Não existem monstros capases de formar uma fusão na sua mão ou campo');
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
     $this->avisar('Não existem monstros capases de formar uma fusão na sua mão ou deck');
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

 $this->duelo->solicitar_carta('ESCOLHA UM MONSTRO', $convertido, $this->dono, $this->inst);
 return true;
}
 
function carta_solicitada($cartaS) {
 $lista = $this->lista(); // pegando a lista de fusões disponíveis
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
     $this->avisar('Não existem monstros capases de formar uma fusão na sua mão ou deck');
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
     $hand = $this->duelo->ler_mao($this->dono);
     $campo = $this->duelo->ler_campo($this->dono);
     for($x = 1; $campo[$x][1] != 0 && $x <= 5; $x++); // procurando slot para colocar o monstro
    if($x > 5) {
     parent::avisar('Não existe espaço para invocar o monstro');
     $this->tarefa('x');
     return false;
    }
    
    // todas as checagens foram feitas agora é hora de ativar
     parent::avisar('Polimerização ativada!', 1);
     //apagando os componentes da fusão
     for($x = 1;$x < $monstro->componentes[0];$x++) {
         $y = 1;
         for(;$y < $hand[0] && $y != -1;$y++) {
             if($hand[$y] == $monstro->componentes[$x]) {
                 $this->duelo->apagar_carta_hand($this->dono, $y); // apagando a carta da mão
                 $this->duelo->colocar_no_cemiterio($hand[$y], $this->dono); // colocando a mesma carta no cemitério
                 $hand = $this->duelo->ler_mao($this->dono); // um foi apagado
                 $y = -2; // deve parar de procurar
             }
         }
         if($y != -1) {
            for($y = 1; $y <= 5; $y++) {// percorrer o campo de mosntros
              if($campo[$y][1] == $monstro->componentes[$x]) { // não precisa checar se é mosntro pois só existem mostros no campo 1 a 5
                $alvo = $this->duelo->regenerar_instancia_local($y, $this->dono);
                $alvo->destruir(); // destruindo a carta
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
     $lista[0] = 29;
     // dragão caveira negra
     $lista[1] = new combinacao();
     $lista[1]->qual = 47;
     $lista[1]->componentes = array();
     $lista[1]->componentes[0] = 3;
     $lista[1]->componentes[1] = 694;
     $lista[1]->componentes[2] = 594;
     // aqua dragon
     $lista[2] = new combinacao();
     $lista[2]->qual = 27;
     $lista[2]->componentes = array();
     $lista[2]->componentes[0] = 4;
     $lista[2]->componentes[1] = 244;
     $lista[2]->componentes[2] = 17;
     $lista[2]->componentes[3] = 831;
      // Blue-Eyes Ultimate Dragon
     $lista[3] = new combinacao();
     $lista[3]->qual = 94;
     $lista[3]->componentes = array();
     $lista[3]->componentes[0] = 4;
     $lista[3]->componentes[1] = 95;
     $lista[3]->componentes[2] = 95;
     $lista[3]->componentes[3] = 95;
       // Charubin the Fire Knight
     $lista[4] = new combinacao();
     $lista[4]->qual = 118;
     $lista[4]->componentes = array();
     $lista[4]->componentes[0] = 3;
     $lista[4]->componentes[1] = 499;
     $lista[4]->componentes[2] = 367;
     // Darkfire Dragon
     $lista[5] = new combinacao();
     $lista[5]->qual = 182;
     $lista[5]->componentes = array();
     $lista[5]->componentes[0] = 3;
     $lista[5]->componentes[1] = 257;
     $lista[5]->componentes[2] = 557;
     // Deepsea Shark
     $lista[6] = new combinacao();
     $lista[6]->qual = 189;
     $lista[6]->componentes = array();
     $lista[6]->componentes[0] = 3;
     $lista[6]->componentes[1] = 99;
     $lista[6]->componentes[2] = 747;
      // Dragoness the Wicked Knight
     $lista[7] = new combinacao();
     $lista[7]->qual = 214;
     $lista[7]->componentes = array();
     $lista[7]->componentes[0] = 3;
     $lista[7]->componentes[1] = 31;
     $lista[7]->componentes[2] = 540;
     // Flame Ghost
     $lista[8] = new combinacao();
     $lista[8]->qual = 265;
     $lista[8]->componentes = array();
     $lista[8]->componentes[0] = 3;
     $lista[8]->componentes[1] = 659;
     $lista[8]->componentes[2] = 203;
     // Flame Swordsman
     $lista[9] = new combinacao();
     $lista[9]->qual = 267;
     $lista[9]->componentes = array();
     $lista[9]->componentes[0] = 3;
     $lista[9]->componentes[1] = 266;
     $lista[9]->componentes[2] = 467;
     // Flower Wolf
     $lista[10] = new combinacao();
     $lista[10]->qual = 269;
     $lista[10]->componentes = array();
     $lista[10]->componentes[0] = 3;
     $lista[10]->componentes[1] = 653;
     $lista[10]->componentes[2] = 185;
      // Fusionist
     $lista[11] = new combinacao();
     $lista[11]->qual = 284;
     $lista[11]->componentes = array();
     $lista[11]->componentes[0] = 3;
     $lista[11]->componentes[1] = 556;
     $lista[11]->componentes[2] = 518;
     // Gaia the Dragon Champion
     $lista[12] = new combinacao();
     $lista[12]->qual = 288;
     $lista[12]->componentes = array();
     $lista[12]->componentes[0] = 3;
     $lista[12]->componentes[1] = 289;
     $lista[12]->componentes[2] = 151;
     // Giltia the D. Knight
     $lista[13] = new combinacao();
     $lista[13]->qual = 324;
     $lista[13]->componentes = array();
     $lista[13]->componentes[0] = 3;
     $lista[13]->componentes[1] = 349;
     $lista[13]->componentes[2] = 567;
     // Humanoid Worm Drake
     $lista[14] = new combinacao();
     $lista[14]->qual = 374;
     $lista[14]->componentes = array();
     $lista[14]->componentes[0] = 3;
     $lista[14]->componentes[1] = 813;
     $lista[14]->componentes[2] = 373;
     // Kaiser Dragon
     $lista[15] = new combinacao();
     $lista[15]->qual = 402;
     $lista[15]->componentes = array();
     $lista[15]->componentes[0] = 3;
     $lista[15]->componentes[1] = 805;
     $lista[15]->componentes[2] = 244;
     // Kaminari Attack
     $lista[16] = new combinacao();
     $lista[16]->qual = 403;
     $lista[16]->componentes = array();
     $lista[16]->componentes[0] = 3;
     $lista[16]->componentes[1] = 536;
     $lista[16]->componentes[2] = 476;
      // Karbonala Warrior
     $lista[17] = new combinacao();
     $lista[17]->qual = 404;
     $lista[17]->componentes = array();
     $lista[17]->componentes[0] = 3;
     $lista[17]->componentes[1] = 444;
     $lista[17]->componentes[2] = 445;
     // Master of Oz
     $lista[18] = new combinacao();
     $lista[18]->qual = 471;
     $lista[18]->componentes = array();
     $lista[18]->componentes[0] = 3;
     $lista[18]->componentes[1] = 66;
     $lista[18]->componentes[2] = 192;
     // Metal Dragon
     $lista[19] = new combinacao();
     $lista[19]->qual = 481;
     $lista[19]->componentes = array();
     $lista[19]->componentes[0] = 3;
     $lista[19]->componentes[1] = 687;
     $lista[19]->componentes[2] = 432;
     // Meteor B. Dragon
     $lista[20] = new combinacao();
     $lista[20]->qual = 484;
     $lista[20]->componentes = array();
     $lista[20]->componentes[0] = 3;
     $lista[20]->componentes[1] = 594;
     $lista[20]->componentes[2] = 485;
     // Pragtical
     $lista[21] = new combinacao();
     $lista[21]->qual = 565;
     $lista[21]->componentes = array();
     $lista[21]->componentes[0] = 3;
     $lista[21]->componentes[1] = 749;
     $lista[21]->componentes[2] = 268;
     // Punished Eagle
     $lista[22] = new combinacao();
     $lista[22]->qual = 569;
     $lista[22]->componentes = array();
     $lista[22]->componentes[0] = 3;
     $lista[22]->componentes[1] = 96;
     $lista[22]->componentes[2] = 531;
     // Rabid Horseman
     $lista[23] = new combinacao();
     $lista[23]->qual = 577;
     $lista[23]->componentes = array();
     $lista[23]->componentes[0] = 3;
     $lista[23]->componentes[1] = 53;
     $lista[23]->componentes[2] = 512;
     // Roaring Ocean Snake
     $lista[24] = new combinacao();
     $lista[24]->qual = 611;
     $lista[24]->componentes = array();
     $lista[24]->componentes[0] = 3;
     $lista[24]->componentes[1] = 513;
     $lista[24]->componentes[2] = 379;
     // Skull Knight
     $lista[25] = new combinacao();
     $lista[25]->qual = 656;
     $lista[25]->componentes = array();
     $lista[25]->componentes[0] = 3;
     $lista[25]->componentes[1] = 706;
     $lista[25]->componentes[2] = 19;
     // Thousand Dragon
     $lista[26] = new combinacao();
     $lista[26]->qual = 738;
     $lista[26]->componentes = array();
     $lista[26]->componentes[0] = 3;
     $lista[26]->componentes[1] = 744;
     $lista[26]->componentes[2] = 48;
     // Twin-Headed Thunder Dragon
     $lista[27] = new combinacao();
     $lista[27]->qual = 765;
     $lista[27]->componentes = array();
     $lista[27]->componentes[0] = 3;
     $lista[27]->componentes[1] = 742;
     $lista[27]->componentes[2] = 742;
     // Warrior of Tradition
     $lista[28] = new combinacao();
     $lista[28]->qual = 793;
     $lista[28]->componentes = array();
     $lista[28]->componentes[0] = 3;
     $lista[28]->componentes[1] = 668;
     $lista[28]->componentes[2] = 62;
     
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
      // Elemental HERO Flame Wingman
     $lista[10] = new combinacao();
     $lista[10]->qual = 230;
     $lista[10]->componentes = array();
     $lista[10]->componentes[0] = 3;
     $lista[10]->componentes[1] = 227;
     $lista[10]->componentes[2] = 228;
      // Elemental HERO Phoenix Enforcer
     $lista[11] = new combinacao();
     $lista[11]->qual = 232;
     $lista[11]->componentes = array();
     $lista[11]->componentes[0] = 3;
     $lista[11]->componentes[1] = 227;
     $lista[11]->componentes[2] = 228;
    // Five-Headed Dragon
     $lista[11] = new combinacao();
     $lista[11]->qual = 261;
     $lista[11]->componentes = array();
     $lista[11]->componentes[0] = 5dragões diferentes */
 }
}


class combinacao { // usado para guardar cada fusão que existe
    public $qual;
    public $componentes;
}
?>