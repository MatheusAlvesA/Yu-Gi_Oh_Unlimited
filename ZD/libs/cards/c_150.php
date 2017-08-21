<?php
class c_150 extends Magica {
/* carta terminada dia 10/03/2017. terminada em 1 dia
 * Jogue 1 moeda:
 * Se o resultado for Cara: Compre 2 cartas.
 * Se o resultado for Coroa: Seu oponente compra 2 cartas.
 */

function ativar_efeito() {  // Efeito desse monstro
    if(!parent::checar_ativar()) {return false;}
    
    $gatilho[0] = 'magica';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'comprar_carta';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }

    $resultado = $this->duelo->moeda();
    
    if($resultado) {
        parent::avisar('Efeito da carta '.$this->nome.' ativado e o resultado foi CARA. O duelista puxa duas cartas',1);
        $this->duelo->puxar_carta($this->dono); // primaira carta...
        $this->duelo->puxar_carta($this->dono); // ...segunda carta.
    } else {
        parent::avisar('Efeito da carta '.$this->nome.' ativado e o resultado foi COROA. O oponente puxa duas cartas',1);
        $this->duelo->puxar_carta($this->duelo->oponente($this->dono)); // primaira carta...
        $this->duelo->puxar_carta($this->duelo->oponente($this->dono)); // ...segunda carta.
    }
    
   parent::destruir();
   return true;
  }
    
}
?>