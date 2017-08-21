<?php
class c_4 extends Monstro_normal {
/*Jogue uma moeda e peça Cara ou Coroa. Se você acertar, inflija 1000 pontos de 
 *dano nos Life Points de seu oponente. Se você errar, você receberá 1000 pontos
 *de dano. Esse efeito somente pode ser usado uma vez por turno, durante sua Main Phase.*/
    function ativar_efeito() {
        $phase = $this->duelo->ler_phase($this->dono);
        $turno = $this->duelo->ler_turno();
        if($phase != 3 && $phase != 5) {
            parent::avisar('Você não pode ativar esse efeito fara das Main Phasesl!');
            return false;
        }
        if(parent::ler_variavel('ativado_em') == $turno) {
            parent::avisar('Você só pode ativar esse efeito uma vez por turno!');
            return false;  
        }
        
        if($this->duelo->moeda()) {
            $this->duelo->alterar_lp((-1000), $this->duelo->oponente($this->dono));
            parent::manter('ativado_em', $turno);
            parent::avisar('Uma moeda foi arremessada e o duelista que chamou o efeito VENCEU, 1000 de dano foi causado aos LPs do oponente', 1);
            return true;
        }
        else {
            $this->duelo->alterar_lp((-1000), $this->dono);
            parent::manter('ativado_em', $turno);
            parent::avisar('Uma moeda foi arremessada e o duelista que chamou o efeito PERDEU, 1000 de dano foi causado a seus LPs', 1);
            return true;
        }
    }
    
function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   if($phase != 4) $comandos_possiveis['ativar'] = true;
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