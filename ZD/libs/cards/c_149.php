<?php
class c_149 extends Magica {
/* carta terminada dia 04/03/2017. terminada em 2 dias
 * Ative somente enviando 1 monstro (Six-Samurai) virado para cima que você controla ao Cemitério.
 * Special Summon 1 monstro (Six Samurai) do Cemitério de qualquer um dos jogadores.*/

 function ativar_efeito() {  // unica função dessa mágica
    if(!parent::checar_ativar()) {return false;}
    
    $gatilho[0] = 'magica';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'resucitar';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    $ferramentas = new biblioteca_de_efeitos;
    
    $flags['local'] = 'campo_monstros';
    $flags['duelista'] = $this->dono;
    $flags['revelar'] = true;
    $flags['regra'] = 'six_samurai';
    $lista = $ferramentas->listar($this->duelo, $flags);
    
    if(count($lista) == 0 || $lista === false) {
        parent::avisar('Não existem monstros Six-Samurai no seu campo');
        parent::destruir();
        return false;
    }
    
    $this->duelo->solicitar_carta('Escolha um sacrifício', $lista, $this->dono, $this->inst);
    $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x'); // caso não faça o que precisa fazer
   parent::mudar_modo(MODOS::ATAQUE);
   return true;
 }
 
 function tarefa($txt) {
     parent::destruir();
     return true;
 }
         
  function carta_solicitada($cartaS) {
     if(parent::ler_variavel('sacrificio') !== 0) return $this->carta_solicitada_2($cartaS);
    $ferramentas = new biblioteca_de_efeitos;
    
    $flags['local'] = 'campo_monstros';
    $flags['duelista'] = $this->dono;
    $flags['revelar'] = true;
    $flags['regra'] = 'six_samurai';
    $lista = $ferramentas->listar($this->duelo, $flags);
    
    if(count($lista) == 0 || !isset($lista[$cartaS])) {
        parent::avisar('Não existem monstros Six-Samurai no seu campo');
        parent::destruir();
        return false;
    }
    
    $campo_bruto = $this->duelo->ler_campo($this->dono);
    $campo = array();
    for($x = 1;$x <= 5;$x++) {
        $campo[$x-1] = $campo_bruto[$x][1];
    }
    
    $local = $ferramentas->local_original($lista, $campo, $cartaS);
    parent::manter('sacrificio', $local+1);
    $this->ativar_efeito_2();
     return true;
 }
 
  function ativar_efeito_2() {  // unica função dessa mágica
    if(!parent::checar_ativar()) {return false;}
    
    $gatilho[0] = 'magica';
    $gatilho[1] = 'efeito';
    $gatilho[2] = 'resucitar';
    if(parent::checar($gatilho)) {
     $carta = &parent::checar($gatilho);
     $resposta = $carta->acionar($gatilho, $this);
     if($resposta['bloqueado']) {return false;}
    }
    $ferramentas = new biblioteca_de_efeitos;
    
    $flags['local'] = 'cmt';
    $flags['duelista'] = $this->dono;
    $flags['regra'] = 'six_samurai';
    $lista_d = $ferramentas->listar($this->duelo, $flags);
    $flags['duelista'] = $this->duelo->oponente($this->dono);
    $lista_o = $ferramentas->listar($this->duelo, $flags);
    
    $lista = array();
    $y = 0;
    $txt = 'Escolha um monstro do cemitério: ';
    if(count($lista_d) > 0) {
        $txt .= 'SEU';
        for($x = 0; $x < count($lista_d);$x++) {
            $lista[$y] = $lista_d[$x];
            $y++;
        }
    }
    if(count($lista_o) > 0) {
        if($y > 0) {
            $txt .= ' | OPONENTE';
            $lista[$y] = 'divisor';
            $y++;
        }
        else $txt .= 'OPONENTE';
        
        for($x = 0; $x < count($lista_o);$x++) {
            $lista[$y] = $lista_o[$x];
            $y++;
        }
    }
    
    if(count($lista) == 0) {
        parent::avisar('Não existem monstros Six-Samurai no seu cemitério ou do oponente');
        parent::destruir();
        return false;
    }
    
    $this->duelo->solicitar_carta($txt, $lista, $this->dono, $this->inst);
   return true;
 }
 
   function carta_solicitada_2($cartaS) {

    $ferramentas = new biblioteca_de_efeitos;
    
    $flags['local'] = 'cmt';
    $flags['duelista'] = $this->dono;
    $flags['regra'] = 'six_samurai';
    $lista_d = $ferramentas->listar($this->duelo, $flags);
    $flags['duelista'] = $this->duelo->oponente($this->dono);
    $lista_o = $ferramentas->listar($this->duelo, $flags);
    
    $lista = array();
    $y = 0;
    if(count($lista_d) > 0) {
        for($x = 0; $x < count($lista_d);$x++) {
            $lista[$y] = $lista_d[$x];
            $y++;
        }
    }
    if(count($lista_o) > 0) {
        if($y > 0) {
            $lista[$y] = 'divisor';
            $divP = $y;
            $y++;
        }
        
        for($x = 0; $x < count($lista_o);$x++) {
            $lista[$y] = $lista_o[$x];
            $y++;
        }
    }
    
    if(count($lista) == 0 || !isset($lista[$cartaS]) || $lista[$cartaS] === 'divisor') {
      parent::avisar('Falha ao ativar esse efeito');
      parent::destruir();
      return false;
    }
    
    $monstro = new DB_cards;
    $efeito[0] = 'destruir';
    $monstro->ler_id($lista[$cartaS]);
    $sacrificio = $this->duelo->regenerar_instancia_local(parent::ler_variavel('sacrificio'), $this->dono);
    
    if(isset($divP)) {
        if($cartaS > $divP)
            $this->duelo->apagar_cmt($lista[$cartaS], $this->duelo->oponente($this->dono));
        else
            $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
    }
    else{
        if(count($lista_d) == 0)
            $this->duelo->apagar_cmt($lista[$cartaS], $this->duelo->oponente($this->dono));
        else
            $this->duelo->apagar_cmt($lista[$cartaS], $this->dono);
    }
    
    parent::avisar('Efeito da carta '.$this->nome.' ativado. '.$monstro->nome.' foi ressuscitado em troca do '.$sacrificio->nome.' ser destruido', 1);
    $sacrificio->sofrer_efeito($efeito, $this);
    
    $campo = $this->duelo->ler_campo($this->dono);
    $x = 1;
    while($campo[$x][1] !== 0 && $x <= 5) $x++;
    $this->duelo->invocar($this->dono, $x, MODOS::ATAQUE, $monstro->id, 'especial');

    parent::destruir();
     return true;
}
}
?>