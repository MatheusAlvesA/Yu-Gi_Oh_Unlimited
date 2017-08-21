<?php
// este algoritmo é responsável por finalizar duelos que estejam por algum motivo inativos
// e apagar os .txt da pasta duelos
//ela vai rodar a cada 10 minutos chamada pelo con.php
include('libs/loby_lib.php');

$pastas = array_diff(scandir('duelos'), array('.','..','index.php'));
$grav = new Gravacao();
$suporte = new SSID();

foreach($pastas as $pasta) {
 if(file_exists('duelos/'.$pasta.'/tempo_turno.txt')) {
	$tempo = file_get_contents('duelos/'.$pasta.'/tempo_turno.txt');
  if((time() - $tempo) >= 600) {$suporte->finalizar($pasta);}
 }
elseif(file_exists('duelos/'.$pasta.'/metadata.txt')) {
	 $grav->set_caminho('duelos/'.$pasta.'/metadata.txt');
	 	$matriz = $grav->ler(1);
		$tempo = $matriz[1][0];
   if((time() - $tempo) >= 60) {$suporte->finalizar($pasta);}
	}
elseif(file_exists('duelos/'.$pasta)) {@unlink('duelos/'.$pasta);}
}
unset($grav);
unset($suporte);
?>