<?php
class c_260 extends Magica {
 // carta terminada em 26/05/2017 criada em 1 dia
 /*Destrua o monstro virado para cima com o menor ATK no lado do seu oponente do campo*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  	  $gatilho[0] = 'magica';
	  $gatilho[1] = 'efeito';
          $gatilho[1] = 'destruir_monstro';
	  if(parent::checar($gatilho)) {
		 $carta = &parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		  if($resposta['bloqueado']) {
                      return false;
                  }
		}

  $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
  $lista = array();
  $y = 0;
  for($x = 1; $x <= 5; $x++) {
      if($campo[$x][1] != 0 && $campo[$x][0] != MODOS::DEFESA_BAIXO) {
          $lista[$y] = &$this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
          $y++;
      }
  }
  if($y <= 0) {
      parent::avisar('Não existem monstros no lado do campo do seu oponente virados para cima');
      parent::mudar_modo(MODOS::ATAQUE_BAIXO);
      return false;
  }
  $menor = $lista[0];
  for($x = 1; $x < $y; $x++) {
      if($menor->atk > $lista[$x]->atk) $menor = $lista[$x];
  }
  $efeito[0] = 'destruir';
  $menor->sofrer_efeito($efeito, $this);
  parent::destruir();
  return true;
 }
}
?>