<?php
class c_209 extends Magica {
 /*carta terminada em 23/04/2017. criada em 1 dia
  *Você pode Normal Summon 1 vez adicional neste turno.
  */

 function ativar_efeito() {  // unica função dessa mágica
  if(!parent::checar_ativar() || parent::ler_variavel('ativado_em') != 0) {return false;}
  	$gatilho[0] = 'magica';
	$gatilho[1] = 'efeito';
        $gatilho[1] = 'invocar';
	if(parent::checar($gatilho)) {
		 $carta = &parent::checar($gatilho);
		 $resposta = $carta->acionar($gatilho, $this);
		  if($resposta['bloqueado']) {
                      return false;
                  }
	}

        parent::avisar('Efeito da carta '.$this->nome.' ativado', 1);
        parent::mudar_modo(MODOS::ATAQUE);
        
        if(file_exists($this->pasta.'m_invocado.txt')) {
            $this->_ativar();
        }
        else {
            $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
            $this->duelo->set_engine($this->inst, $this->dono);
            parent::manter('ativado_em', parent::ler_turno());
        }
        
  return true;
 }
 
 private function _ativar() {
     parent::destruir();
     @unlink($this->pasta.'m_invocado.txt');
     return true;
 }
 
 function engine() {if(file_exists($this->pasta.'m_invocado.txt'))$this->_ativar();}
 function tarefa($x) {parent::destruir();}
}
?>