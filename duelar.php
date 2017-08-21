<?php
require_once 'libs/tools_lib.php';
require_once 'libs/Mobile_Detect.php';
require_once 'libs/gravacao_lib.php';

$obj = new Tools();
$obj->verificar();

if(!file_exists('duelar_agora.txt')) file_put_contents ('duelar_agora.txt', "1\n0\n0\n0");
if(!file_exists('duelar_agora_desafios.txt')) file_put_contents('duelar_agora_desafios.txt', json_encode(array()));

$html = ''; // o corpo da página deve estar nessa variável
session_start();
if(isset($_GET['api'])) {
    if(!isset($_SESSION['deck_predef']) || !isset($_SESSION['id'])) echo 'rn';
    if(checar_duelo($_SESSION['id'])) {
        $_SESSION['id_duelo'] = checar_duelo($_SESSION['id']);
        $_SESSION['duelando'] = 'S';
        echo 'rs';
        exit();
    }
    $vetor = explode("\n", file_get_contents('duelar_agora.txt'));
    if($vetor[1] == $_SESSION['id']) {
        $vetor[2] = time();
        file_put_contents('duelar_agora.txt', $vetor[0]."\n".$vetor[1]."\n".$vetor[2]."\n".$vetor[3]);
        echo 'n';
    }
    else {
        if((int)$vetor[1] > 0) {
            if((int)(time()-$vetor[2]) > 3) {
                $vetor[1] = $_SESSION['id'];
                $vetor[2] = time();
                $vetor[3] = $_SESSION['deck_predef'];
                file_put_contents('duelar_agora.txt', $vetor[0]."\n".$vetor[1]."\n".$vetor[2]."\n".$vetor[3]);
                echo 'n';
            }
            else {
                $_SESSION['id_duelo'] = instanciar($_SESSION['id'], $vetor[1], (int)$vetor[3]);
                if($_SESSION['id_duelo'] === false) {
                    unset($_SESSION['id_duelo']);
                    echo 'rn';
                    exit();
                }
                $vetor[1] = 0;
                $vetor[2] = 0;
                $vetor[3] = 0;
                file_put_contents('duelar_agora.txt', $vetor[0]."\n".$vetor[1]."\n".$vetor[2]."\n".$vetor[3]);
                echo 'rs';
            }
        } else {
            $vetor[1] = $_SESSION['id'];
            $vetor[2] = time();
            $vetor[3] = $_SESSION['deck_predef'];
            file_put_contents('duelar_agora.txt', $vetor[0]."\n".$vetor[1]."\n".$vetor[2]."\n".$vetor[3]);
            echo 'n';
        }
    }
    exit();
}
if(isset($_GET['reset'])) {
    session_destroy();
    header('location: duelar.php');
    exit();
}
else if(isset($_GET['deck'])) { // o duelista está logando no sistema
    $deck = (int)$_GET['deck'];
    $vetor = json_decode(file_get_contents('predef_decks.txt'));
    if($deck <= 0 || $deck > count($vetor)) {header('location: duelar.php');exit();}
    //tudo checado logando o usuário no subsistema
    session_destroy();
    session_start();
    $_SESSION['deck_predef'] = ($deck-1);
    $arq = fopen('duelar_agora.txt', 'r');
    $_SESSION['id'] = (int)fgets($arq);
    $detect = new Mobile_Detect;
    if($detect->isMobile()) $_SESSION['mobile'] = 'S';
    
    $adv = (int)fgets($arq);
    $tempo = (int)fgets($arq);
    $escolha = (int)fgets($arq);
    fclose($arq);
    file_put_contents('duelar_agora.txt', ($_SESSION['id']+1)."\n".$adv."\n".$tempo."\n".$escolha);
    
    header('location: duelar.php');
} else { // o duelista não está logando no sistema agora
    if(isset($_SESSION['deck_predef'])) { // o duelista já está logado
        $html = '<div class="row">

<div role="main" class="col-md-6 col-md-push-3">
    <h1 class="oficial-font" style="text-align: center;">HORA DO DUELO !!!</h1><hr>
    <h3 style="text-align: center; font-family: sans;">ASSIM QUE OUTRO JOGADOR CLICAR EM DUELAR VOCÊS COMEÇARÃO A PARTIDA...</h3><img src="imgs/load.gif" width="50px" height="50px" style="float: right;" /><hr>
    <h4>Sinta-se a vontade para fazer outras coisas, apenas mantenha essa aba aberta e você será notificado quando uma partida começar.</h4>
    <a href="duelar.php?reset=1"><button class="btn-lg btn-primary">MUDAR DECK</button></a>
</div>

</div>';
    } else { // o duelista não está logado #caso mais básico
        $html = '<div class="row">

<div role="main" class="col-md-6 col-md-push-3">
    <h1 class="oficial-font" style="text-align: center;">HORA DO DUELO !!!</h1><hr>
    <h3 style="text-align: center; font-family: sans;">ESCOLHA UM DECK QUE COMBINE COM VOCÊ...</h3><hr>
    <div class="row" id="decks">
        
        <div class="col-md-4" id="coluna_decks_0"></div>
        
        <div class="col-md-4" id="coluna_decks_1"></div>
        
        <div class="col-md-4" id="coluna_decks_2"></div>
        
    </div>
</div>

</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Yu-Gi-Oh Unlimited é um jogo totalmente online sem necessidade de download onde você pode formar seu deck do jeito que quiser totalmente de graça para em seguida duelar contra outros duelistas online no Brasil todo em um sistema de duelo automático. O jogo atualmente está em fase Alpha(Em teste).">
    <meta name="robots" content="index">
	<meta name="keywords" content="yugi,yugioh,jogar yugioh,yugioh grátis,grátis,yu-gi-oh,yugioh online,yugioh unlimited,yu-gi-oh game,yugioh jogo,yugioh jogo online,forbidden memories,yu-gi-oh online,yugioh rpg,duelo,duelar online"/>
    <link rel="icon" href="imgs/favicon.png">

    <title>Yu-Gi-Oh Unlimited</title>

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fonte.css" type="text/css" media="screen"/>
    <link href="style.css" rel="stylesheet"> <!--Estilos personalizados-->
    <?php 
    	$detectar = new Mobile_Detect;
    	if(!$detectar->isMobile() || $detectar->isTablet()) echo '<link href="style_PC.css" rel="stylesheet"> <!--Estilos exclusivos quando aberto no computador-->';
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
            <li><a href="index.php">Principal</a></li>
            <li><a href="tutorial.php">Aprenda a jogar</a></li>
            <li><a href="desenvolvimento.php">Desenvolvimento</a></li>
            <li><a href="sobre.php">Sobre</a></li>
          </ul>
        </div>
      </div>
    </nav>

<?php echo $html;?>

<hr>
<footer class="row">
	<div class="col-md-4 col-md-push-4">
		<?php include 'rodape.txt';?>
	</div>
</footer>

    <script src="bootstrap/jquery-3.1.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
 <?php if(isset($_SESSION['deck_predef'])) echo '   <script>
        window.setInterval(\'consultar()\', 3000);
        var redir = false;
        function consultar() {
            $.ajax({
              type: \'get\',
              data: \'api\',
              url:\'duelar.php\',
              success: function(retorno){
                  if(redir === false) {
                    if(retorno === "rs") {
                        alert("Duelo iniciado");
                        window.location.href = "/ZD/duelo.php";
                        redir = true;
                    }
                    if(retorno === "rn") {
                        window.location.href = "duelar.php?reset=1";
                        redir = true;
                    }
                  }
              }
            });
        }
    </script>';
else echo '
    <script>
        var decks = JSON.parse(\''.file_get_contents("predef_decks.txt").'\');
        var html_1 = "";
        var html_2 = "";
        var html_3 = "";
        for(var x = 0; x < decks.length; x++) {
            switch(x%3) {
                case 0:
                    html_1 += renderizar_deck(x,decks[x]["nome"],decks[x]["descricao"],decks[x]["componentes"]);
                break;
                case 1:
                    html_2 += renderizar_deck(x,decks[x]["nome"],decks[x]["descricao"],decks[x]["componentes"]);
                break;
                case 2:
                    html_3 += renderizar_deck(x,decks[x]["nome"],decks[x]["descricao"],decks[x]["componentes"]);
                break;
            }
        }
        $("#coluna_decks_0").html(html_1);
        $("#coluna_decks_1").html(html_2);
        $("#coluna_decks_2").html(html_3);
    function renderizar_deck(n,nome,descript,componentes) {
        return "<a href=\"duelar.php?deck="+(n+1)+"\"><div id=\"deck_"+n+"\" title=\""+descript+"\" style=\"width: 100%; cursor: pointer; text-align: center;\"><b style=\"color: black;\">"+nome+"</b><div id=\"imgs_deck_"+n+"\" style=\"width: 150px; height: 140px; position: relative; left: 15%;\"><img src=\"imgs/cards/pequenas/"+componentes[0]+".png\" style=\"position: absolute; left: 0; width: 60%; height: 100%; z-index: 5;\" onmouseover=\"this.style.zIndex = 6;\" onmouseout=\"this.style.zIndex = 5;\" /><img src=\"imgs/cards/pequenas/"+componentes[1]+".png\" style=\"position: absolute; left: 10%; width: 60%; height: 100%; z-index: 4;\" onmouseover=\"this.style.zIndex = 6;\" onmouseout=\"this.style.zIndex = 4;\" /><img src=\"imgs/cards/pequenas/"+componentes[2]+".png\" style=\"position: absolute; left: 20%; width: 60%; height: 100%; z-index: 3;\" onmouseover=\"this.style.zIndex = 6;\" onmouseout=\"this.style.zIndex = 3;\" /><img src=\"imgs/cards/pequenas/"+componentes[3]+".png\" style=\"position: absolute; left: 30%; width: 60%; height: 100%; z-index: 2;\" onmouseover=\"this.style.zIndex = 6;\" onmouseout=\"this.style.zIndex = 2;\" /><img src=\"imgs/cards/pequenas/"+componentes[4]+".png\" style=\"position: absolute; left: 40%; width: 60%; height: 100%; z-index: 1;\" onmouseover=\"this.style.zIndex = 6;\" onmouseout=\"this.style.zIndex = 1;\" /></div></div></a>";
    }
    </script>
  ';
?>
<?php echo file_get_contents('EOP.txt');?>
  </body>
</html>
<?php
function instanciar($duelista, $oponente, $deck_oponente) {
  $vetor['duelista'] = (int)$duelista;
  $vetor['oponente'] = (int)$oponente;
  $vetor['time'] = time();
  $vetor['id'] = 'temp_'.uniqid();
  $decks = json_decode(file_get_contents('predef_decks.txt'),true);
  
  // Iniciando checagens
  if($vetor['duelista'] <= 0 || $vetor['oponente'] <= 0 ) return false;
  if(!isset($decks[$deck_oponente]) || !isset($decks[$_SESSION['deck_predef']])) return false;
  //fim das checagens
      
  $_SESSION['duelando'] = 'S';
  $_SESSION['id_duelo'] = $vetor['id'];

 mkdir('ZD/duelos/'.$vetor['id'], 0770);
 fclose(fopen('ZD/duelos/'.$vetor['id'].'/metadata.txt', 'w'));
  $grav = new Gravacao();
  $grav->set_caminho('ZD/duelos/'.$vetor['id'].'/metadata.txt');
  $array_temp[0][0] = 4;
  $array_temp[0][1] = 1;
  $array_temp[0][2] = 2;
  $array_temp[0][3] = 2;
  $array_temp[1][0] = time();
  $array_temp[2][0] = $vetor['duelista'];
  $array_temp[2][1] = $vetor['oponente'];
  $temp[0] = $vetor['duelista'];
  $temp[1] = $vetor['oponente'];
  $array_temp[3][0] = $temp[rand(0, 1)];
  $array_temp[3][1] = 1;
  $grav->set_matriz($array_temp);
  $grav->gravar();
  unset($grav);

  $d_deck = array();
  $o_deck = array();
  shuffle($decks[$_SESSION['deck_predef']]['componentes']);
  shuffle($decks[$deck_oponente]['componentes']);
  
  $d_deck[0] = 0;
  for($loop = 0; $loop < count($decks[$_SESSION['deck_predef']]['componentes']);$loop++) {
    $d_deck[$loop+1] = $decks[$_SESSION['deck_predef']]['componentes'][$loop];
  }
  $d_deck[0] = count($d_deck);
  
  $o_deck[0] = 0;
  for($loop = 0; $loop < count($decks[$deck_oponente]['componentes']);$loop++) {
    $o_deck[$loop+1] = $decks[$deck_oponente]['componentes'][$loop];
  }
  $o_deck[0] = count($o_deck);
  
  // setando a pasta do duelista
  mkdir('ZD/duelos/'.$vetor['id'].'/'.$vetor['duelista'], 0770);
  $grav = new Gravacao();
  $grav->set_caminho('ZD/duelos/'.$vetor['id'].'/'.$vetor['duelista'].'/deck.txt');
  $grav->set_array($d_deck);
  $hand[0] = 6;
  for($x = 1; $x <= 5; $x++) {$hand[$x] = $d_deck[$x]; $grav->apagar(1);}
  $grav->gravar();
  unset($grav);
  $grav = new Gravacao();
  $grav->set_caminho('ZD/duelos/'.$vetor['id'].'/'.$vetor['duelista'].'/hand.txt');
  $grav->set_array($hand);
  $grav->gravar();
  unset($grav);
  fclose(fopen('ZD/duelos/'.$vetor['id'].'/'.$vetor['duelista'].'/cemitery.txt', 'w'));
  $alp = fopen('ZD/duelos/'.$vetor['id'].'/'.$vetor['duelista'].'/lps.txt', 'w');
  fwrite($alp, '8000');
  fclose($alp);
  $arq = fopen('ZD/duelos/'.$vetor['id'].'/'.$vetor['duelista'].'/campo.txt', 'w');
  fwrite($arq, "0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0");
  fclose($arq);
 // setando a pasta do oponente
  mkdir('ZD/duelos/'.$vetor['id'].'/'.$vetor['oponente'], 0770);
  $grav = new Gravacao();
  $grav->set_caminho('ZD/duelos/'.$vetor['id'].'/'.$vetor['oponente'].'/deck.txt');
  $grav->set_array($o_deck);
  $hand[0] = 6;
  for($x = 1; $x <= 5; $x++) {$hand[$x] = $o_deck[$x]; $grav->apagar(1);}
  $grav->gravar();
  unset($grav);
  $grav = new Gravacao();
  $grav->set_caminho('ZD/duelos/'.$vetor['id'].'/'.$vetor['oponente'].'/hand.txt');
  $grav->set_array($hand);
  $grav->gravar();
  unset($grav);
  fclose(fopen('ZD/duelos/'.$vetor['id'].'/'.$vetor['oponente'].'/cemitery.txt', 'w'));
  $alp = fopen('ZD/duelos/'.$vetor['id'].'/'.$vetor['oponente'].'/lps.txt', 'w');
  fwrite($alp, '8000');
  fclose($alp);
  $arq = fopen('ZD/duelos/'.$vetor['id'].'/'.$vetor['oponente'].'/campo.txt', 'w');
  fwrite($arq, "0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0\n0");
  fclose($arq);
  
  $duelos = json_decode(file_get_contents('duelar_agora_desafios.txt'));
  $duelos[count($duelos)] = $vetor;
  file_put_contents('duelar_agora_desafios.txt', json_encode($duelos));
  
  return $vetor['id'];
}

function checar_duelo($duelista) {
    $vetor = json_decode(file_get_contents('duelar_agora_desafios.txt'),true);
    $novo = array();
    $x = 0;
    $y = 0;
    while($x < count($vetor)) {
        if((time()-$vetor[$x]['time']) <= 10) {
            $novo[$y++] = $vetor[$x];
            if($vetor[$x]['duelista'] == $duelista || $vetor[$x]['oponente'] == $duelista) return $vetor[$x]['id'];
        }
        $x++;
    }
    file_put_contents('duelar_agora_desafios.txt', json_encode($novo));
    return false;
}
?>