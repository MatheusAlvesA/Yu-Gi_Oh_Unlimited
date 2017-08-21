<?php
class c_319 extends Armadilha {
 // carta terminada em 15/06/2017 criada em 1 dia
 /*Ganhe 300 LPs para cada monstro no campo.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  if(parent::ler_variavel('invocado_em') == parent::ler_turno()) {
      parent::avisar('Você não pode ativar essa armadilha imediatamente');
      return false;
  }
  	  $gatilho[0] = 'magica';
	  $gatilho[1] = 'efeito';
	  if(parent::checar($gatilho)) {
		 $carta = &parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		  if($resposta['bloqueado']) {
                      return false;
                  }
		}

  $quanto = $this->n_monstros()*300;
  parent::dano_direto($this->duelo->dir_duelo.$this->dono.'/lps.txt', -1*$quanto);
  parent::avisar('Efeito da carta '.$this->nome.' foi ativado, o duelista ganhou '.$quanto.' LPs', 1);
  parent::destruir();
  return true;
 }
 
 function n_monstros() {
     $campo = $this->duelo->ler_campo($this->dono);
     $campo_o = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
     $quantos = 0;
     for($x = 1;$x <= 5;$x++) {
         if($campo[$x][1] != 0) $quantos++;
         if($campo_o[$x][1] != 0) $quantos++;
     }
    return $quantos;
 }
}
?>