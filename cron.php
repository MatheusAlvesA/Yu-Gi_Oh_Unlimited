<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
$tools = new Tools();
// este algoritmo deve ser configurado no servidor para ser executado a cada 10 minutos se possível ou o mais próximo disso
// ele é responsável por realizar todas as tarefas de manutenção do site

file_get_contents($tools->http.$_SERVER['SERVER_NAME'].'/ZD/faxineira.php'); // limpar os duelos inativos
file_get_contents($tools->http.$_SERVER['SERVER_NAME'].'/decks_tmp/faxineira.php'); // limpar deck que já foram baixados
if((date("H") - 3) == 0) {
    @unlink('acessos.txt');
    @unlink('duelar_agora.txt');
    @unlink('duelar_agora_desafios.txt');
}
if(date("j") == 1 && date("H") == 0) {hard_reset();}

if(!file_exists('dados_reinos.txt')) atualizar_pts_reinos();
else {
	$dados = json_decode(file_get_contents('dados_reinos.txt'));
	if((time() - $dados->{'ultima'}) > 60*60) atualizar_pts_reinos(); // atualize se já passou uma hora
}

function atualizar_pts_reinos() {
	$banco = new DB;
	$dados = array();
	// listando atributos do reino Obelisco
	$dados[1]['nduelistas'] = $banco->Nplayers_reino(1);
	if($dados[1]['nduelistas'] < 1)	$dados[1]['lider'] = 'Ninguém';
	else $dados[1]['lider'] = $banco->lider_reino(1);
	$dados[1]['pontos'] = $banco->total_pts_reino(1);
	// listando atributos do reino Slifer
	$dados[2]['nduelistas'] = $banco->Nplayers_reino(2);
	if($dados[2]['nduelistas'] < 1)	$dados[2]['lider'] = 'Ninguém';
	else $dados[2]['lider'] = $banco->lider_reino(2);
	$dados[2]['pontos'] = $banco->total_pts_reino(2);
	// listando atributos do reino RA
	$dados[3]['nduelistas'] = $banco->Nplayers_reino(3);
	if($dados[3]['nduelistas'] < 1)	$dados[3]['lider'] = 'Ninguém';
	else $dados[3]['lider'] = $banco->lider_reino(3);
	$dados[3]['pontos'] = $banco->total_pts_reino(3);

	$dados['ultima'] = time(); // pra saber quanto tempo faz que não atualiza

	file_put_contents('dados_reinos.txt', json_encode($dados));
}

function hard_reset() {
	$banco = new DB;
	$banco->reset_pts();
}
?>