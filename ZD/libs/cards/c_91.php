<?php
class c_91 extends Magica {
    // carta terminada dia 07/09/2016 terminada em 1 dia
/*Selecione 1 monstro virado para cima em Modo de Ataque que o seu oponente controle e o altere para Modo de Defesa.*/

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar()) {return false;}
  
   $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0 && $campo[$x][0] == MODOS::ATAQUE) {
          $lista[$y] = $campo[$x][1];
          $y++;
       }
   }
   if(!$lista) {
       parent::avisar('Block Attack não pode ser ativada agora');
       parent::mudar_modo(MODOS::ATAQUE_BAIXO);
       return false;
   }
   $this->duelo->solicitar_carta("ESCOLHA UM MONSTRO", $lista, $this->dono, $this->inst);
  
   return true;
 }
 
 function carta_solicitada($cartaS) {
   $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   $y = 0;
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0 && $campo[$x][0] == MODOS::ATAQUE) {
          $lista[$y] = $campo[$x][1];
          $y++;
       }
   }
   if(!$lista || !$lista[$cartaS]) {return false;}
  $quantas = 0;
   for($x = 0;$x <= $cartaS;$x++) {
       if($lista[$x] == $lista[$cartaS]) $quantas++;
   }
   for($x = 1;$x <= 5 && $quantas >= 1;$x++) {
       if($campo[$x][1] == $lista[$cartaS]) $quantas--;
   }
   
  $efeito[0] = 'mudar_modo';
  $efeito[1] = MODOS::DEFESA;
  $instancia = $this->duelo->regenerar_instancia_local($x-1, $this->duelo->oponente($this->dono));
  if($instancia->sofrer_efeito($efeito, $this) === 'bloqueado') {parent::destruir(); return false;}
  parent::destruir();
  parent::avisar('Efeito da carta '.$this->nome.' foi ativado, '.$instancia->nome.' foi afetado.', 1);
  return true;
 }

}
?>