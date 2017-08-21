<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/Mobile_Detect.php");
$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$db = new DB();
$db->ler($_SESSION["id"]);

if($_GET['criar'] != '') {criar($_GET['criar']);exit();}
if($_GET['abandonar'] != '') {abandonar();exit();}
if($_GET['excluir'] != '') {excluir();exit();}
if($_GET['remover'] != '') {remover($_GET['remover']);exit();}
if($_GET['aceitar'] != '') {aceitar($_GET['aceitar']);exit();}

$erro = '';
if($_GET['erro'] != 0) {
  $erro = erro($_GET['erro']);
}

$clan = new DB_clan;
$html = '';
if($db->clan === null) { // esse jogador não tem clan
  $html = '    <h1 style="text-align: center;" class="oficial-font">Lobo solitario</h1>
    <hr />
    <h2>Por que não cria seu próprio?</h2>
    '.$erro.'
      <form action="meu_clan.php" method="get">
        <div class="input-group h2">
            <input name="criar" class="form-control" id="search" type="text" placeholder="Nome do Clã">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit">
                    CRIAR
                </button>
            </span>
        </div>
      </form>';
}
else {
  $clan->ler($db->clan);
  if($clan->lider != $_SESSION['id']) {header('location: info_clan.php?nome='.$clan->nome); exit();}
  $html = '
    <h1 style="text-align: center;" class="oficial-font">'.$clan->nome.'</h1><hr />
    <div class="row">
      <div class="col-md-6" style="text-align: center;">
        <h4>'.$clan->total_pts().' Pontos no total</h4>
      </div>
      <div class="col-md-6">
        <a href="meu_clan.php?abandonar=1"><button class="btn btn-warning">Abandonar Clã</button></a>
        <a href="meu_clan.php?excluir=1"><button class="btn btn-danger">EXCLUIR CLÃ</button></a>
      </div>
    </div>
    <hr />
    <div id="list" class="row">
      <h2>DUELISTAS</h2>
      '.$erro.'
      <div class="table-responsive col-md-12">
          <table class="table table-striped" cellspacing="0" cellpadding="0">
              <thead>
                  <tr>
                      <th>Nome</th>
                      <th>Pontos</th>
                      <th class="actions">Ações</th>
                   </tr>
              </thead>
              <tbody>
   
                  '.duelistas($clan->duelistas()).'
   
              </tbody>
           </table>
       </div>
    </div> <!-- /#lista -->
    <div id="list" class="row">
      <h2>Pendentes de aprovação</h2>
      <div class="table-responsive col-md-12">
          <table class="table table-striped" cellspacing="0" cellpadding="0">
              <thead>
                  <tr>
                      <th>Nome</th>
                      <th class="actions">Ações</th>
                   </tr>
              </thead>
              <tbody>
   
                  '.pendentes($clan->pendentes()).'
   
              </tbody>
           </table>
       </div>
    </div> <!-- /#lista -->';
}
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Meu Clã Yu-Gi-Oh Unlimited</title>
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
      <form action="buscar_clan.php" method="get">
        <div class="input-group h2">
            <input name="nome" class="form-control" id="search" type="text" placeholder="Pesquisar Clãs">
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

  <?php echo $html;?>

</div>

  <aside role="complementary" class="col-md-3 col-md-push-3">
    <hr><a href="mapa.php"><img src="imgs/explorar_mapa.png" class="img-responsive" /></a><hr>
    <a href="ZD/index.php"><img src="imgs/B_duelos.png" class="img-responsive" /></a><hr>
    <a href="deck.php"><img src="imgs/B_Mdeck.png" class="img-responsive" /></a><hr>
    <a href="inventario.php"><img src="imgs/B_Minventario.png" class="img-responsive" /></a><hr>
    <a href="iedeck.php"><img src="imgs/iedeck.png" class="img-responsive" /></a><hr>
    <a href="comercio.php"><img src="imgs/B_comercio.png" class="img-responsive" /></a><hr>
    <a href="ranking.php"><img src="imgs/B_ranking.png" class="img-responsive" /></a><hr>
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

function duelistas($lista) {
  $retorno = '';
  $tamanho = count($lista);
  for($loop = 0; $loop < $tamanho; $loop++)
    $retorno .= '<tr>
    <td> 
      <a href="user.php?nome='.$lista[($tamanho-1) - $loop]['nome'].'"><b>'.$lista[($tamanho-1) - $loop]['nome'].'</b></a>
    </td>
    <td>
      <b>'.$lista[($tamanho-1) - $loop]['pts_clan'].'</b>
    </td>
    <td>
      <a href="meu_clan.php?remover='.$lista[($tamanho-1) - $loop]['nome'].'"><button class="btn btn-danger btn-sm">REMOVER</button>
    </td>
    </tr>'."\n";
  return $retorno;
}

