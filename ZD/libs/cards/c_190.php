<?php

/* terminada em 04/04/2017, terminada em ?? dias
 * Esta carta somente pode ser Ritual Summoned com o Ritual Spell Card, (End of the World).
 * Você pode pagar 2000 LPs para destruir todas as outras cartas no campo. 
 */

class c_190 extends Monstro_normal {
    function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
      if($tipo != 'ritual' && $tipo != 'controle') {
          parent::avisar('Só é possivel invocar esse monstro atraves do efeito do ritual End of the World');
          parent::destruir();
          $this->duelo->colocar_carta_hand($id, $dono);
          return false;
      }
      return parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
    }

    function ativar_efeito() {
        parent::dano_direto($this->pasta.'lps.txt', 2000);
        $this->obliterar();
        $this->auto_obliterar();
        return true;
    }
 
function Comandos() {
  $arq = fopen($this->pasta.'/phase.txt', 'r');
  $phase = fgets($arq);
  fclose($arq);
  
   $comandos_possiveis['ativar'] = true; // monstro normal não pode ativar efeito
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
    
   private function obliterar() {
        $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_monstro';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_especial';
        $gatilho[3] = 'oponente';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       
            $campo = $this->duelo->ler_campo_cods($this->duelo->oponente($this->dono));
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_monstro';
              $id_oponente = $this->duelo->oponente($this->dono);
            for($x = 1; $x <= 11; $x++) {
             if($campo[$x] != 0) {
               $alvo = &$this->duelo->regenerar_instancia($campo[$x], $id_oponente);
               if($alvo !== 0) $alvo->sofrer_efeito($efeito, $this);
               $campo = $this->duelo->ler_campo_cods($this->duelo->oponente($this->dono));
             }
            }
            return true;
  }

   private function auto_obliterar() {
        $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_monstro';
        $gatilho[3] = 'self';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       $gatilho[0] = 'monstro';
        $gatilho[1] = 'efeito';
        $gatilho[2] = 'destruir_especial';
        $gatilho[3] = 'self';
       if(parent::checar($gatilho)) {
         $carta = &parent::checar($gatilho);
          $resposta = $carta->acionar($gatilho, $this);
         if($resposta['bloqueado']) {return false;}
       }
       
            $campo = $this->duelo->ler_campo_cods($this->dono);
              $efeito[0] = 'destruir';
              $efeito[1] = 'efeito_monstro';
            for($x = 1; $x <= 11; $x++) {
             if($campo[$x] != 0) {
               $alvo = &$this->duelo->regenerar_instancia($campo[$x], $this->dono);
               if($alvo !== 0 && $alvo->inst != $this->inst) $alvo->sofrer_efeito($efeito, $this);
               $campo = $this->duelo->ler_campo_cods($this->dono);
             }
            }
            return true;
  }
    
}
?>