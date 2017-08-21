<?php
//terminada dia 28/02/2017
// terminada em 4 dias
/*
 * Esta carta somente pode ser Ritual Summoned com o Ritual Spell Card (Contract with the Dark Master).
 * Uma vez por turno, você pode rolar um dado de seis lados e aplicar 1 dos seguintes efeitos
 * de acordo com os resultados obtidos:
 * é 1 ou 2: Destrua todos os monstros que o seu oponente controla.
 * é 3, 4 ou 5: Destrua 1 monstro que o seu oponente controla.
 * é 6, Destrua todos os monstros que você controla.
 */

class c_177 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'ritual' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito da Contract with the Dark Master');
          parent::destruir();
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    }
    
function ativar_efeito() {
	if(parent::ler_variavel('ativado_em') == parent::ler_turno()) {
		parent::avisar('Esse efeito já foi ativado nesse turno');
		return false;
	}
        
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'destruir';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    
    $dados = $this->duelo->dados();
    parent::avisar('Efeito do monstro '.$this->nome.' ativado. Um dado foi jogado e o resultado foi: '.$dados, 1);
    
    if($dados == 1 || $dados == 2) {
    	 parent::avisar('OBLITERAR NO CAMPO INIMIGO',1);
    	 $this->obliterar();
    	 parent::manter('ativado_em', parent::ler_turno());
    }
    if($dados == 3 || $dados == 4 || $dados == 5) {
    	$ferramenta = new biblioteca_de_efeitos;
        $flag['local'] = 'campo_monstros';
        $flag['duelista'] = $this->duelo->oponente($this->dono);
        $lista = $ferramenta->listar($this->duelo, $flag);
    	if($lista === false) {
    		parent::avisar('Seu oponente não controla monstros');
    		parent::manter('ativado_em', parent::ler_turno());
    		return true;
    	}
    	$this->duelo->solicitar_carta('Escolha um monstro inimigo', $lista, $this->dono, $this->inst);
    	return true;
    }
    if($dados == 6) {
      parent::avisar('OBLITERAR NO PRÓPRIO CAMPO', 1);
    	 parent::manter('ativado_em', parent::ler_turno());
    	 $this->auto_obliterar();
    }
    return true;
}

function carta_solicitada($cartaS) {
	$gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_monstro';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       // checagem feita hora de ativar
    	$ferramenta = new biblioteca_de_efeitos;
        $flag['local'] = 'campo_monstros';
        $flag['duelista'] = $this->duelo->oponente($this->dono);
        $flag['revelar'] = true; // agora deve saber quais sãos os ids sem censura
        $lista = $ferramenta->listar($this->duelo, $flag);
    	if($lista == false || !isset($lista[$cartaS])) {
    		parent::avisar('Falha ao ativar');
    		return false;
    	}
    		
                $campo = $this->duelo->ler_campo($flag['duelista']);
                $campo_filtrado = array();
                $y = 0;
                for($x = 1; $x <= 5; $x++) {
                    $campo_filtrado[$y] = $campo[$x][1];
                    $y++;
                }
                
    		$local = $ferramenta->local_original($lista, $campo_filtrado, $cartaS)+1;
    		    	
    		$alvo = $this->duelo->regenerar_instancia_local($local, $this->duelo->oponente($this->dono));
    		$efeito[0] = 'destruir';
    		$alvo->sofrer_efeito($efeito, $this);
    		    	
    	parent::manter('ativado_em', parent::ler_turno());
    return true;
}

   private function obliterar() {
        $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_monstro';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_especial';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       
            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_monstro';
              $id_oponente = $this->duelo->oponente($this->dono);
            for($x = 1; $x <= 5; $x++) {
             if($campo[$x][1] != 0) {
               $alvo = &$this->duelo->regenerar_instancia_local($x, $id_oponente);
               $alvo->sofrer_efeito($efeito, $this);
             }
            }
            return true;
  }

   private function auto_obliterar() {
        $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_monstro';
        $gatilho[3] = 'self';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_especial';
        $gatilho[3] = 'self';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       
            $campo = $this->duelo->ler_campo($this->dono);
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_monstro';
            for($x = 1; $x <= 5; $x++) {
             if($campo[$x][1] != 0) {
               $alvo = &$this->duelo->regenerar_instancia_local($x, $this->dono);
               $alvo->sofrer_efeito($efeito, $this);
             }
            }
            return true;
  }

function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);

   if(parent::ler_variavel('ativado_em') == parent::ler_turno()) $comandos_possiveis['ativar'] = false;
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