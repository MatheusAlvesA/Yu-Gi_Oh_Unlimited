<?php
class Carta {

	protected function ler_turno() {
	 $grav = new Gravacao();
  $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/metadata.txt');
  $matriz = $grav->ler(1);
  unset($grav);
  return $matriz[3][1];
	}
	
 protected function manter($nome, $valor) {
   if(!$nome) {return 0;}
   $grav = new Gravacao();
   $grav->set_caminho($this->pasta.'_'.$this->inst.'.txt');
   $variaveis = $grav->ler(1);
   for($x = 1; $x < $variaveis[0][0]; $x++) {
    if($nome == $variaveis[$x][0]) {
     $variaveis[$x][1] = $valor;
     $grav->set_matriz($variaveis);
     $grav->gravar();
     unset($grav); 
     return 1;
    }
   }
	 $variaveis[$variaveis[0][0]][0] = $nome;
	 $variaveis[$variaveis[0][0]][1] = $valor;
	 $variaveis[0][$variaveis[0][0]] = 2;
	 $variaveis[0][0]++;
	 $grav->set_matriz($variaveis);
	 $grav->gravar();
	 unset($grav); 
	 return 1;
	}
 protected function ler_variavel($nome) {
	 $grav = new Gravacao();
	 $grav->set_caminho($this->pasta.'_'.$this->inst.'.txt');
	 $variaveis = $grav->ler(1);
   unset($grav);
   for($x = 1; $x < $variaveis[0][0]; $x++) {
    if($nome == $variaveis[$x][0]) {return $variaveis[$x][1];}
   }
   return false;
	}
  protected function avisar($msg, $geral = 0) {
   $grav = new Gravacao();
   if(!$geral) {
    if(!file_exists($this->pasta.'/log.txt')) {
      fclose(fopen($this->pasta.'/log.txt', 'w'));
    }
    $grav->set_caminho($this->pasta.'/log.txt');
    $array = $grav->ler(0);
    $array[$array[0]] = $msg;
    $array[0]++;
    $grav->set_array($array);
    $grav->gravar();
    unset($grav);
   }
  	else {
	 if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/log.txt')) {
	 $arq = fopen('duelos/'.$_SESSION['id_duelo'].'/log.txt', 'w');
         fwrite($arq, "0\n");
         fclose($arq);
	}
    $arq = fopen('duelos/'.$_SESSION['id_duelo'].'/log.txt', 'a');
    fwrite($arq, $msg."\n");
    fclose($arq);
   }
	}
	
	function dano_direto($lps, $valor, $motivo = 'sobra') {
    $nlps = file_get_contents($lps);
	  $sobra = $nlps - $valor;
	  if($sobra < 0) {$sobra = 0;}
	  $arq = fopen($lps, 'w');
	  fwrite($arq, $sobra);
	  fclose($arq);
	}

