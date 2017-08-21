<?php

class c_225 extends Monstro_normal {
/* carta terminada dem 10/05/2017 terminada dem 1 minuto por já ter outra igual
 * Se o seu oponente controla monstros e você não possui monstros no campo,
 * você pode Special Summon essa carta (da sua mão).
*/

 function invocar($local, $id, $modo, $dono, $tipo = 'comum', $flags = false) {
 	if($tipo == 'comum') {
 		$campo_oponente = $this->duelo->ler_campo($this->duelo->oponente($dono));
 		$campo_dono = $this->duelo->ler_campo($dono);
                $x = 0; // declarando o escopo
                
 		for($x = 1; $x <= 5; $x++) {
 			if($campo_oponente[$x][1] !== 0) break;
 		}
 		// nesse caso percorreu todo campo e não encontro monstro
                if($x > 5) {return parent::invocar($local, $id, $modo, $dono, 'comum', $flags);}
 
 		for($x=1;$x<=5;$x++) {
 			if($campo_dono[$x][1] !== 0) break;
 		}
 		// nesse caso percorreu o campo e encontrou algum monstro
                if($x <= 5) {return parent::invocar($local, $id, $modo, $dono, 'comum', $flags);}
                
 		//ativando efeito de invocar especial
 		return parent::invocar($local, $id, $modo, $dono, 'especial', $flags);
 	}
     else return parent::invocar($local, $id, $modo, $dono, $tipo, $flags);
 }
 
}