<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/Mobile_Detect.php");
$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$db = new DB();
$db->ler($_SESSION["id"]);

$dados_reinos = array();
if(file_exists('dados_reinos.txt')) {
  $dados_reinos = json_decode(file_get_contents('dados_reinos.txt'));
}

$erro = '';
if($_GET['serverl']) {$erro = "<div class=\"alert alert-danger\" role=\"alert\">
      <strong>Problema!</strong> O servidor está cheio
  </div>";}
  if($_GET['serverm']) {$erro = "<div class=\"alert alert-danger\" role=\"alert\">
      <strong>Manutenção!</strong> A zona de duelos vai estar disponível novamente em breve
  </div>";}
  if($_GET['mapamobile']) {$erro = "<div class=\"alert alert-danger\" role=\"alert\">
      O mapa está disponível apenas para computadores
  </div>";}
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Yu-Gi-Oh Unlimited</title>
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
            <li class="active"><a href="#">Principal</a></li>
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

  <aside role="complementary" class="col-md-3 col-md-push-9">
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

  <div role="main" class="col-md-6">
    <div class="row" style="text-align: center;">
      <div class="col-md-4">
        <div class="thumbnail" id="div_reino_2">
          <img src="imgs/slifer_ICO.jpg">
          <div class="caption">
            <b>Lider: <a href="user.php?nome=<?php echo $dados_reinos->{'2'}->{'lider'};?>"><?php echo $dados_reinos->{'2'}->{'lider'}?></a></b><br />
            <b><?php echo $dados_reinos->{'2'}->{'nduelistas'}?> Duelistas</b><br />
            <b><?php echo $dados_reinos->{'2'}->{'pontos'}?> Pontos no total</b>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="thumbnail" id="div_reino_3">
          <img src="imgs/ra_ICO.jpg">
          <div class="caption">
            <b>Lider: <a href="user.php?nome=<?php echo $dados_reinos->{'3'}->{'lider'};?>"><?php echo $dados_reinos->{'3'}->{'lider'}?></a></b><br />
            <b><?php echo $dados_reinos->{'3'}->{'nduelistas'}?> Duelistas</b><br />
            <b><?php echo $dados_reinos->{'3'}->{'pontos'}?> Pontos no total</b>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="thumbnail" id="div_reino_1">
          <img src="imgs/obelisk_ICO.jpg" class="img-responsive">
          <div class="caption">
            <b>Lider: <a href="user.php?nome=<?php echo $dados_reinos->{'1'}->{'lider'};?>"><?php echo $dados_reinos->{'1'}->{'lider'}?></a></b><br />
            <b><?php echo $dados_reinos->{'1'}->{'nduelistas'}?> Duelistas</b><br />
            <b><?php echo $dados_reinos->{'1'}->{'pontos'}?> Pontos no total</b>
          </div>
        </div>
      </div>
    </div>

      <h1 style="text-align: center;">Aviso aos novos duelistas</h1><hr>
  <p style="font-size: 15pt; text-align: justify;">
Para os jogadores que estão chegando agora apenas uma instrução: o site tem poucos duelistas então pode ser difícil conseguir alguém online para um duelo. Para duelar va até "Zona de Duelos" ou "Explorar Mapa". Todos os jogadores começam com um deck padrão, vá até "meu deck" para checar seu deck.<br>
O jogo ainda está na forma de protótipo, significa que o site ainda não está "bonito" e bem organizado. Peço a paciência de todos até que as coisas se ajeitem.
  </p>
  <b id="assinatura">Matheus Alves</b>
  </div>

    <div role="left" class="col-md-3 col-md-pull-9" style="position: relative; top: 50%;">
        <?php // esse código insere propaganda na página
            global $G_PROPAGANDA;
            $m = new Mobile_Detect;
            if($G_PROPAGANDA && !$m->isMobile()) echo '
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