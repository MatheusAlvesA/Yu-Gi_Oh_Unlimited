<?php
// carta terminada dia: 02/67/2016 terminada em um dia
// não jantei e agora estou com fome as 22 da noite
/*
Quando um monstro do seu oponente declara um ataque a um monstro Blackwing que você controla: 
remova do jogo todos os monstros em posição de ataque que o seu oponente controla.
 */
class c_73 extends Armadilha {
	function invocar($local, $id, $modo, $dono, $tipo, $gatilho = 'ataque_monstro') {
  	parent::invocar($local, $id, $modo, $dono, $tipo, 'ataque_monstro');
 }

 function acionar($gatilho, $quem) {
  $r['bloqueado'] = $this->_ativar($gatilho, $quem);
  return $r;
 }
 
 function _ativar($gatilho, $quem) {  // unica função dessa armadilha
  if(!parent::checar_ativar()) {return false;}
  $codigo_alvo = $gatilho[4];
  $monstro_alvo = $this->duelo->regenerar_instancia($codigo_alvo, $this->dono);
  if(!($monstro_alvo->id >= 79 && $monstro_alvo->id <= 85)) {return false;}
  if(file_exists($this->id.'ativada.reg')) {
      if(file_get_contents($this->id.'ativada.reg') == parent::ler_turno()) {return false;}
  }
        $gatilho[0] = 'armadilha';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_monstro';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {
             parent::out_gatilho();
             parent::destruir();
             return false;
         }
       }
            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
            parent::avisar('Efeito da carta '.$this->nome.' ativada.', 1);
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_armadilha';
            for($x = 1; $x <= 5; $x++) { // loop que procura monstros do adversário para destruir
             if($campo[$x][1] != 0) {
              if($campo[$x][0] == 1) { // só se estiver em ataque
               $alvo = &$this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
               $alvo->sofrer_efeito($efeito, $this);
              }
             }
            }
           
   file_put_contents($this->pasta.$this->dono.'/'.$this->id.'ativada.reg', parent::ler_turno()); // essa carta só pode ser ativada uma vez 
   parent::out_gatilho();
   parent::destruir();
  return true;
 }
}
?>