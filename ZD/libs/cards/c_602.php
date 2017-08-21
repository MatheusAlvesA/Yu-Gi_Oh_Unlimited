<?php
    /* carta terminada dia 26/05/2017. terminada em 2 dias
 * Esta carta só pode ser Ritual Summon através da Carta Mágica de Ritual (Final Ritual of the Ancients).
 * Uma vez por rodada, descartando 1 Carta Spell da sua mÃ£o,
 * selecione 1 monstro no lado do campo do seu oponentee ganhe o controle daquele monstro atÃ© o fim dessa rodada.
 */

class c_602 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'Final Ritual of the Ancients' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito da Final Ritual of the Ancients');
          parent::remover_do_jogo();
          $this->duelo->colocar_carta_hand(602, $dono);
          return false;
      }
      parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    }
    
    function ativar_efeito() {
        if(parent::ler_variavel('ativou_em') == parent::ler_turno() || parent::ler_variavel('controlado') != 0) {
            parent::avisar('Esse efeito já foi ativado nesse turno');
            return false;
        }
        parent::manter('sacrificio', 0);
        parent::manter('controlado', 0);
        
        $hand = $this->duelo->ler_mao($this->dono);
        $carta = new DB_cards;
        $lista = array();
        $y = 0;
        for($x = 1; $x < $hand[0];$x++) {
            $carta->ler_id($hand[$x]);
            if($carta->categoria == 'spell') {
                $lista[$y] = $carta->id;
                $y++;
            }
        }
        if($y <= 0) {
            parent::avisar('Não existem cartas mágicas na sua mão');
            return false; 
        }
        $this->duelo->solicitar_carta('Escolha uma carta mágica', $lista, $this->dono, $this->inst);
        return true;
    }
    
    function carta_solicitada($cartaS) {
        if(parent::ler_variavel('sacrificio') == 0) {
            $hand = $this->duelo->ler_mao($this->dono);
            $carta = new DB_cards;
            $lista = array();
            $y = 0;
            for($x = 1; $x < $hand[0];$x++) {
                $carta->ler_id($hand[$x]);
                if($carta->categoria == 'spell') {
                    $lista[$y] = $carta->id;
                    $y++;
                }
            }
            if($y <= 0 || !isset($lista[$cartaS])) {return false;}
            parent::manter('sacrificio', (int)$lista[$cartaS]);

            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
            $lista_campo = array();
            $y = 0;
            for($x = 1; $x <= 5;$x++) {
                if($campo[$x][1] != 0) {
                    if($campo[$x][0] != MODOS::DEFESA_BAIXO) $lista_campo[$y] = $campo[$x][1];
                    else $lista_campo[$y] = 'ub';
                    $y++;
                }
            }
            if($y <= 0) {
                parent::avisar('Seu oponente não controla monstros');
                parent::manter('sacrificio', 0);
                return false;
            }
            $this->duelo->solicitar_carta('Escolha um monstro', $lista_campo, $this->dono, $this->inst);
            return true;
        } else {
            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
            $lista_campo = array();
            $campo_r = array();
            $y = 0;
            for($x = 1; $x <= 5;$x++) {
                $campo_r[$x] = $campo[$x][1];
                if($campo[$x][1] != 0) {
                     $lista_campo[$y] = $campo[$x][1];
                    $y++;
                }
            }
            if($y <= 0 || !isset($lista_campo[$cartaS])) {
                parent::manter('sacrificio', 0);
                return false;
            }
            $tool = new biblioteca_de_efeitos;
            $local = $tool->local_original($lista_campo, $campo_r, $cartaS);
            $monstro = $this->duelo->regenerar_instancia_local($local, $this->duelo->oponente($this->dono));
            //temos a carta pra ser sacrificada e temos o monstro pra controlar
            $campo = $this->duelo->ler_campo($this->dono);
            $local = 0;
            for($x = 1; $x <= 5;$x++) {
                if($campo[$x][1] == 0) {
                    $local = $x;
                    break;
                }
            }
            if($local == 0) {
                parent::manter('sacrificio', 0);
                parent::avisar('Você não tem espaço para invocar um monstro');
                return false;
            }
            $id = $monstro->id;
            $efeito[0] = 'remover_do_jogo';
            if(!$monstro->sofrer_efeito($efeito, $this)) {
                parent::manter('sacrificio', 0);
                return false;
            }
            $tool->apagar_carta_mao(parent::ler_variavel('sacrificio'), $this->dono, $this->duelo);
            $this->duelo->invocar($this->dono, $local, MODOS::ATAQUE, $id, 'controle');
            $controlado = $this->duelo->regenerar_instancia_local($local, $this->dono);
            parent::manter('controlado', $controlado->inst);
            parent::avisar('O monstro '.$controlado->nome.' foi controlado pelo efeito da '.$this->nome, 1);
            parent::manter('ativou_em', parent::ler_turno());
            $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
            return true;
        }
    }
    
    function tarefa($txt) {
        if(parent::ler_variavel('controlado') != 0) {
            if(file_exists($this->pasta.parent::ler_variavel('controlado').'.txt')) {
                $controlado = $this->duelo->regenerar_instancia(parent::ler_variavel('controlado'),$this->dono);
                $id = $controlado->id;
                $efeito[0] = 'remover_do_jogo';
                $controlado->sofrer_efeito($efeito, $this);
                $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
                $local = 0;
                for($x = 1; $x <= 5;$x++) {
                    if($campo[$x][1] == 0) {
                        $local = $x;
                        break;
                    }
                }
                if($local != 0) $this->duelo->invocar($this->duelo->oponente($this->dono), $local, MODOS::ATAQUE, $id, 'controle');
                parent::manter('sacrificio', 0);
                parent::manter('controlado', 0);
                return true;
            } else {
                parent::manter('sacrificio', 0);
                parent::manter('controlado', 0);
                return false;
            }
        }
        $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
        return false;
    }

 function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   if(parent::ler_variavel('ativou_em') == parent::ler_turno() || parent::ler_variavel('controlado') != 0) $comandos_possiveis['ativar'] = false;
   else $comandos_possiveis['ativar'] = true;
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