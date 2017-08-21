<?php
include("libs/loby_lib.php");
include_once("../libs/Mobile_Detect.php");
include_once("../libs/desafio_lib.php");
include_once("../libs/db_lib.php");

session_start();
if($_SESSION["id"] == '') {header("location: ../logout.php"); exit();}
if($_SESSION['duelando'] != '') {header("location: duelo.php"); exit();}

$banco = new DB;

$banco->ler($_SESSION['id']);

$desafio = new Desafio;

if($desafio->existe($banco->nome) && $desafio->status === 's') { // esse jogador está em um desafio com estatus aceito
	$suporte = new SSID;
	if($desafio->id_duelo === '0') { // o duelo ainda não foi instanciado
		$_SESSION['duelando'] = 'S'; // registrando na seção que está em um duelo
		$_SESSION['duelo_mapa'] = 'S'; // esse é um duelo de mapa
		$_SESSION['duelo_honra'] = (int)$desafio->honra; // a honra pode ou não estar ativada
		$_SESSION['id_duelo'] = uniqid(); // o id é fgerado agora para agilizar o processo
		$desafio->set_id_duelo($_SESSION['id_duelo']); // registrando qual o id no banco
		$_SESSION['id_duelo'] = $suporte->instanciar($_SESSION['id'], $_SESSION['id_duelo']); // o duelo foi instanciado
		$detectar = new Mobile_Detect;
		if(!$detectar->isMobile() || $detectar->isTablet()) { // redirecionando
			header("location: duelo.php");
		} else {$_SESSION['mobile'] = 'S';header("location: duelo_m.php");}
		exit();// fim
	}
	else { // o duelo já está instanciado
		usleep(100); // atrasando o teste a seguir pra que dê tempo de intanciar totalmente o duelo
		if(file_exists('duelos/'.$desafio->id_duelo.'/metadata.txt')) { // o duelo está acontecendo
			$_SESSION['id_duelo'] = $desafio->id_duelo; // setando id
			$_SESSION['duelando'] = 'S'; // registrando na seção que está em um duelo
			$_SESSION['duelo_mapa'] = 'S'; // esse é um duelo de mapa
			$_SESSION['duelo_honra'] = (int)$desafio->honra; // a honra pode ou não estar ativada
			$detectar = new Mobile_Detect;
			if(!$detectar->isMobile() || $detectar->isTablet()) { // redirecionando
				header("location: duelo.php");
			} else {$_SESSION['mobile'] = 'S';header("location: duelo_m.php");}
			exit();// fim
		}
		else { // o duelo já não existe mais
			$desafio->remover();//apagando
			$backUP = $_SESSION['id']; // fazendo backuo do id
			session_destroy(); // deletando a sessão
			session_start(); // recomeçando a sessão
			$_SESSION['id'] = $backUP; // restabelecendo o id
			$_SESSION['logado'] = 'S'; // está logado novamente
			header('location: ../mapa.php'); // redirecionando
			exit(); // fim
		}
	}
}
else { // não existe desafio ou ele ainda não foi aceito
	if($_SESSION['ZD'] != '') header('location: loby.php');
	else header('location: ../mapa.php');
	exit();
}



?>