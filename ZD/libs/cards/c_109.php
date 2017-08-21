<?php
class c_109 extends Armadilha {
/* carta feita dia 21/09/2016. terminada em 2 dias
 * Aumente a DEF de um monstro virado para cima no campo em 500 pontos até o final do turno.*/

 function ativar_efeito() {
  if(!parent::checar_ativar()) {return false;}
       $campo = $this->duelo->ler_campo($this->dono);
       $y = 0;
       for($x = 1; $x <= 5; $x++) {
           if($campo[$x][1] != 0 && $campo[$x][0] != MODOS::DEFESA_BAIXO) {
               $cartas[$y] = $campo[$x][1]; $y++;
           }
       }
       if(!isset($cartas)) {parent::avisar('NÃ£o foi possivel ativar o efeito da carta '.$this->nome); return false;}
           
    $this->duelo->solicitar_carta('ESCOLHA UM MONSTRO', $cartas, $this->dono, $this->inst);
    $this->mudar_modo(1);
    $this->duelo->agendar_tarefa($this->inst, $this->dono, 'end_phase', 'x');
    return true;
 }
 
 function carta_solicitada($cartaS) {
       $campo = $this->duelo->ler_campo($this->dono);
       $y = 0;
       for($x = 1; $x <= 5; $x++) {
           if($campo[$x][1] != 0 && $campo[$x][0] != MODOS::DEFESA_BAIXO) {
               $cartas[$y] = $campo[$x][1]; $y++;
           }
       }
       if(!isset($cartas)) {parent::avisar('NÃ£o foi possivel ativar o efeito da carta '.$this->nome);$this->tarefa('x'); return false;}
       
  $quantas = 0;
  for($x = 0; $x <= $cartaS; $x++){
    if($cartas[$x] == $cartas[$cartaS]) {$quantas++;}
  }
    for($x = 1; $x <= 5; $x++) {
         if($campo[$x][1] == $cartas[$cartaS]) {
             if($quantas > 1) {$quantas--;}
             else {break;}
         }
     }
     
   $alvo = $this->duelo->regenerar_instancia_local($x, $this->dono);
   $grav = new Gravacao();
   $grav->set_caminho($alvo->pasta.$alvo->inst.'.txt');
   $infos = $grav->ler(0);
   $infos[4] += 500;
   $grav->set_array($infos);
   $grav->gravar();
   
   parent::avisar('Efeito da carta '.$this->nome." ativado, ".$alvo->nome." foi afetado", 1);
   parent::manter('monstro', $alvo->inst);
   return true;
 }
 
 function tarefa($txt) { //essa carta deve se destruir caso seja ativada e round passe
     $this->duelo->apagar_tarefa($this->dono, 'end_phase', $this->inst);
   $alvo = $this->duelo->regenerar_instancia(parent::ler_variavel('monstro'), $this->dono);
   $grav = new Gravacao();
   $grav->set_caminho($alvo->pasta.$alvo->inst.'.txt');
   $infos = $grav->ler(0);
   $infos[4] -= 500;
   $grav->set_array($infos);
   $grav->gravar();
   parent::destruir();
 }
 
function destruir($motivo = 0) {
    $this->tarefa('x');
}

}
?>