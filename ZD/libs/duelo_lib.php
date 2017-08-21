<?php
include("libs/cards_lib.php");
include("libs/cartas_lib.php");

class Duelo {
 var $dir_duelo;
 var $id;
	function __construct($id) {
	 $this->id = $id;
	 $this->dir_duelo = "duelos/$id/";
	}

 function controle_de_turno() {
     if(!file_exists($this->dir_duelo.'metadata.txt')) {return false;}
     if(file_exists($this->dir_duelo.'STOP.txt')) {
         $momento = (int)file_get_contents($this->dir_duelo.'STOP.txt');
         if(time() > $momento)
             @unlink($this->dir_duelo.'STOP.txt');
         else
             return 0;
     }
	session_start();
        $this->engine();
  $grav = new Gravacao();
  $grav->set_caminho($this->dir_duelo.'metadata.txt');
  $matriz = $grav->ler(1);
  unset($grav);
  if($matriz[3][0] == $_SESSION['id']) {
	 if(!file_exists($this->dir_duelo.'tempo_turno.txt')) {
	  $arq = fopen($this->dir_duelo.'tempo_turno.txt', 'w');
	  fwrite($arq, time());
	  fclose($arq);
	  $this->start_phase($_SESSION['id']);
	  return 180;
		}
	 else {
	  $arq = fopen($this->dir_duelo.'tempo_turno.txt', 'r');
	  $t_inicial = fgets($arq);
	  fclose($arq);
	  $t_passado = time() - $t_inicial;
	  $t_restante = 180 - $t_passado;
	  if($t_restante > 0) {return $t_restante;}
	  else {
		 $this->end_phase($_SESSION['id']);
     return 0;
		}
	 }
	}
	elseif(file_exists($this->dir_duelo.'tempo_turno.txt')) {
	  $arq = fopen($this->dir_duelo.'tempo_turno.txt', 'r');
	  $t_inicial = fgets($arq);
	  fclose($arq);
	  $t_passado = time() - $t_inicial;
	  if($t_passado >= 300) {
		 	$arq = fopen($this->dir_duelo.$matriz[3][0].'/lps.txt', 'w');
	   fwrite($arq, '0');
	   fclose($arq);
		}
	}
	else {
	$t_passado = time() - $matriz[1][0];
	 if($t_passado >= 60) {
		 	$arq = fopen($this->dir_duelo.$matriz[3][0].'/lps.txt', 'w');
	   fwrite($arq, '0');
	   fclose($arq);
		}
	}
	return 0;
 }

 public function ler_turno() {
     if(!file_exists($this->dir_duelo.'/metadata.txt')) {return false;}
  $grav = new Gravacao();
  $grav->set_caminho($this->dir_duelo.'metadata.txt');
  $matriz = $grav->ler(1);
  return (int)$matriz[3][1];
 }
 
 function trocar_turno() {
  $grav = new Gravacao();
  $grav->set_caminho($this->dir_duelo.'metadata.txt');
  $matriz = $grav->ler(1);
  if($matriz[2][0] == $matriz[3][0]) {$matriz[3][0] = $matriz[2][1];}
  else {$matriz[3][0] = $matriz[2][0];}
  $matriz[3][1]++;
  $grav->set_matriz($matriz);
  $grav->gravar();
  unset($grav);
	 $arq = fopen($this->dir_duelo.'tempo_turno.txt', 'w');
	 fwrite($arq, time());
	 fclose($arq);
  $this->start_phase($matriz[3][0]);
	}
        
function suspender_duelo($tempo = 20) {file_put_contents($this->dir_duelo.'STOP.txt', time()+$tempo);}
function retomar_duelo() {if(file_exists($this->dir_duelo.'STOP.txt')) @unlink($this->dir_duelo.'STOP.txt');}

