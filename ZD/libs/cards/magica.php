<?php
class Magica extends Carta{
 var $nome;
 var $tipo;
 var $inst;
 var $pasta;
 var $id;
 var $modo;
 var $dono;
 var $duelo;
 
 var $lista_Blackwing;
 var $lista_SixSamurai;
  function __construct($duelo, $pasta, $inst = 0) {
   $this->pasta = $pasta;
   $this->duelo = &$duelo;
   // lista com todos os monstros Blackwing
   $this->lista_Blackwing[0] = 8;
   $this->lista_Blackwing[1] = 79;
   $this->lista_Blackwing[2] = 80;
   $this->lista_Blackwing[3] = 81;
   $this->lista_Blackwing[4] = 82;
   $this->lista_Blackwing[5] = 83;
   $this->lista_Blackwing[6] = 84;
   $this->lista_Blackwing[7] = 85;
   
   $this->lista_SixSamurai[0] = 239;
   $this->lista_SixSamurai[1] = 338;
   $this->lista_SixSamurai[2] = 353;
   $this->lista_SixSamurai[3] = 426;
   $this->lista_SixSamurai[4] = 427;
   $this->lista_SixSamurai[5] = 428;
   $this->lista_SixSamurai[6] = 429;
   $this->lista_SixSamurai[7] = 725;
   $this->lista_SixSamurai[8] = 726;
   $this->lista_SixSamurai[9] = 727;
   $this->lista_SixSamurai[10] = 728;
   $this->lista_SixSamurai[11] = 729;
   $this->lista_SixSamurai[12] = 730;
   $this->lista_SixSamurai[13] = 343;
   
   
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
	function invocar($local, $id, $modo, $dono, $tipo) {
            if($modo == 2 || $modo == 4) {
                parent::avisar('Movimento inválido!');
                return false;
            }
		if($this->inst) {return false;}
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
	     if($modo != 3) {
	     $this->ativar_efeito();
	     return false;
	     }
	    parent::avisar('Uma carta face para baixo foi baixada', 1);
	    return 1;
	}

	function checar_ativar() {
	 $arq = fopen($this->pasta.'/phase.txt', 'r');
	 $phase = fgets($arq);
	 fclose($arq);
	 if($phase != 3 && $phase != 5) {parent::avisar('Você só pode ativar esse efeito nas suas Main Phases'); return false;}
         return true;
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
        
        function colocar_hand($nova_carta, $quem) {
	 $grav = new Gravacao();
	 $grav->set_caminho($this->duelo->dir_duelo.(int)$quem.'/hand.txt');
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
        
         function sofrer_efeito($efeito, &$inst) {
            if($efeito[0] == 'destruir') {
                parent::avisar($this->nome.' foi destruido pelo efeito da carta '.$inst->nome, 1);
                $this->destruir();
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
}
?>