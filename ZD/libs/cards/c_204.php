<?php

/* carta termiada dia 20/04/2107. Terminada em 1 dia
 * Uma vez por turno:
 * Você pode descartar 1 carta, então selecionar 1 monstro tipo Dragon no seu cemitério, adicione-o á sua mão.
 */

class c_204 extends Monstro_normal {

    function ativar_efeito() {
        if((int)parent::ler_turno() == (int)parent::ler_variavel('ativou_em')) {
            parent::avisar('Esse efeito já foi ativado nesse turno');
            return false;
        }
        if(parent::ler_variavel('descartar') != 0) {parent::manter('descartar', 0);}
        
        $hand = $this->duelo->ler_mao($this->dono);
        if($hand[0] == 1) {
            parent::avisar('Você não tem cartas para descartar ');
            return false;
        }
        for($x = 1; $x < $hand[0]; $x++) {$lista[$x-1] = $hand[$x];}
        
        $this->duelo->solicitar_carta('descartar', $lista, $this->dono, $this->inst);
        
        return true;
    }

    function carta_solicitada($cartaS) {
        if(parent::ler_variavel('descartar') == 0) {
            $hand = $this->duelo->ler_mao($this->dono);
            for($x = 1; $x < $hand[0]; $x++) {$lista[$x-1] = $hand[$x];}
            if(count($lista) <= 0 || !isset($lista[$cartaS])) {
                parent::avisar('Falha ao ativar o efeito da carta '.$this->nome);
                return false;
            }

            $cmt = $this->duelo->ler_cmt($this->dono);
            $lista_cmt = array();
            $carta = new DB_cards;
            $y = 0;
            for($x = 1; $x < $cmt[0]; $x++) {
                $carta->ler_id($cmt[$x]);
                if($carta->specie == 'dragon') {
                    $lista_cmt[$y] = $cmt[$x];
                    $y++;
                }
            }
            if(count($lista_cmt) <= 0) {
                parent::avisar('Não existem dragões em seu cemitério');
                return false;
            }

            parent::manter('descartar', $lista[$cartaS]);
            $this->duelo->solicitar_carta('Ressuscitar', $lista_cmt, $this->dono, $this->inst);
            return true;
        }
        else { // as duas cartas estão definidas
            $cmt = $this->duelo->ler_cmt($this->dono);
            $lista_cmt = array();
            $carta = new DB_cards;
            $y = 0;
            for($x = 1; $x < $cmt[0]; $x++) {
                $carta->ler_id($cmt[$x]);
                if($carta->specie == 'dragon') {
                    $lista_cmt[$y] = $cmt[$x];
                    $y++;
                }
            }
            if(count($lista_cmt) <= 0 || !isset($lista_cmt[$cartaS])) {
                parent::manter('descartar', 0);
                parent::avisar('Essa carta não existe no cemitério');
                return false;
            }
            
            $tool = new biblioteca_de_efeitos;
            $tool->apagar_carta_mao(parent::ler_variavel('descartar'), $this->dono, $this->duelo);
            $this->duelo->apagar_cmt($lista_cmt[$cartaS], $this->dono);
            $this->duelo->colocar_carta_hand($lista_cmt[$cartaS], $this->dono);
            $this->duelo->colocar_no_cemiterio(parent::ler_variavel('descartar'), $this->dono);
            
            $carta->ler_id($lista_cmt[$cartaS]);
            parent::avisar('Efeito da carta '.$this->nome.' ativado, '.$carta->nome.' foi ressuscitado',1);
            parent::manter('decartar', 0);
            parent::manter('ativou_em', parent::ler_turno());
            
            return true;
        }
    }
            
function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   if((int)parent::ler_turno() != (int)parent::ler_variavel('ativou_em')) $comandos_possiveis['ativar'] = true;
   else $comandos_possiveis['ativar'] = false;
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