         function ler_mao($qual) {
     if(!file_exists($this->dir_duelo.$qual.'/hand.txt')) {return false;}
  if(!(int)$qual) {return 0;}
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$qual.'/hand.txt');
	 $matriz = $grav->ler(1);
	 unset($grav);
	 $array[0] = $matriz[0][0];
	 for($x = 1; $x < $matriz[0][0]; $x++) {
	  $array[$x] = $matriz[$x][0];
	}
	 	return $array;
 }

 function ler_lps($qual) {
	 if(!(int)$qual) {return 0;}
         if(!file_exists($this->dir_duelo.$qual.'/lps.txt')) {return false;}
  $arq = fopen($this->dir_duelo.$qual.'/lps.txt', 'r');
  $lps = fgets($arq);
  fclose($arq);
  return $lps;
 }

 function ler_Ndeck($qual) {
     if(!file_exists($this->dir_duelo.$qual.'/deck.txt')) {return false;}
	 if(!(int)$qual) {return 0;}
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$qual.'/deck.txt');
	 $array = $grav->ler(0);
	 unset($grav);
	 return $array[0] - 1;
	}
 function ler_Nhand($qual) {
     if(!file_exists($this->dir_duelo.$qual.'/hand.txt')) {return false;}
     $hand = $this->ler_mao($qual);
     return (int)$hand[0]-1;
}
        
       function ler_deck($qual) {
           if(!file_exists($this->dir_duelo.$qual.'/deck.txt')) {return false;}
	 if(!(int)$qual) {return 0;}
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$qual.'/deck.txt');
	 $array = $grav->ler(0);
	 unset($grav);
	 return $array;
	}
	
	 function ler_Ncmt($qual) {
             if(!file_exists($this->dir_duelo.$qual.'/cemitery.txt')) {return false;}
		 if(!(int)$qual) {return 0;}
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$qual.'/cemitery.txt');
	 $array = $grav->ler(0);
	 unset($grav);
	 return $array[0] - 1;
	}

	function ler_campo($qual) {
            if(!file_exists($this->dir_duelo.$qual.'/campo.txt')) {return false;}
		if(!(int)$qual) {return 0;}
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
	 $array = $grav->ler(0);
	 $retorno[0][0] = 12;
	 for($x = 1; $x < 12; $x++) {
	 $temp = &$this->regenerar_instancia($array[$x], $qual);
	 if($temp) {
    $retorno[$x][0] = $temp->modo;
    $retorno[$x][1] = $temp->id;
   }
   else { 
    $retorno[$x][0] = 0;
    $retorno[$x][1] = 0;
    }
	 }
	 unset($grav);
	 return $retorno;
	}
        
 function ler_campo_cods($qual) {
     if(!file_exists($this->dir_duelo.$qual.'/campo.txt')) {return false;}
  if(!(int)$qual) {return 0;}
  $grav = new Gravacao();
  $grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
  $array = $grav->ler(0);
  return $array;
 }
       