function pendentes($lista) {
  if($lista === false) return '<tr><td colspan="2"><b>Nenhuma solicitação de entrada</b></td></tr>';
  $retorno = '';
  for($loop = 0; $loop < count($lista); $loop++)
    $retorno .= '<tr>
    <td> 
      <a href="user.php?nome='.$lista[$loop]['nome'].'"><b>'.$lista[$loop]['nome'].'</b></a>
    </td>
    <td>
      <a href="meu_clan.php?aceitar='.$lista[$loop]['nome'].'"><button class="btn btn-success btn-sm">ACEITAR</button>
      <a href="meu_clan.php?remover='.$lista[$loop]['nome'].'"><button class="btn btn-danger btn-sm">RECUSAR</button>
    </td>
    </tr>'."\n";
  return $retorno;
}

function criar($nome) {
  $clan = new DB_clan;
  global $db;
  $ferramenta = new Tools;
  if(strlen($nome) < 5 || strlen($nome) > 30 || !$ferramenta->verificarstr($nome)) {header('location: meu_clan.php?erro=1');return false;}
  if($clan->clan_existe($nome)) {header('location: meu_clan.php?erro=2');return false;}
  if($db->clan !== null) {header('location: meu_clan.php');return false;}

  $clan->nome = $nome;
  $clan->lider = (int)$_SESSION['id'];
  if(!$clan->criar()) {header('location: meu_clan.php?erro=3');return false;}

  $db->set_status_clan(1);
  header('location: meu_clan.php');
  return true;
}

function remover($nome) {
  $clan = new DB_clan;
  global $db;
  $temp = new DB;
  $ferramenta = new Tools;

  if(strlen($nome) < 5 || strlen($nome) > 50 || !$ferramenta->verificarstr($nome)) {header('location: meu_clan.php');return false;}
  if($db->clan === null) {header('location: meu_clan.php');return false;}
  $clan->ler($db->clan);
  if((int)$clan->lider !== (int)$_SESSION['id']) {header('location: meu_clan.php');return false;}
  if(!$temp->user_existe($nome))  {header('location: meu_clan.php');return false;}
  $temp->ler($temp->nome_id($nome));
  if($temp->clan !== $db->clan)  {header('location: meu_clan.php');return false;}

  $temp->abandonar_clan();
  header('location: meu_clan.php');
  return true;
}

function aceitar($nome) {
  $clan = new DB_clan;
  global $db;
  $temp = new DB;
  $ferramenta = new Tools;

  if(strlen($nome) < 5 || strlen($nome) > 50 || !$ferramenta->verificarstr($nome)) {header('location: meu_clan.php');return false;}
  if($db->clan === null) {header('location: meu_clan.php');return false;}
  $clan->ler($db->clan);
  if((int)$clan->lider !== (int)$_SESSION['id']) {header('location: meu_clan.php');return false;}
  if((int)$clan->Nduelistas() >= 100) {header('location: meu_clan.php?erro=4');return false;}
  if(!$temp->user_existe($nome))  {header('location: meu_clan.php');return false;}
  $temp->ler($temp->nome_id($nome));
  if($temp->clan !== $db->clan)  {header('location: meu_clan.php');return false;}

  $temp->set_status_clan(1);
  header('location: meu_clan.php');
  return true;
}

function abandonar() {
  global $db;

  if($db->clan === null) {header('location: meu_clan.php');return false;}

  $db->abandonar_clan();
  header('location: meu_clan.php');
  return true;
}

function excluir() {
  $clan = new DB_clan;
  global $db;

  if($db->clan === null) {header('location: meu_clan.php');return false;}
  $clan->ler($db->clan);
  if((int)$clan->lider !== (int)$_SESSION['id']) {header('location: meu_clan.php');return false;}

  $clan->excluir();
  header('location: meu_clan.php');
  return true;
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

 function erro() {
  $retorno;
  if($_GET["erro"] == '') {return '';} // caso nao aja erro
  if($_GET["erro"] == 1) {
    $retorno = "<div class=\"alert alert-danger\" role=\"alert\"><strong>Problema!</strong> Nome inválido</div>\n";
  }
  if($_GET["erro"] == 2) {
    $retorno = "\n<div class=\"alert alert-danger\" role=\"alert\"><strong>Problema!</strong> Esse nome já está sendo usado</div>\n";
  }
  if($_GET["erro"] == 3) {
    $retorno = "\n<div class=\"alert alert-danger\" role=\"alert\"><strong>Um erro desconhecido ocorreu ao tentar criar o clan!</strong></div>\n";
  }
  if($_GET["erro"] == 4) {
    $retorno = "\n<div class=\"alert alert-danger\" role=\"alert\"><strong>O Clã está lotado!</strong></div>\n";
  }
  return $retorno;
 }
?>