<?php

/* 
 * Você pode Normal Summon ou Set esta carta sem Tributar.
 * Se o fizer, o ATK original desta carta torna-se 1900.
 * Você pode Tributar 3 monstros para o Tribute Summon desta carta.
 * Quando esta carta é Tribute Summoned deste modo, destrua todas as cartas que o seu oponente controla.
 */

class c_58 extends Monstro_normal {
    
    
function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
   if($this->inst) {return 0;}
   
   $monstro = new DB_cards();
   $monstro->ler_id($id);
   $atk = $monstro->atk;
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
	 if(file_exists($this->pasta.'/m_invocado.txt') && !($flags !== false && $flags['ignorar']['invocou'] === true)) {parent::avisar('Você não pode invocar mais de um monstro por turno'); return 0;}
	$arq = fopen($this->pasta.'/sacrificios.txt', 'r');
	$sacrificios = fgets($arq);
	fclose($arq);
        $ativar = false;
	if($sacrificios >= 3) {
          $ativar = true;
          $sacrificios -= 3;
        }
        elseif($sacrificios == 2) {
            $sacrificios = 0;
        }
        else {
            $atk = 1900;
            $sacrificios = 0;
        }
 	$arq = fopen($this->pasta.'/sacrificios.txt', 'w');
	fwrite($arq, $sacrificios);
	fclose($arq);
  }
	elseif($tipo == 'especial' || $tipo == 'controle') { // processo de Invocação especial
		 if($modo == 3) {return 0;}
                   $this->inst = uniqid();
                 parent::manter('invocação', 'especial');
	}
                
		 $this->nome = $monstro->nome;
		 $this->lv = $monstro->lv;
		 $this->atk = $atk;
		 $this->def = $monstro->def;
		 $this->atributo = $monstro->atributo;
		 $this->specie = $monstro->specie;
		 $this->id = $monstro->id;
		 $this->modo = $modo;
		 unset($monstro);
		 $this->dono = $dono;
                 if($this->inst == '') $this->inst = uniqid();
                 if($tipo == 'comum') file_put_contents($this->pasta.'/m_invocado.txt', $this->inst);
                 
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
                 parent::manter('invocado_em',parent::ler_turno());
                 if($flags['não_mudar_modo'] === true) parent::manter('modo_alterado_em', parent::ler_turno());
		
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
     if($ativar) {$this->obliterar();} // efeito dessa carta
     return 1;
    }
    
   private function obliterar() {
       if(parent::ler_variavel('ativado') != 0) {return false;}
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
       
            parent::manter('ativado', 1);
            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
            parent::avisar('Efeito do monstro '.$this->nome.' ativado', 1);
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_monstro';
              $id_oponente = $this->duelo->oponente($this->dono);
            for($x = 1; $x <= 11; $x++) {
             if($campo[$x][1] != 0) {
               $alvo = &$this->duelo->regenerar_instancia_local($x, $id_oponente);
               $alvo->sofrer_efeito($efeito, $this);
             }
            }
            return true;
  }
}
?>