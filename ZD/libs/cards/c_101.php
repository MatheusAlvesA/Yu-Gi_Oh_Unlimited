<?php
/*
 * Carta terminada no dia 11/09/2016
 * terminada em 1 dia
 */
class c_101 extends Monstro_normal {
    /*Quando esta carta é Normal Summoned:
     * Você pode direcionar um face-up monstro no campo, exceto esta carta,
     * aumentar o níve do alvo em 2 até a EndPhase.*/
 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
     parent::invocar($local, $id, $modo, $dono,$tipo,$flags);
     if($tipo == 'comum') $this->ativar_efeito($local);
    return true;
 }

function carta_solicitada($cartaS) {
     $campo = $this->duelo->ler_campo($this->dono);
     $carta = new DB_cards;
     $y = 0;
     for($x = 1; $x <= 5; $x++) { // percorre todo o campo incluindo o field
       $carta->ler_id($campo[$x][1]);
       if($campo[$x][1] != 0 && $campo[$x][0] != MODOS::DEFESA_BAIXO && $x != parent::ler_variavel('local')) {
           $lista[$y] = $campo[$x][1];
           $y++;
       }
     }
     $lista[$y] = 'divisor';
     $y++;
     $campo_oponente = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
     for($x = 1; $x <= 5; $x++) { // percorre todo o campo de monstros
       $carta->ler_id($campo_oponente[$x][1]);
       if($campo_oponente[$x][1] != 0 && $campo_oponente[$x][0] != MODOS::DEFESA_BAIXO) {
           $lista[$y] = $campo_oponente[$x][1];
           $y++;
       }
     }
     $efeito[0] = 'incrementar_LV'; // efeito
     $efeito[1] = 2;
     for($divisor = 0; $lista[$divisor] != 'divisor'; $divisor++); // descobrindo onde se divide
     if($cartaS < $divisor) $dono = $this->dono;
     elseif($cartaS > $divisor) $dono = $this->duelo->oponente($this->dono); // definido o dono
     if($dono == $this->dono) {
         for($x = 0; $x <= $cartaS; $x++) if($lista[$x] == $lista[$cartaS]) $quantas++;
         for($x = 1; $x <= 5;$x++) {
             if($campo[$x][1] == $lista[$cartaS] && $x != parent::ler_variavel('local')) {
                 $quantas--; 
                 if($quantas == 0) break;
             }
         }
         $instancia = $this->duelo->regenerar_instancia_local($x, $this->dono);
         if($instancia->sofrer_efeito($efeito, $this) === 'destruir') {parent::destruir();return false;}
         $this->escrever_script($instancia->inst, $this->dono);
     }
     else {
         for($x = $divisor+1; $x <= $cartaS; $x++) if($lista[$x] == $lista[$cartaS]) $quantas++;
         for($x = 1; $x <= 5;$x++) {
           if($campo_oponente[$x][1] == $lista[$cartaS]) {
             $quantas--; 
             if($quantas == 0) break;
           }
         }
         $instancia = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
         if($instancia->sofrer_efeito($efeito, $this) === 'destruir') {parent::destruir();return false;}
         $this->escrever_script($instancia->inst, $this->duelo->oponente($this->dono));
     }
     parent::avisar('Efeito da carta '.$this->nome.' ativado, '.$instancia->nome.' foi afetado', 1);
     return true;
}

 function ativar_efeito($local) {
     parent::manter('local', $local);
     $campo = $this->duelo->ler_campo($this->dono);
     $carta = new DB_cards;
     $y = 0;
     for($x = 1; $x <= 5; $x++) { // percorre todo o campo incluindo o field
       $carta->ler_id($campo[$x][1]);
       if($campo[$x][1] != 0 && $campo[$x][0] != MODOS::DEFESA_BAIXO && $x != parent::ler_variavel('local')) {
           $lista[$y] = $campo[$x][1];
           $y++;
       }
     }
     $lista[$y] = 'divisor';
     $y++;
     $campo_oponente = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
     for($x = 1; $x <= 5; $x++) { // percorre todo o campo incluindo o field
       $carta->ler_id($campo_oponente[$x][1]);
       if($campo_oponente[$x][1] != 0 && $campo_oponente[$x][0] != MODOS::DEFESA_BAIXO) {
           $lista[$y] = $campo_oponente[$x][1];
           $y++;
       }
     }
     $this->duelo->solicitar_carta('CARTAS: SUAS | OPONENTE', $lista, $this->dono, $this->inst);
     return true;
 }

 function escrever_script($qual, $quem) {
     $str = "$quem".'/'.$qual.'.txt';
     $codigo = 'if(file_exists($this->dir_duelo."'.$str.'")) {
       $grav = new Gravacao();
       $grav->set_caminho($this->dir_duelo."'.$str.'");
       $infos = $grav->ler(0);
       $infos[2] -= 2;
       $grav->set_array($infos);
       $grav->gravar();
       unlink($this->dir_duelo.$dono."/".$matriz[$x][0]);
     }
     ';
     file_put_contents($this->pasta.'script_'.$this->inst.'.php', $codigo);
     $this->duelo->agendar_tarefa('script_'.$this->inst.'.php', $this->dono, 'end_phase', 0, true);
 }
 
}
 ?>