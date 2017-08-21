<?php
class c_173 extends Magica {
/* carta terminada dia 23/03/2017. terminada em 1 dia
 * Pague metade dos seus LPs para Special Summon 1 (Dark Magician) do seu Deck.*/

function ativar_efeito() {  // Efeito desse monstro
    if(!parent::checar_ativar()) {return false;}
    
    $gatilho[0] = 'magica';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'invocar';
    $gatilho[3] = 'from_deck';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    
    // procurando no deck e registrando lugar
    $deck = $this->duelo->ler_deck($this->dono);
    $posicao = -1;
    for($x = 1; $x < $deck[0]; $x++) {
        if($deck[$x] == 174) { // id de um monstro constellar
            $posicao = $x;
            break;
        }
    }
    
    //buscando um lugar no campo para invocar
    $campo = $this->duelo->ler_campo($this->dono);
    $local = -1;
    for($x = 1; $x <= 5; $x++) {
        if($campo[$x][1] === 0) {
            $local = $x;
            break;
        }
    }
    
 if($posicao === -1) {
     parent::avisar('Não há nenhum Dark Magician no seu deck');
     parent::destruir();
     return false;
 }
 if($local === -1) {
     parent::avisar('Não há espaço em campo para incovar');
     parent::mudar_modo(MODOS::ATAQUE_BAIXO);
     return false;
 }
 
 parent::mudar_modo(MODOS::ATAQUE);
 parent::avisar('Efeito da carta '.$this->nome.' ativado. Um Dark Magician foi selecionado do deck para ser invocado', 1);
 $this->duelo->apagar_carta_deck($this->dono, $posicao);
 $this->duelo->invocar($this->dono, $local, MODOS::ATAQUE, 174, 'especial');
 
 file_put_contents($this->pasta.'lps.txt', (int)((int)file_get_contents($this->pasta.'lps.txt')/2));
 
 parent::destruir();
   return true;
  }
 
}
?>