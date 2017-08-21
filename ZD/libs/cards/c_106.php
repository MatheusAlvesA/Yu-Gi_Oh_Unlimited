<?php
class c_106 extends Magica {
 // carta terminada em 17/09/2016. carta terminada em 1 dia
// Remova do jogo todas as cartas na sua mão e no seu lado do campo.
 // Compre cartas até você ter 2 cartas na sua mão.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  	  $gatilho[0] = 'magica';
	  $gatilho[1] = 'efeito';
	  if(parent::checar($gatilho)) {
		 $carta = &parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		  if($resposta['bloqueado']) {
                      return false;
                  }
		}

            $campo = $this->duelo->ler_campo($this->dono);
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_magica_self';
            for($x = 1; $x <= 11; $x++) {
             if($campo[$x][1] != 0) {
                $alvo = &$this->duelo->regenerar_instancia_local($x, $this->dono);
                $alvo->sofrer_efeito($efeito, $this);
             }
            }
            
            @unlink($this->pasta.'hand.txt');
            file_put_contents($this->pasta.'hand.txt', '');
            
            $this->duelo->puxar_carta($this->dono);
            $this->duelo->puxar_carta($this->dono);
            
  parent::avisar('Efeito da carta '.$this->nome.'. O campo e a mão do duelista foram obliterados, duas cartas foram puxadas', 1);
  parent::destruir();
  return true;
 }
}
?>