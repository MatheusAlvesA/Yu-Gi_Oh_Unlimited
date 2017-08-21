<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/Mobile_Detect.php");

$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$db = new DB();
$db->ler($_SESSION["id"]);

$erro = '';
if($_GET['serverl']) {$erro = "<div class=\"alert alert-danger\" role=\"alert\">
      <strong>Problema!</strong> O servidor está cheio
  </div>";}
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Duelistas Yu-Gi-Oh Unlimited</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fonte.css" type="text/css" media="screen"/>
    <?php 
      $detectar = new Mobile_Detect;
      if(!$detectar->isMobile() || $detectar->isTablet()) echo '<link href="style_PC.css" rel="stylesheet"> <!--Estilos exclusivos quando aberto no computador-->';
      else echo '<link href="style.css" rel="stylesheet"> <!--Estilos personalizados-->';
    ?>
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand oficial-font" href="#">Yu-Gi-Oh Unlimited</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="home.php">Principal</a></li>
            <li><a href="perfil.php">Minha conta</a></li>
            <li><a href="tutorial.php">Aprenda a jogar</a></li>
            <li><a href="desenvolvimento.php">Desenvolvimento</a></li>
            <li id="caixa_user_data_nxp">
              <span id="user_data_nxp"><b><?php echo $db->nome;?></b> <?php echo $db->xp;?>XP</span>
              <a href="logout.php" id="BNTdeslogar_ancora"><span id="BNTdeslogar" class="glyphicon glyphicon-off" aria-hidden="true"></span></a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">

  <div role="main" class="col-md-6 col-md-push-3">

    <div id="top" class="row"> 
      <div class="col-md-9 col-md-push-3">
      <form action="buscar_user.php" method="get">
        <div class="input-group h2">
            <input name="nome" class="form-control" id="search" type="text" placeholder="Pesquisar Duelistas">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit">
                    <span class="glyphicon glyphicon-search"></span>
                </button>
            </span>
        </div>
      </form>
      </div>
    </div> <!-- /#topo -->
    <hr />

    <div id="list" class="row">
    <div class="table-responsive col-md-12">
        <table class="table table-striped" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th class="actions"><b style="float: right;">XP</b></th>
                 </tr>
            </thead>
            <tbody>
 
                <?php echo lista($_GET["nome"]);?>
 
            </tbody>
         </table>
     </div>
    </div> <!-- /#lista -->

  </div>

  <aside role="complementary" class="col-md-3 col-md-push-3">
  <?php echo $erro;?>
    <hr><a href="mapa.php"><img src="imgs/explorar_mapa.png" class="img-responsive" /></a><hr>
    <a href="ZD/index.php"><img src="imgs/B_duelos.png" class="img-responsive" /></a><hr>
    <a href="deck.php"><img src="imgs/B_Mdeck.png" class="img-responsive" /></a><hr>
    <a href="inventario.php"><img src="imgs/B_Minventario.png" class="img-responsive" /></a><hr>
    <a href="iedeck.php"><img src="imgs/iedeck.png" class="img-responsive" /></a><hr>
    <a href="comercio.php"><img src="imgs/B_comercio.png" class="img-responsive" /></a><hr>
    <a href="ranking.php"><img src="imgs/B_ranking.png" class="img-responsive" /></a><hr>
    <a href="meu_clan.php"><img src="imgs/B_clan.png" class="img-responsive" /></a><hr>
    <a href="amigos.php"><img src="imgs/B_amigos.png" class="img-responsive" /></a><hr>
    <a href="mensagens.php"><img src="imgs/B_<?php echo avisar_mensagem();?>mensagens.png" class="img-responsive" /></a><hr>
    <a href="perfil.php"><img src="imgs/B_meu_perfil.png" class="img-responsive" /></a>
  </aside>

    <div role="left" class="col-md-3 col-md-pull-9">
        <?php // esse código insere propaganda na página
            global $G_PROPAGANDA;
            if($G_PROPAGANDA) echo '
        <script type="text/javascript">
          ( function() {
            if (window.CHITIKA === undefined) { window.CHITIKA = { \'units\' : [] }; };
            var unit = {"calltype":"async[2]","publisher":"doutorx","width":300,"height":600,"sid":"Chitika Default"};
            var placement_id = window.CHITIKA.units.length;
            window.CHITIKA.units.push(unit);
            document.write(\'<div id="chitikaAdBlock-\' + placement_id + \'"></div>\');
        }());
        </script>
        <script type="text/javascript" src="//cdn.chitika.net/getads.js" async></script>
                ';
        ?>
    </div>
    
</div>

<hr>
<footer class="row">
  <div class="col-md-4 col-md-push-4">
    <?php include 'rodape.txt';?>
  </div>
</footer>
    <script src="bootstrap/jquery-3.1.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
<?php echo file_get_contents('EOP.txt');?>
  </body>
</html>
<?php
function lista($nome) {
 $t = new Tools();
 if($nome == '') {return '<tr><td colspan="2"><b>Nenhum usuário encontrado</b></td></tr>';}
 if(!$t->verificarstr($nome)) {return '<tr><td colspan="2"><b>Nenhum usuário encontrado</b></td></tr>';}
 unset($t);
 
 $bd = new DB();
 $array = $bd->tabela_user();

$x = 0;
$y = 0; // controla a linha da matriz
 while(count($array) > $y && $x <= 100) {
   if(str_igual($nome, $array[$y]['nome']) == 1) {$retorno = $retorno.'<tr><td><a href="user.php?nome='.$array[$y]['nome'].'"><b>'.$array[$y]['nome'].'</b></a></td><td><b style="float: right;">XP: '.$array[$y]['xp'].'</b></td></tr>'."\n"; $x++; }
   if(str_igual($nome, $array[$y]['nome']) == 2) {$retorno = '<tr><td><a href="user.php?nome='.$array[$y]['nome'].'"><b>'.$array[$y]['nome'].'</b></a></td><td><b style="float: right;">XP: '.$array[$y]['xp'].'</b></td></tr>'."\n".$retorno; $x++; }
   $y++;
 }
if($retorno == '') {return '<tr><td colspan="2"><b>Nenhum usuário encontrado</b></td></tr>';}
return $retorno;
}

function str_igual($str1, $str2) {
 $str1 = strtoupper($str1);
 $str2 = strtoupper($str2);
 if($str1 == $str2) {return 2;} // strings perfeitamente iguais
 $array1 = explode(" ", $str1);
 $array2 = explode(" ", $str2);
 
 $x = 0;
 while($x < count($array1)) {
  $y = 0;
  while($y < count($array2)) {
   $z = 0;
   if(strlen($array1[$x]) < strlen($array2[$y])) {$menor = $array1[$x];}
   else {$menor = $array2[$y];}
   while($z <= (strlen($menor) - 4)) {
    if(substr($array1[$x], $z, 4) == substr($array2[$y], $z, 4)) {return 1;}
    $z++;
   }
   $y++;
  }
  $x++;
 }
 return 0; // se chegou aqui entao nao e parecido
}

function avisar_mensagem() {
 if(!file_exists("msgs/_".$_SESSION["id"].".txt")) {
  $arq = fopen("msgs/_".$_SESSION["id"].".txt", 'w');
  fwrite($arq, 0);
  fclose($arq);
  }
 $arq = fopen("msgs/_".$_SESSION["id"].".txt", 'r');
 $r = fgets($arq);
 fclose($arq);
 if($r == 0) {return '';}
 else {return "T";} //isso fara então receber uma imagem diferente
 }
?>