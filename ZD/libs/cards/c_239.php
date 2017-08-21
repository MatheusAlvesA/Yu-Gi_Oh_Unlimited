<?php
/*
 * Carta terminada no dia 11/05/2017, terminada em 1 dia
 * Esta carta não pode ser Normal Summoned ou Set.
 * Esta carta não pode ser Special Summoned exceto removendo 2 monstros (Six Samurai) no seu Cemitério do jogo.
 * Uma vez por turno, você pode destruir 1 monstro virado para cima no campo.
 * Se este efeito for ativado, esta carta não poderá atacar durante este turno.
 */
class c_239 extends Monstro_normal {

 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
  if($this->inst) {return 0;}
   
  $monstro = new DB_cards();
  $monstro->ler_id($id);
  if($tipo == 'comum') { // processo de Invocação comum
   if($modo != 1 && $modo != 4) {return 0;}
   if(!file_exists($this->pasta.'/sacrificios.txt')) {
     $arq = fopen($this->pasta.'/sacrificios.txt', 'w');
     fwrite($arq, '0');
     fclose($arq);
   }
   $arq = fopen($this->pasta.'/phase.txt', 'r');
   $phase = fgets($arq);
   fclose($arq);
   if($phase != 3 && $phase != 5) {parent::avisar('Você não pode invocar fora das MainPhases'); return 0;}
   // pulando a etapa de checar sacrificios pis essa carta não precisa de sacrificios comuns
  }
  elseif($tipo == 'especial' || $tipo == 'controle') { // processo de Invocação especial não é permitido
      $this->avisar('Essa carta não pode ser especial Summoned');
      return false;
  }
  $this->nome = $monstro->nome;
  $this->lv = $monstro->lv;
  $this->atk = $monstro->atk;
  $this->def = $monstro->def;
  $this->atributo = $monstro->atributo;
  $this->specie = $monstro->specie;
  $this->id = $monstro->id;
  $this->modo = $modo;
  unset($monstro);
  $this->dono = $dono;
  $this->inst = uniqid();
                 
  $gatilho[0] = 'monstro';
  $gatilho[1] = 'invocação';
  $gatilho[2] = $tipo;
  if(parent::checar($gatilho)) {
    $carta = &parent::checar($gatilho);
    $resposta = $carta->acionar($gatilho, $this);
   if($resposta['bloqueado']) {
     return false;
   }
  }
   
  $infos = "$this->nome\n$this->lv\n$this->atk\n$this->def\n$this->atributo\n$this->specie\n$this->id\n$this->modo";
  $arq = fopen($this->pasta.$this->inst.'.txt', 'w');
  fwrite($arq, $infos);
  fclose($arq);
  $arq = fopen($this->pasta.'_'.$this->inst.'.txt', 'w');
  fwrite($arq, 'invocado_em;'.parent::ler_turno());
  fclose($arq);
		
  $grav = new Gravacao();
  $grav->set_caminho($this->pasta.'/campo.txt');
  $campo = $grav->ler(0);
  $campo[$local] = $this->inst;
  $grav->set_array($campo);
  $grav->gravar();
  unset($grav);
	
  if($this->modo != 4) {
    parent::avisar($this->nome.' foi invocado', 1);
  }
  else {parent::avisar('Um monstro face para baixo foi invocado', 1);}
  
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'destruir');
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'battle_phase', 'destruir');
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm1_phase', 'destruir');
  $this->duelo->agendar_tarefa($this->inst, $this->dono, 'm2_phase', 'destruir');
  file_put_contents($this->pasta.'/m_invocado.txt', $this->inst);
   $this->oferendas(); // executando a função que recebe as oferendas
   if($flags['não_mudar_modo'] === true) parent::manter ('modo_alterado_em', parent::ler_turno());
    return true;
 }
 
