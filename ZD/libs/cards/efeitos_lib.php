<?php
class biblioteca_de_efeitos {
    
    /*
     * Função para causar dano aos pontos de um duelista.
     * Ela não realiza qualquer checagem apenas subtrai o valor passado
     * feita dia 23/02/2017
     */
    function dano_direto($quem, $valor) {
        session_start();
        $lps = 'duelos/'.$_SESSION['id_duelo'].'/'.$quem.'/lps.txt';
        $nlps = file_get_contents($lps);
	$sobra = $nlps - $valor;
	if($sobra < 0) {$sobra = 0;}
        file_put_contents($lps, $sobra);
        return true;
    }

    /*
     * Retorna uma lista de monstros na forma de uma array contendo apenas os id's
     * retorna false em casod e erro
     * as flags passadas determinam como a lista vai se formar e um 'dividor poderá ser incluido'
     * $flags deve conter o local e as restrições
     * $flags['local'] :
     *  campo: vai ler o campo em $flags['duelista'];
     *      $flags['revelar'] se for true vai mostrar as cartas do campo sem censurar as pra baixo
     *  hand: vai ler a mão
     */
    public function listar(&$duelo, $flags) {
      $lista = array();
      $index = 0;
      //se for para ler o campo sem restrições
      if($flags['local'] == 'campo_monstros' && !isset($flags['regra'])) { // ler o campo de monstrs sem regras
        $campo = $duelo->ler_campo($flags['duelista']);
    	for($x=1;$x <= 5;$x++) {
            if($campo[$x][1] !== 0) {
                if($flags['revelar'] === true) $lista[$index] = $campo[$x][1];
                else{
                    if($campo[$x][0] == MODOS::DEFESA_BAIXO) $lista[$index] = 'db';
                    else  $lista[$index] = $campo[$x][1];
                }
                $index++;
            }
    	}
    	if($index == 0) return false;
      }
      elseif($flags['local'] == 'campo_monstros' && isset($flags['regra'])) { // ler o campo com uma regra
        $campo = $duelo->ler_campo($flags['duelista']);
        $regra = true;
        if($flags['regra'] == 'six_samurai') {$regra = 'isSixSamurai';} // ponteiro para a função isSixSamurai
        
    	for($x=1;$x <= 5;$x++) {
            if($campo[$x][1] !== 0 && $regra($campo[$x][1])) {
                if($flags['revelar'] === true) $lista[$index] = $campo[$x][1];
                else{
                    if($campo[$x][0] == MODOS::DEFESA_BAIXO) $lista[$index] = 'db';
                    else  $lista[$index] = $campo[$x][1];
                }
                $index++;
            }
    	}
    	if($index == 0) return false;
      }
      
      if($flags['local'] == 'campo' && !isset($flags['regra'])) { // ler o campo sem regras
        $campo = $duelo->ler_campo($flags['duelista']);
    	for($x=1;$x <= 10;$x++) {
            if($campo[$x][1] !== 0) {
                if($flags['revelar'] === true) $lista[$index] = $campo[$x][1];
                else{
                    if($campo[$x][0] == MODOS::DEFESA_BAIXO) $lista[$index] = 'db';
                    elseif($campo[$x][0] == MODOS::ATAQUE_BAIXO) $lista[$index] = 'ub';
                    else  $lista[$index] = $campo[$x][1];
                }
                $index++;
            }
    	}
    	if($index == 0) return false;
      }elseif($flags['local'] == 'campo' && isset($flags['regra'])) { // ler o campo com uma regra
        $campo = $duelo->ler_campo($flags['duelista']);
        $regra = true;
        if($flags['regra'] == 'six_samurai') {$regra = 'isSixSamurai';} // ponteiro para a função isSixSamurai
        
    	for($x=1;$x <= 10;$x++) {
            if($campo[$x][1] !== 0 && $regra($campo[$x][1])) {
                if($flags['revelar'] === true) $lista[$index] = $campo[$x][1];
                else{
                    if($campo[$x][0] == MODOS::DEFESA_BAIXO) $lista[$index] = 'db';
                    elseif($campo[$x][0] == MODOS::ATAQUE_BAIXO) $lista[$index] = 'ub';
                    else  $lista[$index] = $campo[$x][1];
                }
                $index++;
            }
    	}
    	if($index == 0) return false;
      }
      
      elseif($flags['local'] == 'hand') { // ler a mão
        // formando lista da mão
        $hand = $duelo->ler_mao($flags['duelista']);
        $y = 0;
        for($x = 1; $x < $hand[0]; $x++) {
            $lista[$y] = $hand[$x];
            $y++;
        }
      }
      elseif($flags['local'] == 'cmt' && !isset($flags['regra'])) { // ler o cemitério sem regras de formação
        // formando lista do cemitério
        $hand = $duelo->ler_cmt($flags['duelista']);
        $y = 0;
        for($x = 1; $x < $hand[0]; $x++) {
            $lista[$y] = $hand[$x];
            $y++;
        }
      }
      elseif($flags['local'] == 'cmt' && isset($flags['regra'])) { // ler cemitério obedecendo uma regra de formação
        // formando lista do cemitério
        $cmt = $duelo->ler_cmt($flags['duelista']);
        if($flags['regra'] == 'six_samurai') {$regra = 'isSixSamurai';} // ponteiro para a função isSixSamurai
        $y = 0;
        for($x = 1; $x < $cmt[0]; $x++) {
            if($regra($cmt[$x])) {
                $lista[$y] = $cmt[$x];
                $y++;
            }
        }
      }
      
      return $lista;
    }

