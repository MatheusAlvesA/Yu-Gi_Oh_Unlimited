<?php
class c_276 extends Magica {
    // carta terminada dia 1/06/2017. terminada em 1 dia
    /*Aumente o ATK e DEF de todos os Insect, Beast, Plant e Beast-Warrior em 200 pontos.*/
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
   
   $efeito_d[0] = 'incrementar_DEF';
   $efeito_d[1] = 200;
   $refeito_d[0] = 'incrementar_DEF';
   $refeito_d[1] = (-200);
   $efeito_a[0] = 'incrementar_ATK';
   $efeito_a[1] = 200;
   $refeito_a[0] = 'incrementar_ATK';
   $refeito_a[1] = (-200);

   $grav = new Gravacao;
   $grav->set_caminho($this->pasta.$this->inst.'_lista.txt');
   $lista = $grav->ler(0);
   // esse laço almenta a defesa dos monstros que ainda não foram afetados
   for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0) {
          $carta = $this->duelo->regenerar_instancia_local($x, $this->dono);
          if(!$this->buscar($lista, $carta->inst) && $this->checar_atrib($carta)) {
              $carta->sofrer_efeito($efeito_a, $this);
              $carta->sofrer_efeito($efeito_d, $this);
              $lista[$lista[0]] = $carta->inst;
              $lista[0]++;
          }
       }
       if($campo_oponente[$x][1] != 0) {
          $carta = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
          if(!$this->buscar($lista, $carta->inst) && $this->checar_atrib($carta)) {
              $carta->sofrer_efeito($efeito_a, $this);
              $carta->sofrer_efeito($efeito_d, $this);
              $lista[$lista[0]] = $carta->inst;
              $lista[0]++;
          }
       }
   }
   
   $grav->set_array($lista);
   $grav->gravar();
   return true;
 }

 private function checar_atrib($carta) {
     switch ($carta->specie) {
         case 'insect':
             return true;
         case 'beast':
             return true;
         case 'beast-warrior':
             return true;
         case 'plant':
             return true;
     }
     return false;;
 }
         
 function destruir($motivo = 0) {
    if(!file_exists($this->pasta.$this->inst.'_lista.txt')) file_put_contents($this->pasta.$this->inst.'_lista.txt', '');
    
   $campo = $this->duelo->ler_campo($this->dono);
   $campo_oponente = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
   
   $refeito_d[0] = 'incrementar_DEF';
   $refeito_d[1] = (-200);
   $refeito_a[0] = 'incrementar_ATK';
   $refeito_a[1] = (-200);

   $grav = new Gravacao;
   $grav->set_caminho($this->pasta.$this->inst.'_lista.txt');
   $lista = $grav->ler(0);
   
     for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0) {
          $carta = $this->duelo->regenerar_instancia_local($x, $this->dono);
          if($this->buscar($lista, $carta->inst)) {
              $carta->sofrer_efeito($refeito_a, $this);
              $carta->sofrer_efeito($refeito_d, $this);
              $lista[$this->buscar($lista, $carta->inst)] = $lista[$lista[0]-1];
              $lista[0]--;
          }
       }
       if($campo_oponente[$x][1] != 0) {
          $carta = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
          if($this->buscar($lista, $carta->inst)) {
              $carta->sofrer_efeito($refeito_a, $this);
              $carta->sofrer_efeito($refeito_d, $this);
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