function alterar_valor($inst, $quem, $nome, $valor) { // incompleto
    if($nome == 'atk') {
        $grav = new Gravacao();
        $grav->set_caminho($this->dir_duelo.$quem.'/'.$inst.'.txt');
	$infos = $grav->ler(0);
        $infos[3] += $valor;
	$grav->set_array($infos);
        $grav->gravar();
        unset($grav);
        return true;
    }
    else {return false;}
}
 
	 function ler_cmt($qual) {
             if(!file_exists($this->dir_duelo.$qual.'/cemitery.txt')) {return false;}
		if(!(int)$qual) {return 0;}
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$qual.'/cemitery.txt');
	 $array = $grav->ler(0);
	 unset($grav);
	 return $array;
	}
	
        function apagar_cmt($id, $quem) {
		if(!(int)$id) {return 0;}
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$quem.'/cemitery.txt');
	 $array = $grav->ler(0);
         $grav->set_array($array);
         for($x = 1; $x < $array[0]; $x++) {
          if($array[$x] == $id) {
           $grav->apagar($x);
           break;
          }
         }
         $grav->gravar();
	 unset($grav);
	}
        
	function oponente($quem) {
  if(!(int)$quem) {return 0;}
  $grav = new Gravacao();
  $grav->set_caminho($this->dir_duelo.'metadata.txt');
  $matriz = $grav->ler(1);
  if($matriz[2][0] == $quem) {$id = $matriz[2][1];}
  else {$id = $matriz[2][0];}
  unset($grav);
  return $id;
	}
	
	function local_id($local, $posicao) {
	if(!(int)$posicao) {return 0;}
	if((int)$local == 0) {
		$array = $this->ler_mao($_SESSION['id']);
		return $array[$posicao];
		}
	if((int)$local == 1) {
		 $array = $this->ler_campo($_SESSION['id']);
		if($posicao < 12) {return $array[$posicao][1];}
		else {
		 $array = $this->ler_campo($this->oponente($_SESSION['id']));
		 return $array[11][1];
			}
		}
	if((int)$local == 2) {
		$array = $this->ler_campo($this->oponente($_SESSION['id']));
		if($posicao < 11) {
                if($array[$posicao][0] > 2) {return 0;} // se estiver virada pra baixo não mostre informações
		 return $array[$posicao][1];
		}
		else {return 0;}
		}
	if((int)$local == 3) {
		$array = $this->ler_cmt($_SESSION['id']);
		return $array[$posicao];
		}
	if((int)$local == 4) {
		$array = $this->ler_cmt($this->oponente($_SESSION['id']));
		return $array[$posicao];
		}
		return 0;
	}
	
	function invocar($qual, $local, $modo, $id, $tipo = 'comum', $flags = false) {
            $id = (int)$id;
            $qual = (int)$qual;
            $local = (int)$local;
            $modo = (int)$modo;
	 $grav = new Gravacao();
         $grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
	 $campo = $grav->ler(0);
	 if($local != 11 && $campo[$local]) {return 0;}
         unset($campo);
	 	$card = new DB_cards();
	  $card->ler_id($id);
   	if(($card->tipo == 'normal' || $card->tipo == 'fusion') && $card->categoria == 'monster') {
	    if($local > 5) {return 0;}
	    $instancia = new Monstro_normal($this, $this->dir_duelo.$qual.'/');
	    $instancia->invocar($local, $id, $modo, $qual, $tipo, $flags);
	}
	  else {
	    if($card->categoria == 'spell' || $card->categoria == 'trap') {
                if($local <= 5) {return 0;}
                if($local == 11) {
                    switch ($card->id) { // caso a carta seja um field então tudo bem
                        case 119:
                            break;
                        default :
                            return 0; // se não então não pode invocar
                    }
                }
	    }
            $c = 'c_'.$id;
            $instancia = new $c($this, $this->dir_duelo.$qual.'/');
            $instancia->invocar($local, $id, $modo, $qual, $tipo, $flags);
	 }
	if(!$instancia->inst) {return 0;}
	unset($instancia);
	return 1;
	}
	
 function assumir_field() { // esse carta limpa o field caso consiga avisa que está tudo ok
     session_start();
    $campo = $this->ler_campo($_SESSION['id']);
    $campo_oponente = $this->ler_campo($this->oponente($_SESSION['id']));
    if($campo[11][1]) {
        $instancia = $this->regenerar_instancia_local(11, $_SESSION['id']);
        if(!$instancia->destruir()) return false;
        $this->colocar_no_cemiterio($instancia->id, $_SESSION['id']);
    }
    elseif($campo_oponente[11][1]) {
        $instancia = $this->regenerar_instancia_local(11, $this->oponente($_SESSION['id']));
        if(!$instancia->destruir()) return false;
        $this->colocar_no_cemiterio($instancia->id, $this->oponente($_SESSION['id']));
    }
    return true;
 }
        
	function mudar_modo($qual, $local, $modo) {
		if($local > 10) {return 0;}
	 $grav = new Gravacao();
	 	$grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
	 $campo = $grav->ler(0);
	 	unset($grav);
	 if(!$campo[$local]) {return 0;}
  $instancia = &$this->regenerar_instancia($campo[$local], $qual);
	if(!$instancia->inst) {return 0;}
	if(!$instancia->mudar_modo($modo)) {return 0;}
	unset($instancia);
	return 1;
	}
	
	function sacrificar($qual, $local) {
		if($local > 5) {return 0;}
	 $grav = new Gravacao();
	 	$grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
	 $campo = $grav->ler(0);
	 unset($grav);
	 if(!$campo[$local]) {return 0;}
  $instancia = &$this->regenerar_instancia($campo[$local], $qual);
	if(!$instancia->inst) {return 0;}
	if(!$instancia->sacrificar()) {return 0;}
	unset($instancia);
	return 1;
	}
	
