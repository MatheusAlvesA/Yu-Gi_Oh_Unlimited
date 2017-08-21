<?php
class Armadilha extends Carta {
 var $nome;
 var $tipo;
 var $inst;
 var $pasta;
 var $id;
 var $modo;
 var $dono;
 var $duelo;
 
 var $lista_XSaber;
	function __construct($duelo, $pasta, $inst = 0) {
         $this->lista_XSaber[0] = 7;
         $this->lista_XSaber[1] = 817;
         $this->lista_XSaber[2] = 818;
         $this->lista_XSaber[3] = 819;
         $this->lista_XSaber[4] = 820;
         $this->lista_XSaber[5] = 821;
         $this->lista_XSaber[6] = 822;
		$this->pasta = $pasta;
		$this->duelo = &$duelo;
	 if($inst) {
		 $this->inst = $inst;
		 $grav = new Gravacao();
		 $grav->set_caminho($this->pasta.$this->inst.'.txt');
		 $infos = $grav->ler(0);
		 unset($grav);
		$this->nome = $infos[1];
	  $this->tipo = $infos[2];
	  $this->id = $infos[3];
	  $this->modo = $infos[4];
          $this->dono = $infos[5];
	  return true;
		}
		else {return false;}
	}
	function invocar($local, $id, $modo, $dono, $tipo, $gatilho = false) {
		if($this->inst) {return false;}
		if($modo != 3) {
		 parent::avisar('Você não pode ativar essa armadilha imediatamente!');
		 return false;
		}
	$arq = fopen($this->pasta.'/phase.txt', 'r');
	$phase = fgets($arq);
	fclose($arq);
	if($phase != 3 && $phase != 5) {parent::avisar('Você não pode colocar em campo fora das MainPhases'); return false;}

	 	 	$monstro = new DB_cards();
		 $monstro->ler_id($id);
		 $this->nome = $monstro->nome;
		 $this->tipo = $monstro->tipo;
		 $this->id = $monstro->id;
		 $this->modo = $modo;
		 $this->dono = $dono;
		 unset($monstro);
		 $this->inst = uniqid();
		 $infos = "$this->nome\n$this->tipo\n$this->id\n$this->modo\n$this->dono";
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
	
	  if($gatilho) {
		 if($gatilho == 'ataque_monstro') {
			 unset($gatilho);
	    $gatilho[0] = 'monstro';
	    $gatilho[1] = 'ataque';
	    $gatilho[2] = 'tudo';
	    $gatilho[3] = $this->dono.'-'.$this->inst.'-oponente';
	    parent::set_gatilho($gatilho);
	    }
	   }
		 parent::avisar('Uma carta face para baixo foi baixada', 1);
		return 1;
	}
        
        function sofrer_efeito($efeito, &$inst) {
            if($efeito[0] == 'destruir') { // esse efeito destroi a carta
                parent::avisar($this->nome.' foi destruido pelo efeito da carta '.$inst->nome, 1);
                parent::destruir();
                return true;
            }
            if($efeito[0] == 'voltar_deck') { // esse efeito retorna a carta para o deck
                parent::avisar($this->nome.' foi destruido pelo efeito da carta '.$inst->nome, 1);
                parent::remover_do_jogo();
                $this->duelo->colocar_no_deck($this->id, $this->dono);
                $this->duelo->embaralhar_deck($this->id, $this->dono);
                return true;
            }
            return false;
        }
        
        protected function mudar_modo($modo) {
         $grav = new Gravacao();
	 $grav->set_caminho($this->pasta.$this->inst.'.txt');
	 $infos = $grav->ler(0);
	 $infos[4] = $modo;
	 $grav->set_array($infos);
	 $grav->gravar();
	 unset($grav);
         $this->modo = $modo;
         parent::manter('modo_alterado_em', parent::ler_turno());
         return true;
        }
        
	function checar_ativar() {
               // por em quanto não existe bloqueio pra armadilha
         return true;
	}
}
?>