function &checar($gatilho) {
    if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt', 'w'));}
    $grav = new Gravacao();
    $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt');
    $matriz = $grav->ler(1);
    unset($grav);
    
    $remover = array();
    $rm = 0;
    for($i = 1; $i < $matriz[0][0]; $i++) {
        for($j = 0; $j < $matriz[0][$i] - 1; $j++) {
            if($gatilho[$j] != $matriz[$i][$j] && $matriz[$i][$j] != 'tudo') {break;}
        }
        if($j >= $matriz[0][$i] - 1) {
            $inst = explode('-', end($matriz[$i]));
            if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/'.$inst[0].'/'.$inst[1].'.txt')) { // o gatilho não tem dono
                $remover[$rm] = $inst[1];
                $rm++;
            }
            else { // o gatilho é compatível e tem dono
                if($inst[0] == $this->dono && $inst[2] == 'self') {return $this->duelo->regenerar_instancia($inst[1], $inst[0]);}
                elseif($inst[0] != $this->dono && $inst[2] == 'oponente') {return $this->duelo->regenerar_instancia($inst[1], $inst[0]);}
                elseif($inst[2] == 'ambos') {return $this->duelo->regenerar_instancia($inst[1], $inst[0]);}
            }
        }
    }
    
    for($x = 0; $x < count($remover);$x++) { // deletando todos os gatilhos vencidos
        $this->out_gatilho_inst($remover[$x]);
    }
    return false;
}
function &checar_opcionais($gatilho) {
    if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt', 'w'));}
    $grav = new Gravacao();
    $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt');
    $matriz = $grav->ler(1);
    unset($grav);
    
    $remover = array();
    $rm = 0;
    for($i = 1; $i < $matriz[0][0]; $i++) {
        for($j = 0; $j < $matriz[0][$i] - 1; $j++) {
            if($gatilho[$j] != $matriz[$i][$j] && $matriz[$i][$j] != 'tudo') {break;}
        }
        if($j >= $matriz[0][$i] - 1) {
            $inst = explode('-', end($matriz[$i]));
            if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/'.$inst[0].'/'.$inst[1].'.txt')) { // o gatilho não tem dono
                $remover[$rm] = $inst[1];
                $rm++;
            }
            else { // o gatilho é compatível e tem dono
                if($inst[0] == $this->dono && $inst[2] == 'self') {return $this->duelo->regenerar_instancia($inst[1], $inst[0]);}
                elseif($inst[0] != $this->dono && $inst[2] == 'oponente') {return $this->duelo->regenerar_instancia($inst[1], $inst[0]);}
                elseif($inst[2] == 'ambos') {return $this->duelo->regenerar_instancia($inst[1], $inst[0]);}
            }
        }
    }
    
    for($x = 0; $x < count($remover);$x++) { // deletando todos os gatilhos vencidos
        $this->out_gatilho_opcional_inst($remover[$x]);
    }
    return false;
}

function set_gatilho($gatilho) {
    if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt', 'w'));}
    $grav = new Gravacao();
    $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt');
    $matriz = $grav->ler(1);
    $matriz[$matriz[0][0]] = $gatilho;
    $matriz[0][$matriz[0][0]] = count($gatilho);
    $matriz[0][0]++;
    $grav->set_matriz($matriz);
    $grav->gravar();
    unset($grav);
}
function set_gatilho_opcional($gatilho) {
    if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt', 'w'));}
    $grav = new Gravacao();
    $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt');
    $matriz = $grav->ler(1);
    $matriz[$matriz[0][0]] = $gatilho;
    $matriz[0][$matriz[0][0]] = count($gatilho);
    $matriz[0][0]++;
    $grav->set_matriz($matriz);
    $grav->gravar();
    unset($grav);
}
	
function out_gatilho($n = false) {
    if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt', 'w'));}
    $grav = new Gravacao();
    $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt');
    $array = $grav->ler(0);
    $grav->set_array($array);
    if($n) {$grav->apagar($n);}
    else {
        foreach($array as $key => $valor) {
            $temp = explode('-', end(explode(';', $valor)));
            if($temp[1] == $this->inst) {$grav->apagar($key);}
        }
    }
    $grav->gravar();
    unset($grav);
    return true;
}
function out_gatilho_opcional($n = false) {
    if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt', 'w'));}
    $grav = new Gravacao();
    $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt');
    $array = $grav->ler(0);
    $grav->set_array($array);
    if($n) {$grav->apagar($n);}
    else {
        foreach($array as $key => $valor) {
            $temp = explode('-', end(explode(';', $valor)));
            if($temp[1] == $this->inst) {$grav->apagar($key);}
        }
    }
    $grav->gravar();
    unset($grav);
    return true;
}

