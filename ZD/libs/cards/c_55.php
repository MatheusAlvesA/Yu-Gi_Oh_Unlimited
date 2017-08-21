<?php
/*Esta carta ganha 100 de ATK por cada monstro Tipo Winged Beast virado para cima que você controle. 
 * Uma vez por turno, se você controla 3 ou mais monstros Tipo Winged Beast virados para cima, 
 * você pode destruir 1 Carta Spell ou Trap que o seu oponente controle.*/
class c_55 extends Monstro_normal {
    
  function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      $r = parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
      if($r) {
          parent::manter('atk_original', $this->atk);
          $this->duelo->set_engine($this->inst);
      }
     return 1;
    }
    
    function ativar_efeito() {
        $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_especial';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
            $turno = $this->duelo->ler_turno();
            if(parent::ler_variavel('ativado_em') == $turno) {
             parent::avisar('Você só pode ativar esse efeito uma vez por turno!');
             return false;  
            }
     $campo = $this->duelo->ler_campo($this->dono);
     $carta = new DB_cards();
     $quantos = 0;
     for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0 && $campo[$x][0] != 4) {
        $carta->ler_id($campo[$x][1]);
        if($carta->specie == 'winged-beast' && $carta->id != 55) {$quantos++;}
       }
     }
     if($quantos < 3) {
         parent::avisar('Você precisa ter ao menos 3 monstros do tipo Winged Beast em campo para ativar esse efeito');
         return false;
     }
     $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
     $y = 0;
     for($x = 6; $x <= 11; $x++) {
         if($campo[$x][1] != 0) {
             if($campo[$x][0] == 1) {$lista[$y] = $campo[$x][1];}
             else {$lista[$y] = 'ub';}
             $y++;
         }
     }
     if(count($lista) == 0) {
         parent::avisar('O oponente não possui cartas magicas ou armadilhas');
         return false;
     }
     $this->duelo->solicitar_carta('Escolha uma carta', $lista, $this->dono, $this->inst);
     parent::manter('ativado_em', $this->duelo->ler_turno());
     return true;
  }
  
  function carta_solicitada($cartaS) {
     $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
     $y = 0;
     for($x = 6; $x <= 11; $x++) {
         if($campo[$x][1] != 0) {
             $lista[$y] = $campo[$x][1];
             $y++;
         }
     }
  if(!isset($lista)) {
   parent::avisar('Não foi possível ativar o efeito da carta '.$this->nome);
   return false;
  }
  if(!isset($lista[$cartaS])) {
      parent::avisar('Falha ao encontrar a carta');
      return false;
  }
  
  $quantas = 0;
  for($x = 0; $x <= $cartaS; $x++){
    if($lista[$x] == $lista[$cartaS]) {$quantas++;}
  }
    for($x = 6; $x <= 11; $x++) {
         if($campo[$x][1] == $lista[$cartaS]) {
             if($quantas > 1) {$quantas--;}
             else {break;}
         }
     }
  $alvo = $this->duelo->regenerar_instancia_local($x, $this->duelo->oponente($this->dono));
  $efeito[0] = 'destruir';
  $efeito[1] = 'efeito';
  $efeito[2] = 'monstro';
  $efeito[3] = 'oponente';
  $alvo->sofrer_efeito($efeito, $this);
  return true;
}

function engine() {
    $campo = $this->duelo->ler_campo($this->dono);
    $carta = new DB_cards();
    $quantos = 0;
    for($x = 1; $x <= 5; $x++) {
      if($campo[$x][1] != 0 && $campo[$x][0] != 4) {
        $carta->ler_id($campo[$x][1]);
        if($carta->specie == 'winged-beast' && $carta->id != 55) {$quantos++;}
      }
    }
    $quantos *= 100;
    $this->atk = parent::ler_variavel('atk_original') + $quantos;
    
    $grav = new Gravacao();
    $grav->set_caminho($this->pasta.$this->inst.'.txt');
    $infos = $grav->ler(0);
    $infos[3] = $this->atk;
    $grav->set_array($infos);
    $grav->gravar();
    unset($grav);
    return true;
}

function sofrer_efeito($efeito, &$inst) {
    if($efeito[0] == 'incrementar_ATK') {
        parent::manter('atk_original', parent::ler_variavel('atk_original')+$efeito[1]);
        $this->atk += $efeito[1];
       return true;
    }
    else parent::sofrer_efeito($efeito, $inst);
}
        function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
     $campo = $this->duelo->ler_campo($this->dono);
     $carta = new DB_cards();
     $quantos = 0;
     for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0 && $campo[$x][0] != 4) {
        $carta->ler_id($campo[$x][1]);
        if($carta->specie == 'winged-beast' && $carta->id != 55) {$quantos++;}
       }
     }
     if($quantos < 3)
         $comandos_possiveis['ativar'] = false; // mosntro normal não pode ativar efeito
     else
         $comandos_possiveis['ativar'] = true; // mosntro normal pode ativar efeito

   if($this->modo == MODOS::ATAQUE && $phase == 4) $comandos_possiveis['atacar'] = true;
   else $comandos_possiveis['atacar'] = false;
   if($this->modo == MODOS::DEFESA && $phase != 4) $comandos_possiveis['posição_ataque'] = true;
   else $comandos_possiveis['posição_ataque'] = false;
   if($phase != 4 && $this->modo == MODOS::ATAQUE) $comandos_possiveis['posição_defesa'] = true;
   else $comandos_possiveis['posição_defesa'] = false;
   if($this->modo == MODOS::DEFESA_BAIXO && $phase != 4) $comandos_possiveis['flipar'] = true;
   else $comandos_possiveis['flipar'] = false;
   if($phase != 4) $comandos_possiveis['sacrificar'] = true;
   else $comandos_possiveis['sacrificar'] = false;
 return $comandos_possiveis;
}

}
?>