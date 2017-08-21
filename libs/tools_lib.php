<?php
require_once 'config.php';

 class Tools {
	var $B_login = 'imgs/B_login.png';
	var $B_voltar = 'imgs/B_voltar.png';
	var $B_cadastrar = 'imgs/B_cadastrar.png';
	var $B_comercio = 'imgs/B_comercio.png';
	var $B_aprenda = 'imgs/B_aprenda.png';
	var $B_sobre = 'imgs/B_sobre.png';
  var $D_online = 'imgs/D_online.png';
	var $B_amigos = 'imgs/B_amigos.png';
	var $B_sair = 'imgs/B_sair.png';
	var $B_perfil = 'imgs/B_meu_perfil.png';
	var $B_ranking = 'imgs/B_ranking.png';
	var $B_Mdeck = 'imgs/B_Mdeck.png';
	var $B_Minventario = 'imgs/B_Minventario.png';
	var $B_Msenha = 'imgs/B_msenha.png';
  var $B_duelos = 'imgs/B_duelos.png';
  var $B_pinicial = 'imgs/B_pinicial.png';
  var $B_Emensagem = 'imgs/B_Emensagem.png';
	var $rcapa = '30%';
	var $rbotao = '15%';
	var $capa = 'imgs/capa.png';
        var $ZD = false;
	var $fundo=''; //' style="background-color:black;"';
        var $SSL; // SSL configurado no construct
        var $http;

        function __construct($ZD = false) {
            global $G_SSL;
            $this->SSL = $G_SSL; // SSL configurado
            if($ZD) {$this->ZD = true;}
            if($this->SSL) {
                $this->forcarSSL();
                $this->http = 'https://';
            }
            else {$this->http = 'http://';}
        }
                
 function verificar() {
     $prefixo = '';
  if($this->ZD) {$prefixo = '../';}
  $arq = fopen($prefixo."SiteStatus.txt", 'r');
  $status = fgets($arq);
  fclose($arq);
  switch($status) { // caso o site esteja fora do ar
   case 1:
   header("location: ".$prefixo."erro/erro1.php");
   exit();
   break;
   case 2:
   header("location: ".$prefixo."erro/erro2.php");
   exit();
   break;
   default :
   break;
  }
  if($this->ZD) {echo '<meta name="viewport" content="width=device-width, height=device-height">'."\n";} // esta linha deve aparecer em todas as paginas html para regular a resolução de tela
 }

 function verificarlog() { // verifica se o usuario esta logado
    $prefixo = '';
  if($this->ZD) {$prefixo = '../';}
  session_start();
  if($_SESSION["logado"] != 'S') {
   header("location: ".$prefixo."index.php");
   exit();
  }
  if($_SESSION["ban"] == 'S') {
   header("location: ".$prefixo."logout.php");
   exit();
  }
   if($_SESSION["ZD"] != '' && !$this->ZD) {
   header("location: ZD/loby.php");
   exit();
  }
  if($_SESSION["ZD"] == '' && $this->ZD) {
   header("location: ../home.php");
   exit();
  }
   if($_SESSION["duelando"] != '' && $this->ZD) {
   header("location: duelo.php");
   exit();
  }
 }

 function forcarSSL() {
     if ($_SERVER['HTTP_X_FORWARDED_PROTO'] != 'https') {
        $url = $_SERVER['SERVER_NAME'];
        $new_url = "https://" . $url . $_SERVER['REQUEST_URI'];
        header("Location: $new_url");
        exit();
    }
 }
 
 function verificaridade($str) {
  if(strlen($str) > 2) {return 0;}
  $x = 0;
  while($x < strlen($str)) {
  $letra = substr($str, $x, 1);
  $x = $x + 1; 
  switch($letra) {
   case '0':
   break;
   case '1':
   break;
   case '2':
   break;
   case '3':
   break;
   case '4':
   break;
   case '5':
   break;
   case '6':
   break;
   case '7':
   break;
   case '8':
   break;
   case '9':
   break;
   default:
   return 0;
   break;
  }
  }
  return 1;
 }

 function verificarstr($str) {
  $x = 0;
  while($x < strlen($str)) {
   $letra = substr($str, $x, 1);
   $x = $x + 1;
  switch($letra) {
   case 'a':
   break;
   case 'A':
   break;
   case 'b':
   break;
   case 'B':
   break;
   case 'c':
   break;
   case 'C':
   break;
   case 'd':
   break;
   case 'D':
   break;
   case 'e':
   break;
   case 'E':
   break;
   case 'f':
   break;
   case 'F':
   break;
   case 'g':
   break;
   case 'G':
   break;
   case 'h':
   break;
   case 'H':
   break;
   case 'i':
   break;
   case 'I':
   break;
   case 'j':
   break;
   case 'J':
   break;
   case 'k':
   break;
   case 'K':
   break;
   case 'l':
   break;
   case 'L':
   break;
   case 'm':
   break;
   case 'M':
   break;
   case 'n':
   break;
   case 'N':
   break;
   case 'o':
   break;
   case 'O':
   break;
   case 'p':
   break;
   case 'P':
   break;
   case 'q':
   break;
   case 'Q':
   break;
   case 'r':
   break;
   case 'R':
   break;
   case 's':
   break;
   case 'S':
   break;
   case 't':
   break;
   case 'T':
   break;
   case 'u':
   break;
   case 'U':
   break;
   case 'v':
   break;
   case 'V':
   break;
   case 'w':
   break;
   case 'W':
   break;
   case 'x':
   break;
   case 'X':
   break;
   case 'y':
   break;
   case 'Y':
   break;
   case 'z':
   break;
   case 'Z':
   break;
   case '0':
   break;
   case '1':
   break;
   case '2':
   break;
   case '3':
   break;
   case '4':
   break;
   case '5':
   break;
   case '6':
   break;
   case '7':
   break;
   case '8':
   break;
   case '9':
   break;
   case '_':
   break;
   default :
   return 0;
   break;
  }
 }
return 1;
 }

}
?>