function out_gatilho_inst($instancia) {
    if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt', 'w'));}

    $grav = new Gravacao();
  $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/gatilhos.txt');
  $array = $grav->ler(0);
  $grav->set_array($array);
  
  foreach($array as $key => $valor) {
    $temp = explode('-', end(explode(';', $valor)));
    if($temp[1] == $instancia) {$grav->apagar($key);break;}
  }
  
  $grav->gravar();
  unset($grav);
  return true;
}
function out_gatilho_opcional_inst($instancia) {
    if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt', 'w'));}

    $grav = new Gravacao();
  $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/gatilhos_opcionais.txt');
  $array = $grav->ler(0);
  $grav->set_array($array);
  
  foreach($array as $key => $valor) {
    $temp = explode('-', end(explode(';', $valor)));
    if($temp[1] == $instancia) {$grav->apagar($key);break;}
  }
  
  $grav->gravar();
  unset($grav);
  return true;
}
        
 protected function destruir($motivo = 0) {
  if(!file_exists($this->pasta.$this->inst.'.txt') || $this->ler_variavel('travar_destruir') != 0) {return false;}
  if($this->ler_variavel('equip') != 0) {
      $equipamento = &$this->duelo->regenerar_instancia($this->ler_variavel('equip'), $this->dono);
      $equipamento->monstro_destruido();
  }
  @unlink($this->pasta.$this->inst.'.txt');
  @unlink($this->pasta.'_'.$this->inst.'.txt');
  
  if($motivo !== 'falha_na_invocação') {
    $grav = new Gravacao();
	$grav->set_caminho($this->pasta.'cemitery.txt');
	$cmt = $grav->ler(0);
	$cmt[$cmt[0]] = $this->id;
	$cmt[0]++;
	$grav->set_array($cmt);
	$grav->gravar();
    unset($grav);
  }
	$grav = new Gravacao();
	$grav->set_caminho($this->pasta.'/campo.txt');
	$campo = $grav->ler(0);
	for($x = 1; $campo[$x] != $this->inst && $x <= 11; $x++) {}
  if($x > 11) {return 0;}
  $campo[$x] = 0;
	$grav->set_array($campo);
	$grav->gravar();
	unset($grav);
	return true;
 }
 
  protected function remover_do_jogo($motivo = 0) {
     if(!file_exists($this->pasta.$this->inst.'.txt') || $this->ler_variavel('travar_destruir') != 0) {return false;}
  if($this->ler_variavel('equip') != 0) {
      $equipamento = &$this->duelo->regenerar_instancia($this->ler_variavel('equip'), $this->dono);
      $equipamento->monstro_destruido();
  }
  @unlink($this->pasta.$this->inst.'.txt');
  @unlink($this->pasta.'_'.$this->inst.'.txt');
	$grav = new Gravacao();
	$grav->set_caminho($this->pasta.'/campo.txt');
	$campo = $grav->ler(0);
	for($x = 1; $campo[$x] != $this->inst && $x <= 11; $x++) {}
  if($x > 11) {return false;}
  $campo[$x] = 0;
	$grav->set_array($campo);
	$grav->gravar();
	unset($grav);
	return true;
 }
 
  // efeitos usados em cartas especiais
     function get_carta_hand($tipo, $qual = false, $modo = false) { //incompleta
     if(!$qual) {$qual = $this->dono;}
     $cards = $this->duelo->ler_mao($qual);
     if($cards[0] <= 1) {return false;}
     $card = new DB_cards();
     $y = 0;
     if($tipo == 'monster') {
      for($x = 1; $x < $cards[0]; $x++) {
	$card->ler_id($cards[$x]);
        if($card->categoria == 'monster') {$final[$y] = $cards[$x]; $y++;}
      }
     }
     elseif($tipo == 'qualquer') {
        for($x = 1; $x < $cards[0]; $x++) {$final[$x-1] = $cards[$x];}
     }
     if(!isset($final)) {return false;}
     if(!$modo) {
      $chave = array_rand($final);
      return $final[$chave];
     }
    }
    
  function excluir_carta_hand($modo, $qual, $duelista = true) { // incompleta
      if($duelista) {
       if($modo == 'posicao') {
          $this->duelo->apagar_carta_hand($this->dono, $qual);
       }
       else {
           $hand = $this->duelo->ler_mao($this->dono);
           for($x = 1; $x < $hand[0] && $qual != $hand[$x]; $x++) {}
           if($x >= $hand[0]) {return false;}
           $this->duelo->apagar_carta_hand($this->dono, $x);
       }
      }
      return true;
  }

  
    function excluir_carta_deck($modo, $qual, $duelista = true) { // incompleta
      if($duelista) {
       if($modo == 'posicao') {
          $this->duelo->apagar_carta_deck($this->dono, $qual);
       }
       else {
           $deck = $this->duelo->ler_deck($this->dono);
           for($x = 1; $x < $deck[0] && $qual != $deck[$x]; $x++) {}
           if($x >= $deck[0]) {return false;}
           $this->duelo->apagar_carta_deck($this->dono, $x);
       }
      }
      return true;
  }
}
?>