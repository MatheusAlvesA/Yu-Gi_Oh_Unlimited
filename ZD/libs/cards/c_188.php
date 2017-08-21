<?php
class c_188 extends Monstro_normal {
    /* terminada dia 04/04/2017. terminada em 1 dia
     * Quando essa carta é selecionada como alvo de ataque de um monstro do oponente,
     caso você possua monstros de nivel 7 ou maior e tipo Dragon em seu Cemitério,
     será selecionado aleatóriamente um desses monstros e invocado Special Summon para o seu lado do campo.
     Mude o alvo do ataque para o monstro invocado.
     (Nessa hora traps não poderão ser ativadas para impedir a Invocação e/ou destruir o monstro a ser invocado)
    */
    
function atacado($ataque) {
    $lista = $this->listar();
    $campo = $this->duelo->ler_campo($this->dono);
    // testando se tem espaço em campo
    $local = 0;
    $x = 1;
    for(; $x <= 5;$x++) if($campo[$x][0] === 0) break;
    if($x <= 5) $local = $x;
    else {
        if(count($lista) > 0) parent::avisar('Não existem espaço em campo para invocar um dragão');
        return parent::atacado($ataque);
    }
    
    if(count($lista) > 0) {
        $escolhido = $lista[rand(0, (count($lista)-1) )];
        parent::avisar('Efeito do '.$this->nome.' ativado',1);
        if($this->duelo->invocar($this->dono, $local, MODOS::ATAQUE, $escolhido, 'especial') === 0) return parent::atacado($ataque);
        $this->duelo->apagar_cmt($escolhido, $this->dono);
        $novo_alvo = $this->duelo->regenerar_instancia_local($local, $this->dono);
        $bloqueio['resultado'] = 'n';
        $this->agendar_ataque($ataque['atacante'], $novo_alvo);
        if($this->modo == MODOS::DEFESA_BAIXO) {
                $grav = new Gravacao();
                $grav->set_caminho($this->pasta.$this->inst.'.txt');
                $infos = $grav->ler(0);
                $infos[8] = MODOS::DEFESA;
                $grav->set_array($infos);
                $grav->gravar();
                unset($grav);
        }
        return $bloqueio;
    } else return parent::atacado($ataque);
}

function agendar_ataque($atacante, $atacado) {
    parent::manter('atacante', $atacante->inst);
    parent::manter('atacado', $atacado->inst);
    if(parent::ler_variavel('engine_setado') != 1) {$this->duelo->set_engine($this->inst, $this->dono);parent::manter('engine_setado', 1);}
    return true;
}
function engine() {
    if(parent::ler_variavel('atacante') != 0 && parent::ler_variavel('atacado') != 0) {
        $atacante = $this->duelo->regenerar_instancia(parent::ler_variavel('atacante'), $this->duelo->oponente($this->dono));
        $atacado = $this->duelo->regenerar_instancia(parent::ler_variavel('atacado'), $this->dono);
        $atacante->manter('atacou_em', 0);
        $atacante->atacar($atacado, $this->pasta.'lps.txt');
    }
    parent::manter('atacante', 0);
    parent::manter('atacado', 0);
    return true;
}

private function listar() {
     $cmt = $this->duelo->ler_cmt($this->dono);
     $carta = new DB_cards;
     $lista = array();
     $y = 0;
     
     for($x = 1; $x < $cmt[0];$x++) {
         $carta->ler_id($cmt[$x]);
         if($carta->specie == 'dragon' && $carta->lv >= 7 && $carta->tipo != 'ritual' && $carta->tipo != 'ritual-effect' && $carta->tipo != 'fusion' && $carta->tipo != 'fusion-effect'&& $carta->id != 32 && $carta->id != 35) {
             $lista[$y] = $carta->id;
             $y++;
         }
     }
     
     return $lista;
 }
 
}
?>