function oferendas() {
   $carta = new DB_cards();
   $cmt = $this->duelo->ler_cmt($this->dono);
   if(!$this->ler_variavel('oferendas')) { // caso nenhum tenha sido selecionado então o Six Samurai é selecionado
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
     if(isSixSamurai($cmt[$x])) {
       $lista_cmt[$y] = $cmt[$x];
       $y++;
     }
    }
    if($y < 2) {
        $this->avisar('Você não possui monstros Six Samurai suficientes no cemitério');
        $this->tarefa('x'); // executando auto destruição
        return false;
    }
    $this->duelo->solicitar_carta('PRIMEIRO SAMURAI', $lista_cmt, $this->dono, $this->inst);
    return true;
   }
   else { // caso o primeiro Six Samurai já tenha sido sacrificado então é a vez do segundo
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
     if(isSixSamurai($cmt[$x])) {
       $lista_cmt[$y] = $cmt[$x];
       $y++;
     }
    }
    $this->duelo->solicitar_carta('SEGUNDO SAMURAI', $lista_cmt, $this->dono, $this->inst);
    return true;
   }
}

function carta_solicitada($cartaS) {
  if(parent::ler_variavel('oferendas') == 2) { // nesse caso é o efeito e não a invocação que está acontecendo
     $carta = new DB_cards;
     $y = 0;
     $campo_oponente = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
     for($x = 1; $x <= 5; $x++) { // percorre todo o campo incluindo o field
       $carta->ler_id($campo_oponente[$x][1]);
       if($campo_oponente[$x][1] != 0 && $campo_oponente[$x][0] != MODOS::DEFESA_BAIXO) {
           $lista[$y] = $campo_oponente[$x][1];
           $y++;
       }
     }
     
     $lista[$y] = 'divisor';
     $y++;
     
     $campo = $this->duelo->ler_campo($this->dono);
     for($x = 1; $x <= 5; $x++) { // percorre todo o campo
       $carta->ler_id($campo[$x][1]);
       if($campo[$x][1] != 0 && $campo[$x][0] != MODOS::DEFESA_BAIXO) {
           $lista[$y] = $campo[$x][1];
           $y++;
       }
     }

     if(count($lista) <= 1 || !$lista[$cartaS] || $lista[$cartaS] == 'divisor') {return false;}
     
     $efeito[0] = 'destruir'; // esse efeito é enviado a todos os monstros
     for($divisor = 0; $lista[$divisor] != 'divisor'; $divisor++); // descobrindo onde se divide
     if($cartaS < $divisor) $dono = $this->duelo->oponente($this->dono);
     elseif($cartaS > $divisor) $dono =  $this->dono;
     
     if($dono == $this->dono) {
         for($x = 0; $x <= $cartaS; $x++) if($lista[$x] == $lista[$cartaS]) $quantas++;
         for($x = 1; $x <= 5;$x++) {
             if($campo[$x][1] == $lista[$cartaS]) {
                 $quantas--; 
                 if(quantas == 0) break;
             }
         }
         $instancia = $this->duelo->regenerar_instancia_local($x, $this->dono);
         if($instancia->sofrer_efeito($efeito, $this) === 'destruir') {parent::destruir();return false;}
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
     }
     parent::manter('ativou_em', parent::ler_turno()); // registrando para não poder usar mais
     return true;
  }
   $cmt = $this->duelo->ler_cmt($this->dono);
   $carta = new DB_cards;
   if(!$this->ler_variavel('oferendas')) { // caso nenhum tenha sido selecionado então o primeiro é selecionado
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
     if(isSixSamurai($cmt[$x])) {
       $lista_cmt[$y] = $cmt[$x];
       $y++;
     }
    }
    $carta->ler_id($lista_cmt[$cartaS]);
    if(!$lista_cmt[$cartaS] || !isSixSamurai($lista_cmt[$cartaS])) { // algo deu errado
      $this->tarefa('x');
      return false;
    }
// checando se o efeito pode continuar a partir daqui
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
     if(isSixSamurai($cmt[$x])) $y++;
    }
    if($y == 0) {
        $this->avisar('Você não possui monstros Six Samurai no cemitério');
        $this->tarefa('x'); // executando auto destruição
        return false;
    }