function atacar($qual, $local, $alvo) {
 if($local > 5) {return 0;}
 if($alvo > 5 && $alvo != 'd') {return 0;}
 $grav = new Gravacao();
 $grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
 $campo = $grav->ler(0);
 if(!$campo[$local]) {return 0;}
 $instancia = &$this->regenerar_instancia($campo[$local], $qual);
 if(!$instancia->inst) {return 0;}

 $grav2 = new Gravacao();
 $grav2->set_caminho($this->dir_duelo.$this->oponente($qual).'/campo.txt');
 $campo2 = $grav2->ler(0);
 if($alvo == 'd') {
  $alvo = 'direto_s';
  for($x = 1; $x <= 5; $x++) {if($campo2[$x]) {$alvo = 'direto_n'; break;}}
  $resultado = $instancia->atacar($alvo, $this->dir_duelo.$this->oponente($qual).'/lps.txt');
 }
 else {
  if(!$campo2[$alvo]) {return 0;}
  $alvo = &$this->regenerar_instancia($campo2[$alvo], $this->oponente($qual));
  if(!$alvo->inst) {return 0;}
  $resultado = $instancia->atacar($alvo, $this->dir_duelo.$this->oponente($qual).'/lps.txt');
 }
 unset($grav);
 unset($grav2);
 unset($instancia);
 unset($alvo);
 return $resultado;
}