    /*
     * Essa função realiza uma operação comum e complexa de encontrar a posição
     * original de uma carta em uma lista de cartas. Quando a lista de cartas é refinada
     * a lista resultanto pede ter elementos repetidos e em posições diferentes
     * $lista é a lista tratada
     * $original é a lista de cartas por onde a $lista foi feita
     * $posicao é o lugar na $lista onde a carta estáss
     */
   public function local_original($lista, $original, $posicao) {
        $repetidas = 0;
        for($x = 0; $x <= $posicao; $x++)
            if($lista[$x] == $lista[$posicao])
    		$repetidas++;
    		    
    	$local = 0;
    	while($repetidas > 0) {
            if((int)$original[$local] === (int)$lista[$posicao]) $repetidas--;
            $local++;
        }
        
        return ($local-1);
    }
    
function apagar_carta_mao($id, $dono, &$duelo) {
    $hand = $duelo->ler_mao($dono);
    for($x = 1; $x <= $hand[0]; $x++) {
        if($id == $hand[$x]) {
            $duelo->apagar_carta_hand($dono, $x);
            return true;
        }
    }
    return false;
}
    
}

//ZONA DE FUNÇÕES ESTÁTICAS
function isSixSamurai($id) {
    switch ((int)$id) {
        case 239:
            return true;
        case 338:
            return true;
        case 343:
            return true;
        case 353:
            return true;
        case 426:
            return true;
        case 427:
            return true;
        case 428:
            return true;
        case 429:
            return true;
        case 725:
            return true;
        case 726:
            return true;
        case 727:
            return true;
        case 728:
            return true;
        case 729:
            return true;
        case 730:
            return true;
    }
    return false;
}
//essa função retorna dado um id se aquele efeito de mágica ou armadilha designa um alvo para destruir(true) ou se é generico (false)
function TRAPouSPELLdesignaAlvo($id) {
    switch ((int)$id) {
        case 5:
            return false;
        case 43:
            return false;
        case 73:
            return false;
        case 100:
            return false;
        case 106:
            return false;
        case 110:
            return false;
        case 171:
            return false;
        case 178:
            return false;
        case 181:
            return false;
        case 194:
            return false;
    }
    return true;
}

?>