// se chegar aqui o efeito pode continuar
    $this->duelo->apagar_cmt($lista_cmt[$cartaS], $this->dono); // removendo o sacrificio do jogo
    $this->manter('oferendas', 1);
    $this->oferendas();
    return true;
   }
   else { // caso o primeiro já tenha sido sacrificado então é a vez do dark
    $y = 0;
    for($x = 1; $x < $cmt[0]; $x++) {
     if(isSixSamurai($cmt[$x])) {
       $lista_cmt[$y] = $cmt[$x];
       $y++;
     }
    }
    $carta->ler_id($lista_cmt[$cartaS]);
    if(!$lista_cmt[$cartaS] || !isSixSamurai($lista_cmt[$cartaS])) { // algo deu errado
      $this->tarefa('x'); // auto destruir
      return false;
    }
    $this->duelo->apagar_cmt($lista_cmt[$cartaS], $this->dono); // removendo o sacrificio do jogo
    $this->manter('oferendas', 2);
    $this->tarefa('x'); // removendo a auto destruição
   }
}

 function tarefa($txt) { // se chegar aqui é pq não coseguiu se invocar
     if($txt === 'resetar') {
         parent::manter ('atacou_em', 0);
         return true;
     }
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm1_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'm2_phase', $this->inst);
     $this->duelo->apagar_tarefa($this->dono, 'battle_phase', $this->inst);
     @unlink($this->pasta.'/m_invocado.txt'); // o monstro não foi invocado na pratica
     if(parent::ler_variavel('oferendas') != 2) {parent::destruir();}
     else $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'resetar');
     return true;
 }

 function ativar_efeito() {
   if(parent::ler_variavel('ativou_em') == parent::ler_turno()) {parent::avisar('Você já ativou um efeito nessa rodada');return false;}

     $carta = new DB_cards;
     $y = 0;
     $campo_oponente = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
     for($x = 1; $x <= 5; $x++) { // percorre todo o campo incluindo o field
       $carta->ler_id($campo_oponente[$x][1]);
       if($campo_oponente[$x][1] != 0 && $campo_oponente[$x][0] != MODOS::DEFESA_BAIXO) {
           $lista[$y] = $campo_oponente[$x][1];
           $y++;
       }
     }
     
     $lista[$y] = 'divisor';
     $y++;
     
     $campo = $this->duelo->ler_campo($this->dono);
     for($x = 1; $x <= 5; $x++) { // percorre todo o campo
       $carta->ler_id($campo[$x][1]);
       if($campo[$x][1] != 0 && $campo[$x][0] != MODOS::DEFESA_BAIXO) {
           $lista[$y] = $campo[$x][1];
           $y++;
       }
     }

     if(count($lista) <= 1) {
         parent::avisar('Não existem monstros em campo para ativar esse efeito');
         return false;
     }
     $this->duelo->solicitar_carta('CARTAS: OPONENTE | SUAS', $lista, $this->dono, $this->inst);
     return true;
 }
         
