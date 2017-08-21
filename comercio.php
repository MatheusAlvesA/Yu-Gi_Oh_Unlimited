<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include_once("libs/cards_lib.php");
include_once("libs/gravacao_lib.php");
include("libs/Mobile_Detect.php");

$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$db = new DB();
$db->ler($_SESSION["id"]);

$erro = '';
if($_GET['serverl']) {$erro = "  <div class=\"alert alert-danger\" role=\"alert\">
      <strong>Problema!</strong> O servidor está cheio
  </div>";}

$lista = listar();
if(!$tools->verificaridade($_GET["pagina"]) || $lista[0] <= ($_GET["pagina"] * 50) + 1) {$pagina = '';}
else {$pagina  = $_GET["pagina"];}
if($pagina == 0) {$pagina = '';}
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Comércio Yu-Gi-Oh Unlimited</title>
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
      <form action="comercio.php" method="get">
        <div class="input-group h2">
            <input name="buscar" class="form-control" id="search" type="text" placeholder="Pesquisar Cartas">
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
                    <th>Tipo</th>
                    <th>Preço</th>
                    <th class="actions">Ações</th>
                 </tr>
            </thead>
            <tbody>
 
                <?php echo cards($lista, $pagina);?>
 
            </tbody>
         </table>
     </div>
      <?php echo paginacao($lista, $pagina);?>
    </div> <!-- /#lista -->

  </div>

  <aside role="complementary" class="col-md-3 col-md-push-3">
  <?php echo $erro;?>
    <hr><a href="mapa.php"><img src="imgs/explorar_mapa.png" class="img-responsive" /></a><hr>
    <a href="ZD/index.php"><img src="imgs/B_duelos.png" class="img-responsive" /></a><hr>
    <a href="deck.php"><img src="imgs/B_Mdeck.png" class="img-responsive" /></a><hr>
    <a href="inventario.php"><img src="imgs/B_Minventario.png" class="img-responsive" /></a><hr>
    <a href="iedeck.php"><img src="imgs/iedeck.png" class="img-responsive" /></a><hr>
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

<script language="JavaScript">
function abrir(URL) {
 
  var width = 350;
  var height = 500;
 
  var left = 200;
  var top = 100;
 
  window.open(URL,'Info', 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=no, fullscreen=no');
 
}
</script>
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

 function cards($amigos, $pagina) { // retorna a lista de amigos
  if($amigos[0] <= 1) {$retorno = "<tr><td colspan=\"2\"><b>Nenhuma carta</b></td></tr>";}
  // caso não precise paginar
  if($amigos[0] <= 51) {
    $x = 1;
    $db_temp = new DB_cards();
    while($x < $amigos[0]) {
      $db_temp->ler($amigos[$x]); // ler quem e o amigo
      $retorno = $retorno."<tr>
                    <td>".$db_temp->nome."</td>
                    <td>".card_cor($db_temp)."</td>
                    <td>Grátis</td>
                    <td class=\"actions\">
                        <a class=\"btn btn-success btn-sm\" href=\"javascript:abrir('info_card.php?fonte=3&id=".$db_temp->id."')\">Visualizar</a>
                        <a class=\"btn btn-primary btn-sm\" href=\"javascript:abrir('info_card.php?fonte=3&act=1&id=".$db_temp->id."')\">Comprar</a>
                    </td>
                </tr>\n";
      $x++;
    }
   return $retorno;
  }
  // caso precise paginar e a pagina seja a primeira
  elseif($pagina == '') { // se tiver mais de 10 amigos
   $x = 1;
   $db_temp = new DB_cards();
   while($x < 51) { // loop que gera a lista de amigos
    $db_temp->ler($amigos[$x]); // ler quem e o amigo
    $retorno = $retorno."<tr>
                    <td>".$db_temp->nome."</td>
                    <td>".card_cor($db_temp)."</td>
                    <td>Grátis</td>
                    <td class=\"actions\">
                        <a class=\"btn btn-success btn-sm\" href=\"javascript:abrir('info_card.php?fonte=3&id=".$db_temp->id."')\">Visualizar</a>
                        <a class=\"btn btn-primary btn-sm\" href=\"javascript:abrir('info_card.php?fonte=3&act=1&id=".$db_temp->id."')\">Comprar</a>
                    </td>
                </tr>\n";
    $x = $x + 1;
   }
  }
  // caso precise paginar e a pagina não seja a primeira
  if($pagina >= 1) {
   $x = ($pagina * 50) + 1;
   $db_temp = new DB_cards();
   for($y = 0;$y < 50;$y++) {
    if($x < $amigos[0]) {
     $db_temp->ler($amigos[$x]); // ler quem e o amigo
     $retorno = $retorno."<tr>
                    <td>".$db_temp->nome."</td>
                    <td>".card_cor($db_temp)."</td>
                    <td>Grátis</td>
                    <td class=\"actions\">
                        <a class=\"btn btn-success btn-sm\" href=\"javascript:abrir('info_card.php?fonte=3&id=".$db_temp->id."')\">Visualizar</a>
                        <a class=\"btn btn-primary btn-sm\" href=\"javascript:abrir('info_card.php?fonte=3&act=1&id=".$db_temp->id."')\">Comprar</a>
                    </td>
                </tr>\n";
     $x++;
    }
   }
  }
  return $retorno;
 }

function npaginas($lista) {
  $n = $lista[0]-1;
  $div = $n/50;
  $mod = $n%50;

  if($mod > 0) return $div+1;
  else return $div;
}
 function paginacao($amigos, $pagina) { // retorna a lista de amigos
  if($amigos[0] <= 51) {return '';}
  // caso precise paginar e a pagina seja a primeira
  if($pagina == '') {
    $retorno = '<div id="bottom" class="row">
        <div class="col-md-12">
         
        <ul class="pagination">
            <li class="disabled"><a>&lt; Anterior</a></li>
            <li class="disabled"><a>1</a></li>';

   $x = 2; // a primeira já foi listada
   while($x < npaginas($amigos)) { // loop que gera a lista de amigos
    $retorno = $retorno."\n<li><a href=\"comercio.php?pagina=".($x-1)."\">".$x."</a></li>\n";
    $x = $x + 1;
   }

    $retorno = $retorno."
            <li class=\"next\"><a href=\"comercio.php?pagina=1\" rel=\"next\">Próximo &gt;</a></li>
        </ul><!-- /.pagination -->
 
      </div>
     </div> <!-- /#rodape -->\n";
  }
  // caso precise paginar e a pagina não seja a primeira
  if($pagina >= 1) {
       $retorno = '<div id="bottom" class="row">
        <div class="col-md-12">
         
        <ul class="pagination">
            <li><a href="comercio.php?pagina='.($pagina-1).'">&lt; Anterior</a></li>';

   $x = 1; // a primeira já foi listada
   while($x < npaginas($amigos)) { // loop que gera a lista de amigos
    if($x-1 != $pagina) $retorno = $retorno."\n<li><a href=\"comercio.php?pagina=".($x-1)."\">".$x."</a></li>\n";
    else $retorno = $retorno."\n<li class=\"disabled\"><a href=\"comercio.php?pagina=".($x-1)."\">".$x."</a></li>\n";
    $x = $x + 1;
   }

    if($pagina >= ($amigos[0]/50)-1) { // essa é a ultima página
      $retorno = $retorno."
            <li class=\"next disabled\"><a href=\"comercio.php?pagina=".($pagina+1)."\" rel=\"next\">Próximo &gt;</a></li>
        </ul><!-- /.pagination -->
 
      </div>
     </div> <!-- /#rodape -->\n";
   }
   else {
      $retorno = $retorno."
            <li class=\"next\"><a href=\"comercio.php?pagina=".($pagina+1)."\" rel=\"next\">Próximo &gt;</a></li>
        </ul><!-- /.pagination -->
 
      </div>
     </div> <!-- /#rodape -->\n";
   }
  }
  return $retorno;
 }

function listar() {
$temp = new Gravacao();
$temp->set_caminho('bd_cards.txt');
$matriz = $temp->ler(1);
unset($temp);
$temp = new DB_cards();
$x = 1;
$array[0] = '.....';
 while($x < $matriz[0][0]) {
    $temp->ler_id($matriz[$x][0]);
    if($temp->id != 13 && ($temp->filtro() || $temp->tipo == 'fusion' || $temp->tipo == 'fusion-effect')) {$array[$x] = $matriz[$x][2];}
    $x++;
 }
sort($array);
$array[0] = count($array);

if($_GET['buscar'] != '') { // filtrando o resultado caso nescesário
  $refinado[0] = 1;
  for($x = 1; $x < $array[0] && $refinado[0] <= 41; $x++) {
    if(str_igual($_GET['buscar'], $array[$x]) == 1) {
      $refinado[$refinado[0]] = $array[$x];
      $refinado[0]++;
    }
    elseif(str_igual($_GET['buscar'], $array[$x]) == 2) {
      if($refinado[0] > 1) $refinado[$refinado[0]] = $refinado[1];
      $refinado[1] = $array[$x];
      $refinado[0]++;
    }
  }
  return $refinado;
}

return $array;
}

function card_cor($carta) {
if($carta->categoria == 'monster') {
 switch($carta->tipo) {
  case 'normal':
   return '<b style="color: #c6c904">Monstro normal</b>';
   break;
  case 'effect':
   return '<b style="color: #FF9933">Monstro efeito</b>';
  break;
  case 'fusion':
   return '<b style="color: purple">Monstro fusão</b>';
  break;
  case 'fusion-effect':
   return '<b style="color: purple">Monstro fusão/efeito</b>';
  break;
  case 'ritual':
   return '<b style="color: #99FFFF">Monstro ritual</b>';
  break;
  case 'ritual-effect':
   return '<b style="color: #99FFFF">Monstro ritual/efeito</b>';
  break;
 }
}
elseif($carta->categoria == 'spell') {return '<b style="color: green">Mágica</b>';}
elseif($carta->categoria == 'trap') {return '<b style="color: pink">Armadilha</b>';}
else {return 'style="background-color: red"';}
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
?>