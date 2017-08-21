<?php
// este algoritmo é responsável por apagar decks que já foram baixados.
//ela vai rodar a cada 10 minutos chamada pelo con.php

$arquivos = array_diff(scandir('../decks_tmp'), array('.','..','index.php','faxineira.php'));

foreach($arquivos as $arquivo) {
 if(file_exists($arquivo)) {
	$array = explode('.', $arquivo);
	$array = explode('_', $array[0]);
	$tempo = $array[1];
  if((time() - $tempo) >= 600) {unlink($arquivo);}
 }
}
?>