function acts_card($qual, $local) { // retorna o que a carta pode fazer
 if($local > 5) {return false;}
 $grav = new Gravacao();
 $grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
 $campo = $grav->ler(0);
 if(!$campo[$local]) {return false;}
 $instancia = &$this->regenerar_instancia($campo[$local], $qual);
 if(!$instancia->inst) {return false;}
 return $instancia->Comandos();
}

 	function ler_phase($quem) {
            if(!file_exists($this->dir_duelo.$quem.'/phase.txt')) {return 0;}
  if(!(int)$quem) {return 0;}
  if(!file_exists($this->dir_duelo.(int)$quem.'/phase.txt')) {return 0;}
	$arq = fopen($this->dir_duelo.(int)$quem.'/phase.txt', 'r');
	$retorno = fgets($arq);
	fclose($arq);
  return $retorno;
	}
        function puxar_carta($quem) {
         $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.(int)$quem.'/deck.txt');
	 $array = $grav->ler(0);
         
         if($array[0] < 2) {return 'vazio';}
         
	 $nova_carta = $array[1];
	 $grav->set_array($array);
	 $grav->apagar(1);
	 $grav->gravar();
	 unset($grav);
         
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.(int)$quem.'/hand.txt');
	 $hand = $grav->ler(0);
	 if($hand[0] >= 8) {
	  $grav->set_array($hand);
	  $grav->apagar(1);
	  $grav->gravar();
	  $hand = $grav->ler(0);
	  $hand[7] = $nova_carta;
	  $hand[0] = 8;
	  $grav->set_array($hand);
	  $grav->gravar();
	 }
	 else {
	  $hand[$hand[0]] = $nova_carta;
	  $hand[0]++;
	  $grav->set_array($hand);
	  $grav->gravar();
	 }
         return true;
        }
        
        function colocar_carta_hand($id, $quem) {    
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.(int)$quem.'/hand.txt');
	 $hand = $grav->ler(0);
	 if($hand[0] >= 8) {
	  $grav->set_array($hand);
	  $grav->apagar(1);
	  $grav->gravar();
	  $hand = $grav->ler(0);
	  $hand[7] = $id;
	  $hand[0] = 8;
	  $grav->set_array($hand);
	  $grav->gravar();
	 }
	 else {
	  $hand[$hand[0]] = $id;
	  $hand[0]++;
	  $grav->set_array($hand);
	  $grav->gravar();
	 }
         return true;
        } 
       
	function start_phase($quem) {
         $this->limpar_engine();
         $this->verificar_tarefa($quem, 'start_phase');
	$grav = new Gravacao();
	$grav->set_caminho($this->dir_duelo.'metadata.txt');
	$matriz = $grav->ler(1);
	unset($grav);
	
	if($matriz[3][1] > 2) {
         if($this->puxar_carta($quem) === 'vazio')  {
           $arq = fopen($this->dir_duelo.$quem.'/lps.txt', 'w');
	   fwrite($arq, '0');
	   fclose($arq);
           return false;
         }
	}
	
	$arq = fopen($this->dir_duelo.(int)$quem.'/phase.txt', 'w');
	fwrite($arq, '2');
	fclose($arq);
	$this->sb_phase($quem);
	}
	
	function sb_phase($quem) {
         if(!(int)$quem) {return 0;}
         $this->verificar_tarefa($quem, 'sb_phase');
	 $arq = fopen($this->dir_duelo.(int)$quem.'/phase.txt', 'w');
	 fwrite($arq, '3');
	 fclose($arq);
	 $this->m1_phase($quem);
	}
	
 function m1_phase($quem, $proximo = 0) {
  if(!(int)$quem) {return 0;}
  if($proximo) {
    $arq = fopen($this->dir_duelo.(int)$quem.'/phase.txt', 'w');
    fwrite($arq, '4');
    fclose($arq);
    $this->battle_phase($quem);
  }
	else {
            $this->verificar_tarefa($quem, 'm1_phase');
            return 0;
        }
 }
	
    function battle_phase($quem, $proximo = 0) {
  if(!(int)$quem) {return 0;}
  if($proximo) {
   $arq = fopen($this->dir_duelo.(int)$quem.'/phase.txt', 'w');
   fwrite($arq, '5');
   fclose($arq);
    $this->m2_phase($quem);
	 }
	else {
         $this->verificar_tarefa($quem, 'battle_phase');
         return 0;
        }
    }
	
 function m2_phase($quem, $proximo = 0) {
  if(!(int)$quem) {return 0;}
  if($proximo) {
	$arq = fopen($this->dir_duelo.(int)$quem.'/phase.txt', 'w');
	fwrite($arq, '6');
	fclose($arq);
	 $this->end_phase($quem);
	 }
	else {
            $this->verificar_tarefa($quem, 'm2_phase');
            return 0;
        }
 }
	
	function end_phase($quem) {
          $this->verificar_tarefa($quem, 'end_phase');
	$arq = fopen($this->dir_duelo.(int)$quem.'/phase.txt', 'w');
	fwrite($arq, '0');
	fclose($arq);
	$arq = fopen($this->dir_duelo.(int)$quem.'/sacrificios.txt', 'w');
	fwrite($arq, '0');
	fclose($arq);
        @unlink($this->dir_duelo.(int)$quem.'/m_invocado.txt');
        @unlink($this->dir_duelo.(int)$quem.'/card_solicitado.txt');
	$this->trocar_turno();
	}
	
        function apagar_carta_hand($quem, $local) {
         $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$quem."/hand.txt");
	 $array = $grav->ler(0);
	 $grav->set_array($array);
         if(!$array[$local]) {return false;}
	 $grav->apagar($local);
 	 $grav->gravar();
	 unset($grav);
         return true;
        }

        function apagar_carta_deck($quem, $local) {
         $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$quem."/deck.txt");
	 $array = $grav->ler(0);
	 $grav->set_array($array);
         if(!$array[$local]) {return false;}
	 $grav->apagar($local);
 	 $grav->gravar();
	 unset($grav);
         return true;
        }

        function colocar_no_cemiterio($id, $qual) {
         if(!(int)$id || !(int)$qual) {return false;}
         $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$qual.'/cemitery.txt');
	 $cmt = $grav->ler(0);
	 $cmt[$cmt[0]] = $id;
	 $cmt[0]++;
	 $grav->set_array($cmt);
	 $grav->gravar();
	 unset($grav);
         return true;
        }
        
        function colocar_no_deck($id, $qual) {
         if(!(int)$id || !(int)$qual) {return false;}
         $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$qual.'/deck.txt');
	 $cmt = $grav->ler(0);
	 $cmt[$cmt[0]] = $id;
	 $cmt[0]++;
	 $grav->set_array($cmt);
	 $grav->gravar();
	 unset($grav);
         return true;
        }
       
        function embaralhar_deck($id, $qual) {
            if(!(int)$id || !(int)$qual) {return false;}
            $grav = new Gravacao();
            $grav->set_caminho($this->dir_duelo.$qual.'/deck.txt');
            $array = $grav->ler(0);
            
            $temp[0] = '';
            for($x = 1; $x < $array[0]; $x++) {$temp[$x - 1] = $array[$x];}
            shuffle($temp);
            for($x = 1; $x < $array[0]; $x++) {$array[$x] = $temp[$x - 1];}
            
            $grav->set_array($array);
            $grav->gravar();
            unset($grav);
            return true;
        }
                
        function alterar_lp($quanto, $quem) {
          $arq = fopen($this->dir_duelo.$quem.'/lps.txt', 'r');
	  $nlps = fgets($arq);
	  fclose($arq);
	  $sobra = $nlps + $quanto;
	  if($sobra < 0) {$sobra = 0;}
	  $arq = fopen($this->dir_duelo.$quem.'/lps.txt', 'w');
	  fwrite($arq, $sobra);
	  fclose($arq);
	  return true;
        }
        
        function ativar_efeito($qual, $local) {
         if($local > 10) {return 0;}
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
	 $campo = $grav->ler(0);
	 unset($grav);
	 if(!$campo[$local]) {return 0;}
         $instancia = &$this->regenerar_instancia($campo[$local], $qual);
	 if(!$instancia->inst) {return 0;}
	 if(!$instancia->ativar_efeito()) {return 0;}
	 unset($instancia);
	 return 1;
        }
        
        function moeda() { // simula o arremesso de uma moeda
            if((int)rand(1, 2) == 1) {return true;}
            return false;
        }
        function dados() { // simula o arremesso de um dado de 6 lados
            return rand(1, 6);
        }
        
        function solicitar_carta($txt, $lista, $qual, $responder) {
           $string = $txt."\n";
          for($x = 0;$x < count($lista); $x++) {$string .= $lista[$x]."\n";}
          file_put_contents($this->dir_duelo.$qual.'/logc.txt', (time()+20)."\n".substr($string, 0, -1));
          file_put_contents($this->dir_duelo.$qual.'/card_solicitado.txt', $responder);
        }
        
        function responder_carta($carta, $qual) {
            if(!file_exists($this->dir_duelo.$qual.'/card_solicitado.txt')) {
                @unlink($this->dir_duelo.$qual.'/logc.txt');
                return false;
            }
            $cartaS = $this->regenerar_instancia(file_get_contents($this->dir_duelo.$qual.'/card_solicitado.txt'), $qual);
            @unlink($this->dir_duelo.$qual.'/card_solicitado.txt');
            @unlink($this->dir_duelo.$qual.'/logc.txt');
            $cartaS->carta_solicitada((int)$carta);
            return true;
        }
        
        function &regenerar_instancia_local($local, $qual) {
          $grav = new Gravacao();
	  $grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
	  $campo = $grav->ler(0);
	  if(!$campo[$local]) {return false;}
          $instancia = &$this->regenerar_instancia($campo[$local], $qual);
	  if(!$instancia->inst) {return false;}
          return $instancia;
        }
        
        function agendar_tarefa($inst, $dono, $quando, $texto, $script = false) {
         if(!file_exists($this->dir_duelo.$dono."/cron.txt")) {fclose(fopen($this->dir_duelo.$dono."/cron.txt", 'w'));}
         $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$dono."/cron.txt");
	 $matriz = $grav->ler(1);
         $matriz[$matriz[0][0]][0] = $inst;
         $matriz[$matriz[0][0]][1] = $quando;
         $matriz[$matriz[0][0]][2] = $texto;
         if($script) {
             $matriz[$matriz[0][0]][3] = 'script';
             $matriz[0][$matriz[0][0]] = 4;
         }
         else {$matriz[0][$matriz[0][0]] = 3;}
         $matriz[0][0]++;
         $grav->set_matriz($matriz);
         $grav->gravar();
	 unset($grav);
         return true;
        }
        
        function verificar_tarefa($dono, $quando) {
         if(!file_exists($this->dir_duelo.$dono."/cron.txt")) {return false;}
        $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$dono."/cron.txt");
	 $matriz = $grav->ler(1);
         unset($grav);
         for($x = 1;$x < $matriz[0][0];$x++) {
            if($matriz[$x][1] == $quando) {
              if(!file_exists($this->dir_duelo.$dono.'/'.$matriz[$x][0].'.txt') && !file_exists($this->dir_duelo.$dono.'/'.$matriz[$x][0])) {$this->apagar_tarefa($dono, $quando, $matriz[$x][0]);}
                else {
                  if($matriz[$x][3] !== 'script') {
                   $inst = &$this->regenerar_instancia($matriz[$x][0], $dono);
                   $inst->tarefa($matriz[$x][2]);
                  }
                  else {
                      $codigo = file_get_contents($this->dir_duelo.$dono.'/'.$matriz[$x][0]);
                      eval($codigo); // executando o que estiver no arquivo
                 }
                }
             }
         }
         return true;
        }
        
        function apagar_tarefa($dono, $quando, $inst) {
         if(!file_exists($this->dir_duelo.$dono."/cron.txt")) {fclose(fopen($this->dir_duelo.$dono."/cron.txt", 'w'));}
         $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$dono."/cron.txt");
	 $matriz = $grav->ler(1);
         unset($grav);
         for($x = 1;$x < $matriz[0][0];$x++) {
             if($matriz[$x][1] == $quando && $matriz[$x][0] == $inst) {
               if($matriz[$x][3] === 'script') {@unlink($this->dir_duelo.$dono."/".$matriz[$x][0]);}
              $grav = new Gravacao();
	      $grav->set_caminho($this->dir_duelo.$dono."/cron.txt");
	      $matriz = $grav->ler(0);
              $grav->set_array($matriz);
              $grav->apagar($x);
              $grav->gravar();
              unset($grav);
              break;
             }
         }
        }
        
        function get_atk($qual, $local) {
          $local = (int) $local;
          if($local > 5 || $local < 1) {return 0;}
	  $grav = new Gravacao();
	  $grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
	  $campo = $grav->ler(0);
	  if(!$campo[$local]) {return 0;}
          $instancia = &$this->regenerar_instancia($campo[$local], $qual);
	  if(!$instancia->inst) {return 0;}
          return $instancia->get_atk();
        }
        
        function get_def($qual, $local) {
          $local = (int) $local;
          if($local > 5 || $local < 1) {return 0;}
	  $grav = new Gravacao();
	  $grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
	  $campo = $grav->ler(0);
	  if(!$campo[$local]) {return 0;}
          $instancia = &$this->regenerar_instancia($campo[$local], $qual);
	  if(!$instancia->inst) {return 0;}
          return $instancia->get_def();
        }
        
        function get_lv($qual, $local) {
          $local = (int) $local;
          if($local > 5 || $local < 1) {return 0;}
	  $grav = new Gravacao();
	  $grav->set_caminho($this->dir_duelo.$qual.'/campo.txt');
	  $campo = $grav->ler(0);
	  if(!$campo[$local]) {return 0;}
          $instancia = &$this->regenerar_instancia($campo[$local], $qual);
	  if(!$instancia->inst) {return 0;}
          return $instancia->get_lv();
        }

