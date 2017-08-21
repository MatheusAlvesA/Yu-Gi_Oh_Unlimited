<?php
//terminada dia 11/03/2016. terminada em 2 dias
/*
 * Esta carta não pode ser Normal summoned ou Set.
 * Essa carta não pode ser Special Summoned exceto por se ter exatamente 3 monstros DARK no seu Cemitério.
 * Você pode remover do jogo 1 monstro DARK do seu Cemitério para destruir 1 carta no campo.
 */

class c_163 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      $cmt = $this->duelo->ler_cmt($dono);
      $carta = new DB_cards;
      $n_darks = 0;
      for($x = 1; $x < $cmt[0]; $x++) {
          $carta->ler_id($cmt[$x]);
          if($carta->atributo === 'dark') $n_darks++;
      }
      
      if($n_darks === 3 || $tipo == 'controle') return parent::invocar ($local, $id, $modo, $dono, 'especial', $flags);
      else {
          parent::avisar('Você precisa ter exatamente 3 darks no cemitério!');
          parent::destruir('falha_na_invocação');
          return false;
      }
    }
    
    function ativar_efeito() {
        parent::manter('sacrificio', 'x'); // reset efeito
        $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir';
        $gatilho[3] = 'oponente';
        if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
         $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {$this->tarefa('x');return false;}
        }
        
      $cmt = $this->duelo->ler_cmt($this->dono);
      $carta = new DB_cards;
      $lista_darks = array();
      $y = 0;
      for($x = 1; $x < $cmt[0]; $x++) {
          $carta->ler_id($cmt[$x]);
          if($carta->atributo === 'dark') {
              $lista_darks[$y] = $carta->id;
              $y++;
          }
      }
      
      if(count($lista_darks) < 1) {
          parent::avisar('Você não tem darks para sacrificar em seu cemitério.');
          return false;
      }
      
      $this->duelo->solicitar_carta('Escolha um sacrifício', $lista_darks, $this->dono, $this->inst);
      return true;
    }
    
    function carta_solicitada($cartaS) {
        if(parent::ler_variavel('sacrificio') != 'x') {
            // formando lista do campo oponente
            $ferramenta = new biblioteca_de_efeitos;
            $flags['local'] = 'campo';
            $flags['revelar'] = true;
            $flags['duelista'] = $this->duelo->oponente($this->dono);
            $lista = $ferramenta->listar($this->duelo, $flags);
            if($lista === false) {
                parent::avisar('Seu oponente não constrola monstros');
                parent::manter('sacrificio', '');
                return false;
            }
            if($cartaS >= count($lista) || $cartaS < 0) return false;
            
            $campo = $this->duelo->ler_campo($this->duelo->oponente($this->dono));
            $campo_r = array();
            for($x = 1; $x < 10; $x++) {
                $campo_r[$x-1] = $campo[$x][1];
            }
            $local = $ferramenta->local_original($lista, $campo_r, $cartaS);
            
            return $this->executar(parent::ler_variavel('sacrificio'), $local+1);
        }
        
      $cmt = $this->duelo->ler_cmt($this->dono);
      $carta = new DB_cards;
      $lista_darks = array();
      $y = 0;
      for($x = 1; $x < $cmt[0]; $x++) {
          $carta->ler_id($cmt[$x]);
          if($carta->atributo === 'dark') {
              $lista_darks[$y] = $carta->id;
              $y++;
          }
      }
      
      if($cartaS >= $y || $cartaS < 0) return false;
      parent::manter('sacrificio', $lista_darks[(int)$cartaS]);
      
      // formando lista do campo oponente
      $ferramenta = new biblioteca_de_efeitos;
      $flags['local'] = 'campo';
      $flags['duelista'] = $this->duelo->oponente($this->dono);
      $lista = $ferramenta->listar($this->duelo, $flags);
      
      if($lista === false) {
          parent::avisar('Seu oponente não constrola monstros');
          parent::manter('sacrificio', '');
          return false;
      }
      
      $this->duelo->solicitar_carta('Escolha o alvo', $lista, $this->dono, $this->inst);
      return true;
    }
    
    function executar($sacrificio, $local_alvo) {
        $this->duelo->apagar_cmt($sacrificio, $this->dono);
        $efeito[0] = 'destruir';
        $this->duelo->regenerar_instancia_local($local_alvo, $this->duelo->oponente($this->dono))
                ->sofrer_efeito($efeito, $this);
        parent::manter('sacrificio', '');
        return true;
    }
    
function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   $comandos_possiveis['ativar'] = true;
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
