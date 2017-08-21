<?php
class c_119 extends Magica {
    // carta terminada dia 25/09/2016. terminada em 1 dia
/*Aumentar a DEF de todos os monstros na posição de defesa em 500 pontos.*/
   function invocar($local, $id, $modo, $dono, $tipo = 'comum') {
        if(!$this->duelo->assumir_field()) return false;
        parent::invocar(11, $id, MODOS::ATAQUE, $dono, $tipo);
        parent::avisar('Campo alterado para a '.$this->nome, 1);
    }
 
 function ativar_efeito() {
     $this->duelo->set_engine($this->inst);
 }

 function engine() {
    if(!file_exists($this->pasta.$this->inst.'_lista.txt')) file_put_contents($this->pasta.$this->inst.'_lista.txt', '');
    
   $campo = $this->duelo->ler_campo($this->dono);
   $campo_oponente = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   
   $efeito[0] = 'incrementar_DEF';
   $efeito[1] = 500;
   $refeito[0] = 'incrementar_DEF';
   $refeito[1] = (-500);

   $grav = new Gravacao;
   $grav->set_caminho($this->pasta.$this->inst.'_lista.txt');
   $lista = $grav->ler(0);
   // esse laço almenta a defesa dos monstros que ainda não foram afetados
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0 && $campo[$x][0] != MODOS::ATAQUE) {
          $carta = $this->duelo->regenerar_instancia_local($x, $this->dono);
          if(!$this->buscar($lista, $carta->inst)) {
              $carta->sofrer_efeito($efeito, $this);
              $lista[$lista[0]] = $carta->inst;
              $lista[0]++;
          }
       }
       if($campo_oponente[$x][1] != 0 && $campo_oponente[$x][0] != MODOS::ATAQUE) {
          $carta = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
          if(!$this->buscar($lista, $carta->inst)) {
             $carta->sofrer_efeito($efeito, $this);
              $lista[$lista[0]] = $carta->inst;
              $lista[0]++;
          }
       }
   }
   // esse laço retira a defesa dos monstros que foram afetados mas mudaram sua posição
      for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0 && $campo[$x][0] == MODOS::ATAQUE) {
          $carta = $this->duelo->regenerar_instancia_local($x, $this->dono);
          if($this->buscar($lista, $carta->inst)) {
              $carta->sofrer_efeito($refeito, $this);
              $lista[$this->buscar($lista, $carta->inst)] = $lista[$lista[0]-1];
              $lista[0]--;
          }
       }
       if($campo_oponente[$x][1] != 0 && $campo_oponente[$x][0] == MODOS::ATAQUE) {
          $carta = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
          if($this->buscar($lista, $carta->inst)) {
              $carta->sofrer_efeito($refeito, $this);
              $lista[$this->buscar($lista, $carta->inst)] = $lista[$lista[0]-1];
              $lista[0]--;
          }
       }
   }
   
   $grav->set_array($lista);
   $grav->gravar();
   return true;
 }

 function destruir($motivo = 0) {
         if(!file_exists($this->pasta.$this->inst.'_lista.txt')) file_put_contents($this->pasta.$this->inst.'_lista.txt', '');
    
   $campo = $this->duelo->ler_campo($this->dono);
   $campo_oponente = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   
   $refeito[0] = 'incrementar_DEF';
   $refeito[1] = (-500);

   $grav = new Gravacao;
   $grav->set_caminho($this->pasta.$this->inst.'_lista.txt');
   $lista = $grav->ler(0);
   
     for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0) {
          $carta = $this->duelo->regenerar_instancia_local($x, $this->dono);
          if($this->buscar($lista, $carta->inst)) {
              $carta->sofrer_efeito($refeito, $this);
              $lista[$this->buscar($lista, $carta->inst)] = $lista[$lista[0]-1];
              $lista[0]--;
          }
       }
       if($campo_oponente[$x][1] != 0) {
          $carta = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
          if($this->buscar($lista, $carta->inst)) {
              $carta->sofrer_efeito($refeito, $this);
              $lista[$this->buscar($lista, $carta->inst)] = $lista[$lista[0]-1];
              $lista[0]--;
          }
       }
   }
     @unlink($this->pasta.$this->inst.'_lista.txt');
     parent::destruir($motivo);
     return true;
 }
 
 function buscar($lista, $carta) {
     for($x = 1; $x < $lista[0]; $x++){
         if($lista[$x] == $carta) return $x;
     }
     return false;
 }
 
}
?>