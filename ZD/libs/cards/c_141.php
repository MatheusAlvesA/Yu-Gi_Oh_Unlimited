<?php

/* carta terminada dia 24/02/2017. terminada em 1 dia
 * Quando esta carta é Summoned, selecione 1 monstro que o seu oponente controle.
 * O ATK e DEF dessa carta se tornam iguais ao ATK e DEF originais do monstro selecionado.
 */
class c_141 extends Monstro_normal {

 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {		
     $resposta = parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
     if($resposta) $this->_ativar();
     return $resposta;
 }
    
 private function _ativar() {  // Efeito desse monstro
    $gatilho[0] = 'monstro';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'invocar_carta';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    // ativando
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
 
  function carta_solicitada($cartaS) {
    	$gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'copiar_atributos_monstro';
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
                $alvo_limpo = new DB_cards;
                $alvo_limpo->ler_id($alvo->id);
    		$this->atk = $alvo_limpo->atk;
                $this->def = $alvo_limpo->def;
                if($alvo->modo == MODOS::DEFESA_BAIXO) parent::avisar ('Um monstro virado para baixo foi copiado',1);
                else parent::avisar ($alvo->nome.' foi copiado',1);
                  $grav = new Gravacao();
   //alterando o arquivo
  $grav->set_caminho($this->pasta.$this->inst.'.txt');
  $infos = $grav->ler(0);
    $infos[3] = $this->atk;
    $infos[4] = $this->def;
    $grav->set_array($infos);
    $grav->gravar();
    return true;
  }
 
}
?>