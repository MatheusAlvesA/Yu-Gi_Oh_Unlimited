<?php
include_once("../libs/tools_lib.php");
include_once("libs/loby_lib.php");
include_once("../libs/db_lib.php");
include_once("../libs/Mobile_Detect.php");

$tools = new Tools(true);
$tools->verificar();
$tools->verificarlog();
//verificar se o usuario esta logado
if($_GET['msg'] != '') {enviar($_GET['msg']); exit();}

$db = new DB;
$db->ler($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="../imgs/favicon.png">
    <title>Yu-Gi-Oh Unlimited</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../fonte.css" type="text/css" media="screen"/>
    <?php 
      $detectar = new Mobile_Detect;
      if(!$detectar->isMobile() || $detectar->isTablet()) echo '<link href="../style_PC.css" rel="stylesheet"> <!--Estilos exclusivos quando aberto no computador-->';
      else echo '<link href="../style.css" rel="stylesheet"> <!--Estilos personalizados-->';
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
            <li id="caixa_user_data_nxp">
              <span id="user_data_nxp"><b><?php echo $db->nome;?></b> <?php echo $db->xp;?>XP</span>
              <a href="index.php?sair=1" id="BNTdeslogar_ancora"><span id="BNTdeslogar" class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span></a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">

 <div role="main" class="col-md-6 col-md-push-3">
  <div class="row">
    <div id="DivChat" class="col-md-12" style="overflow: auto; width: 100%; height: 500px; border: 1px solid black; position: relative; top: 5px;"></div>
    <table border = "1" width = "100%" style="position: relative; top: 10px;" class="col-md-10">
      <tr>
        <td>
          <textarea id="txtarea" name="msg" rows="4" style="width: 100%;"></textarea>
        </td>
        <td align="center">
          <img src="../imgs/send.png" style="width: 50px; height: 50px;" onClick="enviar()" />
        </td>
      </tr>
    </table>
  </div>
</div>

  <aside role="complementary" class="col-md-3 col-md-push-3">
  <h1 class="oficial-font" style="text-align: center;" id="palavra">ONLINES</h1>
  <?php echo aviso();?>
  <div id="DivList" style="overflow: auto;"></div>
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
  <script src="../bootstrap/jquery-3.1.1.min.js"></script>
  <script type="text/javascript">
    mostrar();
    mostrarchat();
    window.setInterval('mostrar()', 3000);
    window.setInterval('mostrarchat()', 3000);

    function mostrar(){
      $.ajax({
        type: 'get',
        data: 'users=1',
        url:'data.php',
        success: function(retorno){
          if(retorno == "r") {window.location.href = "/ZD/desafio.php";}
          else $('#DivList').html(retorno);  
        }
      })
    }

  function mostrarchat(retornado = null) {
    if(retornado !== null) {
      if(retornado != "r") $('#DivChat').html(retornado);
      return;
    }
      $.ajax({
        type: 'get',
        data: 'chat=1',
        url:'data.php',
        success: function(retorno){
          mostrarchat(retorno);
        }
      });
  }

  function enviar() {
      $.ajax({
        type: 'get',
        data: 'msg='+ document.getElementById('txtarea').value,
        url:'loby.php',
      })
    document.getElementById('txtarea').value = '';
  }
</script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
<?php echo file_get_contents('../EOP.txt');?>
  </body>
</html>
<?php
function enviar($msg) {
  session_start();
  $db = new DB();
  $db->ler($_SESSION['id']);
  $chat = new Chat();
  $chat->enviar($db->nome, $msg);
  unset($db);
  unset($chat);
}
function aviso() {
  if($_GET['desr']) {return '<table border="1" width="100%"><tr><td align="center"><b style="color: red">Duelo recusado</b></td></tr></table>';}
  if($_GET['ndes']) {return '<table border="1" width="100%"><tr><td align="center"><b style="color: red">Nenhum desafio</b></td></tr></table>';}
  if($_GET['duelc']) {return '<table border="1" width="100%"><tr><td align="center"><b style="color: red">Duelo cancelado</b></td></tr></table>';}
  if($_GET['usero']) {return '<table border="1" width="100%"><tr><td align="center"><b style="color: red">Duelista indisponível</b></td></tr></table>';}
  return '';
}
?>