<?php
class c_210 extends Armadilha {
/* Terminada em 29/04/2017. Terminada em 1 dia
 * Selecione 2 monstros (Six Samurai) do seu Cemitério e os Special Summon na Posição de Ataque.
 * Destrua eles durante a End Phase deste turno e receba dano igual ao ATK de todos os monstros destruídos.
 */
 function ativar_efeito() {  // unica função dessa armadilha
  if(!parent::checar_ativar()) {return false;}
  if(parent::ler_variavel('monstro_1') != 0 && parent::ler_variavel('monstro_2') != 0) return false;
  
        $gatilho[0] = 'armadilha';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'invocar';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
   
       parent::manter('monstro_1', 0);
       parent::manter('monstro_2', 0);
       
    $cmt = $this->duelo->ler_cmt($this->dono);
    $lista = array();
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
       if(isSixSamurai($cmt[$x])) {
           $lista[$y] = $cmt[$x];
           $y++;
       }
    }
    if($y < 2) {
        parent::avisar('Você não tem six samurai suficientes no cemitério');
        return false;
    }    
    
    $this->duelo->solicitar_carta('Escolha o primeiro Six Samurai', $lista, $this->dono, $this->inst);
    parent::mudar_modo(MODOS::ATAQUE);
    $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
    $this->duelo->agendar_tarefa($this->inst, $this->dono, 'start_phase', 'x');
    
  return true;
 }
 
 function carta_solicitada($cartaS) {
     if(parent::ler_variavel('monstro_1') != 0) {
        $cmt = $this->duelo->ler_cmt($this->dono);
        $lista = array();
        $y = 0;
        $monstro_1 = parent::ler_variavel('monstro_1');
        for($x = 1; $x < $cmt[0]; $x++) {
            if(isSixSamurai($cmt[$x]) && $cmt[$x] != $monstro_1) {
                $lista[$y] = $cmt[$x];
                $y++;
            } else if($cmt[$x] == $monstro_1) $monstro_1 = 0;
        }
    
        if($y == 0 || !isset($lista[$cartaS])) {
            parent::avisar('Falha ao ativar');
            parent::destruir();
            return false;
        }
        
         parent::manter('monstro_2', $lista[$cartaS]);
         $this->_ativar();
         return true;
     }
     //Gravando o primeiro six samurai
    $cmt = $this->duelo->ler_cmt($this->dono);
    $lista = array();
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
       if(isSixSamurai($cmt[$x])) {
           $lista[$y] = $cmt[$x];
           $y++;
       }
    }
    
    if($y == 0 || !isset($lista[$cartaS])) {
        parent::avisar('Falha ao ativar');
        parent::destruir();
        return false;
    }
    
    parent::manter('monstro_1', $lista[$cartaS]);
    
    unset($lista);
    $y = 0;
    $monstro_1 = parent::ler_variavel('monstro_1');
    for($x = 1; $x < $cmt[0]; $x++) {
       if(isSixSamurai($cmt[$x]) && $cmt[$x] != $monstro_1) {
           $lista[$y] = $cmt[$x];
           $y++;
       } else if($cmt[$x] == $monstro_1) $monstro_1 = 0;
    }
    $this->duelo->solicitar_carta('Escolha o segundo Six Samurai', $lista, $this->dono, $this->inst);
    return true;
 }
 
 function _ativar() {
     $campo = $this->duelo->ler_campo($this->dono);
     $local_1 = 0;
     $local_2 = 0;
     for($x = 1; $x < 5; $x++) {
         if($local_1 == 0) {
             if($campo[$x][1] == 0) $local_1 = $x;
         } else
         if($local_2 == 0) {
             if($campo[$x][1] == 0) $local_2 = $x;
         }
     }
     
     if($local_1 == 0 || $local_2 == 0) {
         parent::avisar('Você não tem espaço em campo para invocar os dois Six Samurai');
         parent::destruir();
         return false;
     }

     $this->duelo->apagar_cmt(parent::ler_variavel('monstro_1'), $this->dono);
     $this->duelo->apagar_cmt(parent::ler_variavel('monstro_2'), $this->dono);
     
     $this->duelo->invocar($this->dono, $local_1, MODOS::ATAQUE, parent::ler_variavel('monstro_1'), 'especial');
     $this->duelo->invocar($this->dono, $local_2, MODOS::ATAQUE, parent::ler_variavel('monstro_2'), 'especial');
     
     $cods = $this->duelo->ler_campo_cods($this->dono);
     parent::manter('monstro_1', $cods[$local_1]);
     parent::manter('monstro_2', $cods[$local_2]);
     
     return true;
 }
         
 function tarefa($txt) {
     
     if(parent::ler_variavel('monstro_1') != 0 && parent::ler_variavel('monstro_2') != 0) {
         $monstro_1 = $this->duelo->regenerar_instancia(parent::ler_variavel('monstro_1'), $this->dono);
         $monstro_2 = $this->duelo->regenerar_instancia(parent::ler_variavel('monstro_2'), $this->dono);
         $atk = 0;
         if($monstro_1) {
            $atk = $monstro_1->atk;
            $monstro_1->destruir();
         }
         if($monstro_2) {
            $atk += $monstro_2->atk;
            $monstro_2->destruir();
         }
         $this->duelo->alterar_lp(-1*$atk, $this->dono);
     }
     
     parent::destruir();
 }
 
}
?>