function set_engine($instancia, $dono = (-1)) {
    if($dono === -1) $dono = $_SESSION['id'];
     if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/'.$dono.'/engine.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/'.$dono.'/engine.txt', 'w'));}
     $grav = new Gravacao();
     $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/'.$dono.'/engine.txt');
     $matriz = $grav->ler(0);
     $matriz[$matriz[0]] = $instancia;
     $matriz[0]++;
     $grav->set_array($matriz);
     $grav->gravar();
     unset($grav);
}
        
function engine() {
     if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/engine.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/engine.txt', 'w'));}
     $bruto = file_get_contents('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/engine.txt');
     if($bruto == '') return false;
     $matriz = explode("\n", $bruto);
     for($i = 0; $i < count($matriz); $i++) {
      if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/'.$matriz[$i].'.txt')) {$this->limpar_engine();return false;}
      else {
          if(substr($matriz[$i], 0, 7) == 'script_') {
              eval(file_get_contents('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/'.$matriz[$i].'.txt'));   
          }
          else {
            $carta = $this->regenerar_instancia($matriz[$i], $_SESSION['id']);
            $carta->engine();
            unset($carta);
          }
      }
     }
     return true;
}
function limpar_engine() {
    if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/engine.txt')) {fclose(fopen('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/engine.txt', 'w'));}
     $grav = new Gravacao();
     $grav->set_caminho('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/engine.txt');
     $matriz = $grav->ler(0);
     $grav->set_array($matriz);
     for($i = 1; $i < $matriz[0]; $i++) {
       if(!file_exists('duelos/'.$_SESSION['id_duelo'].'/'.$_SESSION['id'].'/'.$matriz[$i].'.txt')) {
        $grav->apagar($i);
       }
     }
     $grav->gravar();
     unset($grav);
}
                function &regenerar_instancia($inst, $quem) {
		$quem = (int)$quem;
		if(!$inst) {return 0;}
	 $grav = new Gravacao();
	 $grav->set_caminho($this->dir_duelo.$quem."/$inst.txt");
	 $array = $grav->ler(0);
	 unset($grav);
	if($array[0] > 6) {$id = $array[7];}
	else {$id = $array[3];}
	$card = new DB_cards();
	$card->ler_id($id);
	if(($card->tipo == 'normal' || $card->tipo == 'fusion') && $card->categoria == 'monster') {
		$instancia = new Monstro_normal($this, $this->dir_duelo.$quem.'/', $inst);
		}
	else {
	    $c = 'c_'.$card->id;
            $instancia = new $c($this, $this->dir_duelo.$quem.'/', $inst);
	 }
	return $instancia;
	}
}

class Inbox {
    var $local_file;
    
    function __construct($id_duelo) {
        $this->local_file = 'duelos/'.$id_duelo.'/chat.txt';
        if(!file_exists($this->local_file))
            file_put_contents($this->local_file, json_encode(array()));
    }
    
    function enviar($quem, $msg) {
        $mensagem = array();
        $mensagem['quem'] = (int)$quem;
        $mensagem['msg'] = utf8_encode(htmlentities(mb_substr($msg, 0, 300)));
        
        $mensagens = $this->read_file();
        $mensagens[count($mensagens)] = $mensagem;
        file_put_contents($this->local_file, json_encode($mensagens));
        
        return true;
    }

    function read_file() {return json_decode(file_get_contents($this->local_file), true);}
}

?>