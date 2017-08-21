<?php
include('../libs/db_lib.php');
include("libs/loby_lib.php");
include_once("../libs/desafio_lib.php");
include("../libs/tools_lib.php");
$tools = new Tools(true);
$tools->verificar();
session_start();
    if($_SESSION['logado'] != 'S' && !isset($_SESSION['deck_predef'])) {header('location: ../logout.php'); exit();}
    if($_SESSION['duelando'] != 'S') {header("location: loby.php"); exit();}
    if(!$_SESSION['resultado']) {header("location: duelo.php"); exit();}
    $array = explode('/', $_SESSION['resultado']);
    if($_SESSION['id'] == $array[0]) {$venceu = 's';}
    else {$venceu = 'n';}

    $voltar = 'loby'; // por padrão o botão voltar leva para o loby
    if($_SESSION['duelo_mapa'] === 'S') {$voltar = '../mapa';} // mas se for um duelo do mapa leva para o mapa
    if(isset($_SESSION['deck_predef'])) {$voltar = '../logout';} // se for um duelo sem cadastro

?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Fim do duelo - Yu-Gi-Oh Unlimited</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet"> <!--Estilos personalizados-->
    <link rel="stylesheet" href="../fonte.css" type="text/css" media="screen"/>
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand oficial-font" href="#">Yu-Gi-Oh Unlimited</a>
        </div>
      </div>
    </nav>

<div class="row">

  <div role="main" class="col-md-4 col-md-push-4">
	<img src="<?php echo '../'.$tools->capa;?>" height="<?php echo $tools->rcapa;?>" width="100%" />
	<img src="../imgs/vitoria_<?php echo $venceu;?>.png" height="50%" width="100%" />
	<a href="<?php echo $voltar;?>.php"><img src="<?php echo '../'.$tools->B_voltar;?>" height="<?php echo $tools->rbotao;?>" width="100%" /></a>
	<?php include '../rodape.txt';?>
  </div>

</div>
    <script src="../bootstrap/jquery-3.1.1.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
<?php
if(!file_exists('duelos/'.$_SESSION['id_duelo'].'.txt')) {
 file_put_contents('duelos/'.$_SESSION['id_duelo'].'.txt', $_SESSION['resultado']);
 $suporte = new SSID();
 $desafio = new Desafio;
 if($_SESSION['duelo_mapa'] === 'S') { // se esse foi um duelo do mapa
  $banco = new DB;
  $banco->ler($_SESSION['id']);
  if($desafio->existe($banco->nome)) $desafio->remover();
 }
 if($_SESSION['logado'] === 'S')$suporte->finalizar($_SESSION['id_duelo']);
 else {
    if(file_exists('duelos/'.$_SESSION['id_duelo'])) {$suporte->delTree('duelos/'.$_SESSION['id_duelo']);}
    session_destroy();
 }
 unset($suporte);
}
else {
    @unlink('duelos/'.$_SESSION['id_duelo'].'.txt');
    $_SESSION['duelando'] = '';
    $_SESSION['id_duelo'] = '';
    $_SESSION['resultado'] = '';
    $_SESSION['duelo_mapa'] = '';
    $_SESSION['honra'] = '';
}
?>