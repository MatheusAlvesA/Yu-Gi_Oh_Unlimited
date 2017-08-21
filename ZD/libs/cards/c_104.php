<?php
class c_104 extends Monstro_normal {
/* terminado dia 13/09/2016. terminado em 1 dia
 * Esta carta ganha 500 de ATK por cada monstro Tipo Dragon que o seu oponente possui no campo e no Cemitério.*/

    function invocar($local, $id, $modo, $dono, $tipo = 'comum',$flags = false) {
        parent::invocar($local, $id, $modo, $dono, $tipo,$flags);
        $carta = new DB_cards;
        $carta->ler_id($id);
        parent::manter('atk_padrão', $carta->atk);
        $this->duelo->set_engine($this->inst, $dono);
    }
    
 function engine() {  // unica função dessa mágica
  $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
  $cmt = $this->duelo->ler_cmt($this->duelo->oponente($this->dono));
  $carta = new DB_cards();
  $adicional = 0;
  
  for($x = 1; $x <= 5; $x++) {
      $carta->ler_id($campo[$x][1]);
      if($campo[$x][1] != 0 && $carta->specie === 'dragon') {
          $adicional += 500;
      }
  }
  for($x = 1; $x < $cmt[0]; $x++) {
      $carta->ler_id($cmt[$x]);
      if($carta->specie === 'dragon') $adicional += 500;
  }
      
        $grav = new Gravacao();
	$grav->set_caminho($this->pasta.$this->inst.'.txt');
	$infos = $grav->ler(0);
        $infos[3] = (int)parent::ler_variavel('atk_padrão') + $adicional;
        $grav->set_array($infos);
        $grav->gravar();
	unset($grav);
      
      
   return true;
 }

   function sofrer_efeito($efeito, &$inst) {
          if(parent::ler_variavel('equip') == 0) { // se não estiver equipado
            if($efeito[0] == 'destruir') {
                parent::avisar($this->nome.' foi destruido pelo efeito da carta '.$inst->nome, 1);
                parent::destruir();
                return true;
            }
            else if($efeito[0] == 'remover_do_jogo') {
                parent::avisar($this->nome.' foi removido do jogo pelo efeito da carta '.$inst->nome, 1);
                parent::remover_do_jogo();
                return true;
            }
            else if($efeito[0] == 'mudar_modo') {
                $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[8] = $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->modo = $efeito[1];
                return true;
            }
            else if($efeito[0] == 'incrementar_LV') {
                $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[2] += $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->lv += $efeito[1];
                return true;
            }
            else if($efeito[0] == 'incrementar_ATK') {
                parent::manter('atk_padrão', (int)parent::ler_variavel('atk_padrão') + $efeito[1]);
                $this->atk += $efeito[1];
                return true;
            }
            else if($efeito[0] == 'incrementar_DEF') {
                $grav = new Gravacao();
		$grav->set_caminho($this->pasta.$this->inst.'.txt');
		$infos = $grav->ler(0);
		$infos[4] += $efeito[1];
		$grav->set_array($infos);
		$grav->gravar();
		unset($grav);
                $this->def += $efeito[1];
                return true;
            }
            else if($efeito[0] == 'voltar_deck') { // esse efeito retorna a carta para o deck
                parent::avisar($this->nome.' retornou para o deck pelo efeito da carta '.$inst->nome, 1);
                parent::remover_do_jogo();
                $this->duelo->colocar_no_deck($this->id, $this->dono);
                $this->duelo->embaralhar_deck($this->id, $this->dono);
                return true;
            }
            return false;
          }
          else {
             $equipamento = &$this->duelo->regenerar_instancia(parent::ler_variavel('equip'), $this->dono);
            if($efeito[0] == 'incrementar_ATK') {
                parent::manter('atk_padrão', (int)parent::ler_variavel('atk_padrão') + $efeito[1]);
                return true;
            }
             return $equipamento->monstro_sofrer_efeito($efeito, $inst, $this);
          }
  }
 
}
?>