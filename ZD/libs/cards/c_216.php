<?php
class c_216 extends Armadilha {
    /* Carta terminada em 04/05/2017, terminada em 1 dia
     * Selecione e remova do jogo 1 monstro Dragon que você controle virado para cima no campo.
     * Special Summon 1 monstro Dragon da sua mão ou cemitério.
     */

 function ativar_efeito() {
   if(!parent::checar_ativar()) return false;
   parent::manter('sacrificio', 0);
   $campo = $this->duelo->ler_campo($this->dono);
   
   $lista = array();
   $carta = new DB_cards;
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0) {
           $carta->ler_id($campo[$x][1]);
           if($carta->specie == 'dragon' && $campo[$x][0] != MODOS::DEFESA_BAIXO) {
               $lista[$y] = $carta->id;
               $y++;
           }
       }
   }
   
   if($y == 0) {
       parent::avisar('Você não tem dragões virados para cima em campo');
       return false;
   }
   
   $this->duelo->solicitar_carta('Escolha um tributo', $lista, $this->dono, $this->inst);
   return true;
 }
 
 function carta_solicitada($cartaS) {
  if(parent::ler_variavel('sacrificio') == 0) {
   $campo = $this->duelo->ler_campo($this->dono);
   $campo_r = array();
   $lista = array();
   $carta = new DB_cards;
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
       $campo_r[$x] = $campo[$x][1];
       if($campo[$x][1] != 0) {
           $carta->ler_id($campo[$x][1]);
           if($carta->specie == 'dragon' && $campo[$x][0] != MODOS::DEFESA_BAIXO) {
               $lista[$y] = $carta->id;
               $y++;
           }
       }
   }
   
   if($y == 0 || !isset($lista[$cartaS])) {
       parent::avisar('Falha ao ativar o efeito');
       return false;
   }
   
   $tool = new biblioteca_de_efeitos;
   $local = $tool->local_original($lista, $campo_r, (int)$cartaS);
   $sacrificio = $this->duelo->regenerar_instancia_local($local, $this->dono);
   parent::manter('sacrificio', $sacrificio->inst);
   
   $hand = $this->duelo->ler_mao($this->dono);
   $lista_hand = array();
   $y = 0;
   for($x = 1; $x < $hand[0]; $x++) {
        $carta->ler_id($hand[$x]);
        if($carta->specie == 'dragon') {
            $lista_hand[$y] = $carta->id;
            $y++;
        }
   }
   
   $cmt = $this->duelo->ler_cmt($this->dono);
   $lista_cmt = array();
   $y = 0;
   for($x = 1; $x < $cmt[0]; $x++) {
        $carta->ler_id($cmt[$x]);
        if($carta->specie == 'dragon') {
            $lista_cmt[$y] = $carta->id;
            $y++;
        }
   }
   
   if(count($lista_hand) == 0 && count($lista_cmt) == 0) {
       parent::avisar('Você não tem dragões para invocar');
       parent::manter('sacrificio', 0);
       return false;
   }
   
   $y = 0;
   $nova_lista = array();
   for($x = 0; $x < count($lista_hand); $x++) {
       $nova_lista[$y] = $lista_hand[$x];
       $y++;
   }
   $nova_lista[$y] = 'divisor';
   $y++;
   for($x = 0; $x < count($lista_cmt);$x++) {
       $nova_lista[$y] = $lista_cmt[$x];
       $y++;
   }
   $this->duelo->solicitar_carta('Escolha um dragão da MÃO|CEMITÉRIO', $nova_lista, $this->dono, $this->inst);
   return true;
 }
 
 else { // pegando dragão pra invocar especial
   $carta = new DB_cards;
   $hand = $this->duelo->ler_mao($this->dono);
   $lista_hand = array();
   $y = 0;
   for($x = 1; $x < $hand[0]; $x++) {
        $carta->ler_id($hand[$x]);
        if($carta->specie == 'dragon') {
            $lista_hand[$y] = $carta->id;
            $y++;
        }
   }

   $cmt = $this->duelo->ler_cmt($this->dono);
   $lista_cmt = array();
   $y = 0;
   for($x = 1; $x < $cmt[0]; $x++) {
        $carta->ler_id($cmt[$x]);
        if($carta->specie == 'dragon') {
            $lista_cmt[$y] = $carta->id;
            $y++;
        }
   }

   if(count($lista_hand) == 0 && count($lista_cmt) == 0) {
       parent::avisar('Você não tem dragões para invocar');
       parent::manter('sacrificio', 0);
       return false;
   }
   
   $y = 0;
   $nova_lista = array();
   for($x = 0; $x < count($lista_hand); $x++) {
       $nova_lista[$y] = $lista_hand[$x];
       $y++;
   }
   $nova_lista[$y] = 'divisor';
   $posicao_divisor = $y;
   $y++;
   for($x = 0; $x < count($lista_cmt);$x++) {
       $nova_lista[$y] = $lista_cmt[$x];
       $y++;
   }
   
   if(!isset($nova_lista[$cartaS]) || $nova_lista[$cartaS] == 'divisor') {
       parent::avisar('Falha ao ativar');
       return false;
   }
   
   parent::avisar('Efeito da carta '.$this->nome.' ativado', 1);
   parent::mudar_modo(MODOS::ATAQUE);
   $sacrificio = $this->duelo->regenerar_instancia(parent::ler_variavel('sacrificio'), $this->dono);
   $sacrificio->remover_do_jogo();

   $campo = $this->duelo->ler_campo($this->dono);
   $local = 0;
   for($x = 1; $x <= 5; $x++) {
    if($campo[$x][1] == 0) {$local = $x;break;}
   }

   if($local > 5) {
       parent::avisar('Sem espaço para invocar');
       parent::destruir();
       return false;
   }

   $tool = new biblioteca_de_efeitos;
   if($posicao_divisor > $cartaS) $tool->apagar_carta_mao((int)$nova_lista[$cartaS], $this->dono, $this->duelo);
   else $this->duelo->apagar_cmt($nova_lista[$cartaS], $this->dono);
   $this->duelo->invocar($this->dono, $local, MODOS::ATAQUE, $nova_lista[$cartaS], 'especial');
   
   parent::destruir();
   return true;
 }
 }
 
}
?>