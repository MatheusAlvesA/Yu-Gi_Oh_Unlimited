<?php

/* terminado dia 15/07/2016 terminado em 1 dia
 * Se o seu oponente controla um monstro e você não, 
 * você pode Normal Summon ou Set esta carta sem Tributar.
 * Uma vez por turno, você pode selecionar 1 monstro (Blackwing) virado para cima que você controle. 
 * Até o fim desse turno o ATK dele torna-se igual ao total de ATK
 * de todos os monstros (Blackwing) no campo exceto dele mesmo. 
 * Monstros fora o selecionado não poderá atacar no turno em que você ativar esse efeito.
 */

class c_85 extends Monstro_normal {
    
  function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
     $campo = $this->duelo->ler_campo($dono);
     $campo_oponente = $this->duelo->ler_campo($this->duelo->oponente($dono));
     $duelista = false;
     $oponente = false;
     for($x = 1; $x <= 5; $x++) {
       if($campo[$x][1] != 0) $duelista = true;
       if($campo_oponente[$x][1] != 0) $oponente = true;
     }
     if($oponente && !$duelista) {
         parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
         $temp = $this->duelo->regenerar_instancia_local($local, $dono);
         if(!file_exists($this->pasta.'/m_invocado.txt')) @file_put_contents($this->pasta.'/m_invocado.txt', $temp->inst);
         return true;
     }
    return parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
 }
 
 function ativar_efeito() {
     if(parent::ler_variavel('NEGAR_EFEITO') == 1 || parent::ler_variavel('ativado_em') == parent::ler_turno()) {return false;}
  $campo = $this->duelo->ler_campo($this->dono);
  $y = 0;
  for($x = 1; $x <= 5;$x++) {
      if($campo[$x][1] >= 79 && $campo[$x][1] <= 85) {
          $lista[$y] = $campo[$x][1];
          $y++;
      }
  }
  if(!is_array($lista)) {
      parent::avisar('Você não possui monstros do tipo Blackwing em campo');
      return false;
  }
  
  $this->duelo->solicitar_carta('Escolha um monstro', $lista, $this->dono, $this->inst);
  return true;
 }
 
  function carta_solicitada($cartaS) {
     if(parent::ler_variavel('NEGAR_EFEITO') == 1 || parent::ler_variavel('ativado_em') == parent::ler_turno()) {return false;}
  $campo = $this->duelo->ler_campo($this->dono);
  $carta = new DB_cards;
  $y = 0;
  $ataque = 0;
  for($x = 1; $x <= 5;$x++) {
      if($campo[$x][1] >= 79 && $campo[$x][1] <= 85) {
          $carta->ler_id($campo[$x][1]);
          $ataque += $carta->atk;
          $lista[$y] = $campo[$x][1];
          $y++;
      }
  }
  if(!is_array($lista) || !$lista[$cartaS]) {return false;}
  $quantas = 0;
  for($x = 0; $x < count($lista);$x++) if($lista[$x] == $lista[$cartaS]) $quantas++;
  for($x = 1; $x <= 5 && $quantas > 0;$x++) if($campo[$x][1] == $lista[$cartaS]) $quantas--;
  
  $monstro = $this->duelo->regenerar_instancia_local($x-1, $this->dono);
  $ataque -= $monstro->atk;
  $this->inserir_ataque($monstro->inst, $ataque);
  parent::manter('monstro', $monstro->inst);
  	    $gatilho[0] = 'monstro';
	    $gatilho[1] = 'ataque';
	    $gatilho[2] = 'tudo';
	    $gatilho[3] = $this->dono.'-'.$this->inst.'-self';
	    parent::set_gatilho($gatilho);
            $this->escrever($monstro->inst);
            $this->duelo->agendar_tarefa($this->inst.'_script.php', $this->dono, 'end_phase', 'x', true);
     parent::manter('ativado_em', parent::ler_turno());
     parent::avisar('O efeito da carta '.$this->nome.' foi ativado no monstro '.$monstro->nome, 1);
     return true;
 }
 
 function acionar($g, $monstro) {
  if($monstro->inst != parent::ler_variavel('monstro') && parent::ler_variavel('ativado_em') == parent::ler_turno()) $r['bloqueado'] = true;
  else $r['bloqueado'] = false;
  return $r;
 }
 
 function inserir_ataque($inst, $ataque) {
	$grav = new Gravacao();
	$grav->set_caminho($this->pasta.$inst.'.txt');
	$infos = $grav->ler(0);
          $infos[3] = $ataque;
        $grav->set_array($infos);
        $grav->gravar();
        unset($grav);
        return true;
 }
 
 function escrever($inst) {
     $monstro = $this->duelo->regenerar_instancia($inst, $this->dono);
     $carta = new DB_cards;
     $carta->ler_id($monstro->id);
     $codigo = '
        $grav = new Gravacao();
	$grav->set_caminho("'.$this->pasta.$inst.'.txt");
	$infos = $grav->ler(0);
        $infos[3] = '.$carta->atk.';
        $grav->set_array($infos);
        $grav->gravar();
        unset($grav);
        $this->apagar_tarefa('.$this->dono.', "end_phase", "'.$this->inst.'_script.php'.'");
        unlink("'.$this->pasta.$this->inst.'_script.php'.'");
        ';
     file_put_contents($this->pasta.$this->inst.'_script.php', $codigo);
     return true;
 }
         
 function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   if(parent::ler_variavel('NEGAR_EFEITO') == 1 || parent::ler_variavel('ativado_em') == parent::ler_turno()) $comandos_possiveis['ativar'] = false;
   else $comandos_possiveis['ativar'] = true;
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