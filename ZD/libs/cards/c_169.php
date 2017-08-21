<?php
class c_169 extends Magica {
/* carta terminada dia 16/03/2017. terminada em 1 dia
 * Adicione 1 monstro DARK com 1500 ou menos de ATK do seu Cemitério á sua mão.*/

function ativar_efeito() {  // Efeito desse monstro
    if(!parent::checar_ativar()) {return false;}
    
    $gatilho[0] = 'magica';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'ressucitar_carta';
    $gatilho[3] = 'mão';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    
    // formando lista do cemiterio
    $cmt = $this->duelo->ler_cmt($this->dono);
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
        if($this->is($cmt[$x])) { // id de um monstro constellar
            $lista_cmt[$y] = $cmt[$x];
            $y++;
        }
    }
    
 if(!isset($lista_cmt)) {
     parent::avisar('Não há monstros dark com 1500 ou menos de ataque no seu cemitério');
     parent::mudar_modo(MODOS::ATAQUE_BAIXO);
     return false;
 }
    
  $this->duelo->solicitar_carta("Escolha o dark para resucitar", $lista_cmt, $this->dono, $this->inst);
   return true;
  }
 
  function carta_solicitada($cartaS) {
        //todo agora receber o que é pra ressucitar
    $cmt = $this->duelo->ler_cmt($this->dono);
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
        if($this->is($cmt[$x])) { // id de um monstro constellar
            $lista_cmt[$y] = $cmt[$x];
            $y++;
        }
    }
    
 if(!isset($lista_cmt) || !isset($lista_cmt[$cartaS])) {
     parent::avisar('falha ao ativar');
     return false;
 }    
    parent::mudar_modo(MODOS::ATAQUE);
    $this->duelo->apagar_cmt($lista_cmt[$cartaS], $this->dono);
    $this->duelo->colocar_carta_hand($lista_cmt[$cartaS], $this->dono);
    $carta = new DB_cards;
    $carta->ler_id($lista_cmt[$cartaS]);
    parent::avisar('Efeito da carta '.$this->nome.' ativado. '.$carta->nome.' foi movido do cemitério para a mão', 1);
    parent::destruir();
    return true;
}

  
function is($id) {
    $carta = new DB_cards;
    $carta->ler_id($id);
    if($carta->categoria === 'monster' && $carta->atributo === 'dark' && $carta->atk <= 1500) return true;
    else return false;
 }
 
}
?>