function atacar(&$alvo, $lps, $checar = true) {
	 $arq = fopen($this->pasta.'/phase.txt', 'r');
	 $phase = fgets($arq);
	 fclose($arq);
	 if($phase != 4) {parent::avisar('Você não pode atacar fora da BattlePhase'); return 0;}
	 if(parent::ler_turno() <= 1) {parent::avisar('Você não pode atacar neste turno'); return 0;}
         if(parent::ler_variavel('ativou_em') == parent::ler_turno()) {parent::avisar('Você não pode atacar no turno em que ativar o efeito dessa carta'); return 0;}
	 if(parent::ler_variavel('atacou_em') == parent::ler_turno()) {parent::avisar('Você não pode atacar mais de uma vez com o mesmo monstro no mesmo turno'); return 0;}
         if($this->modo != 1) {parent::avisar('Movimento inválido. Você só pode atacar se estiver em modo de ataque'); return 0;}
         if(parent::ler_variavel('equip') == 0) { // se não tiver equipamento
         if($alvo == 'direto_n') {parent::avisar('Você não pode atacar o oponente diretamente se o campo dele não estiver vazio'); return 0;}
         if($alvo == 'direto_s') {
	  $gatilho[0] = 'monstro';
	  $gatilho[1] = 'ataque';
	  $gatilho[2] = 'comum';
	  $gatilho[3] = 'direto';
	  if(parent::checar($gatilho)) {
	   $carta = &parent::checar($gatilho);
	   $resposta = $carta->acionar($gatilho, $this);
	   if($resposta['bloqueado']) {
            parent::manter('atacou_em', parent::ler_turno());
            return false;
           }
          }
          $this->duelo->alterar_lp((-1)*$this->atk, $this->duelo->oponente($this->dono));
          parent::manter('atacou_em', parent::ler_turno()); 
	  parent::avisar($this->nome.' atacou os pontos de vida diretamente', 1);
	  return true;
	 }
	 else {
	  $gatilho[0] = 'monstro';
	  $gatilho[1] = 'ataque';
	  $gatilho[2] = 'comum';
	  $gatilho[3] = 'monstro';
	  $gatilho[4] = $alvo->inst;
	  if(parent::checar($gatilho)) {
	   $carta = parent::checar($gatilho);
	   $resposta = $carta->acionar($gatilho, $this);
	   if($resposta['bloqueado']) {
            parent::manter('atacou_em', parent::ler_turno());
            return false;
           }
          }
	  $ataque['tipo'] = 'c';
          $ataque['atacante'] = $this; //O alvo pode precisar saber quem está atacando
	  $ataque[1] = $this->atk;
	  $resposta = $alvo->atacado($ataque);
	  if($resposta['resultado'] == 'd') {
           parent::manter('atacou_em', parent::ler_turno());
           if($alvo->modo == 1) {parent::destruir();}
           else {
               $tool = new biblioteca_de_efeitos;
               $tool->dano_direto($this->dono, (-1)*$resposta['sobra']);
           }
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' e perdeu', 1);
	   return true;
	  }
            if($resposta['resultado'] == 'D') { // uma derrota, mas deve ser removido do jogo
             parent::manter('atacou_em', parent::ler_turno());
             if($alvo->modo == 1) {parent::remover_do_jogo();}
             else {
                $tool = new biblioteca_de_efeitos;
                $tool->dano_direto($this->dono, (-1)*$resposta['sobra']);
              }
             parent::avisar($this->nome.' atacou '.$alvo->nome.', mas perdeu e foi removido do jogo', 1);
             return true;
            }
	  elseif($resposta['resultado'] == 'e') {
	   if($alvo->modo == 1) {parent::destruir();}
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' e ambos foram destruídos', 1);
	   return true;
	  }
            elseif($resposta['resultado'] == 'E') { // foi um empate, mas deve ser removido
             if($alvo->modo == 1) {parent::remover_do_jogo();}
             parent::avisar($this->nome.' atacou '.$alvo->nome.' e ambos foram destruídos. Essa carta foi removida do jogo', 1);
             return true;
            }
	  elseif($resposta['resultado'] == 'n') {
	   parent::avisar($this->nome.' atacou '.$alvo->nome.', mas nada aconteceu', 1);
	   parent::manter('atacou_em', parent::ler_turno()); 
	   return true;
	  }
	  elseif($resposta['resultado'] == 'v') {
           if($alvo->modo == MODOS::ATAQUE) parent::dano_direto($lps, $resposta['sobra']);
	   parent::avisar($this->nome.' atacou '.$alvo->nome.' que foi destruído', 1);
           parent::manter('atacou_em', parent::ler_turno());
           return true;
	  }
	 }
         return false;
        }
        else {
         $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
         return $equipamento->atacar($alvo, $this, $lps);
        }
}
 
function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   if(parent::ler_variavel('ativou_em') == parent::ler_turno()) // se ativou nesse turno não pode ativar mais
      $comandos_possiveis['ativar'] = false; // mosntro normal não pode ativar efeito
   else
      $comandos_possiveis['ativar'] = true; // mosntro normal não pode ativar efeito
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