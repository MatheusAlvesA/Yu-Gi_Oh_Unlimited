<?php
include("libs/tools_lib.php");
require_once 'libs/Mobile_Detect.php';

$tools = new Tools();
$tools->verificar();
?>
<!DOCTYPE html>
<html lang="pt">
  <head> <?php include 'head.txt';?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="imgs/favicon.png">
    <title>Tutorial Yu-Gi-Oh Unlimited</title>
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
            <li><a href="index.php">Principal</a></li>
            <li class="active"><a href="tutorial.php">Aprenda a jogar</a></li>
            <li><a href="desenvolvimento.php">Desenvolvimento</a></li>
            <li><a href="sobre.php">Sobre</a></li>
          </ul>
        </div>
      </div>
    </nav>

<div class="row">

    <div role="main" class="col-md-6 col-md-push-3">
        <h1 style="color: blue; text-align: center;">TUTORIAL</h1>
        <table border = "1" width = "100%">
            <tr>
                <td align="center">
                    <b>Esse tutorial se dedica apenas a ensinar os jogadores como usar o site, as regras do fã game Yu-Gi-Oh não serão ensinadas aqui.</b>
                </td>
            </tr>
        </table>
        <hr />
        <img style="border: 1px solid red;" src="imgs/tutorial_1.png" height="90%" width="100%" />
        <table border = "1" width = "100%">
            <tr>
                <td align="center"><b style="color: green;">
                    #1</b> O nome que você escolheu no momento do cadastro
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#2</b> A quantidade de XP que você possui. XP pode ser adquirido em duelos
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#3</b> Aqui é onde você poderá encontrar outros duelistas, em um chat, assim como tambem poderá desafialos para duelos.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#4</b> Aqui você poderá editar seu deck e o seu inventário. No inventário é onde ficam suas cartas compradas, mas que não seram usadas no duelo, coloque a carta no deck para só então poder usala.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#5</b> O comércio é onde você poderá comprar cartas que estaram destacadas em diferentes cores, cada cor representa um tipo de carta.
                    <b style="color: yellow; background-color: black;">Amarela</b> para monstros normais;
                    <b style="color: #FF9933; background-color: black;">Laranja</b> para monstros com efeito;
                    <b style="color: purple; background-color: black;">roxo</b> para monstros do tipo fusão;
                    <b style="color: blue; background-color: black;">azul</b> para monstros do tipo ritual;
                    <b style="color: green; background-color: black;">verde</b> para mágicas;
                    <b style="color: pink; background-color: black;">rosa</b> para armadilhas.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#6</b> O ranking do melhores jogadores ordenado por <b>XP</b>
                </td>
            </tr>
        </table>
        <hr />
        <img style="border: 1px solid red;" src="imgs/tutorial_2.png" height="90%" width="100%" />
        <table border = "1" width = "100%">
            <tr>
                <td align="center">
                    <b style="color: green;">#1</b> Aqui você poderá encontrar duelistas e adicionalos a sua lista de amigos, poderá tambem enviar mensagens para ele.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#2</b> As mensagens que outros duelistas enviaram pra você, caso haja um nova mensagem este botão irá mudar sua aparencia avisado da nova mensagem não lida.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#3</b> Neste menu você poderá visualizar suas informações pessoais e <b>alterar sua senha.</b>
                </td>
            </tr>
        </table>
    </div>

    <aside role="complementary" class="col-md-3 col-md-push-3">
        <hr>
        <table border = "1" width = "100%">
            <tr>
                <td align="center">
                    <b style="color: red;">DUELANDO NO CELULAR</b>
                </td>
            </tr>
        </table>
        <img style="border: 1px solid red;" src="imgs/tutorial_3.png" height="90%" width="100%" />
        <table border = "1" width = "100%">
            <tr>
                <td align="center">
                    <b style="color: green;">#1</b> Na primeira linha você vai encontrar 3 mostradores que são: nessa ordem, o tempo que falta para sua vez acabar; o botão para seguir pra a proxima fase, lembrando que no jogo Yu-Gi-Oh existem várias fases de ação, sendo as principais delas: <b>Main Phase 1, Battle Phase, e Main Phase 2</b>. Apertando esse botão você passará por cada fase, e quando passar da Main Phase 2<b>(MF2</b>) sua vez terminará; e por ultimo o botão <b>VS</b> que serve para <b>visualizar o campo inimigo.</b>
                </td>
            </tr>
            <tr>
                <td align="center"><b style="color: green;">#2</b> Aqui são mostradas informações gerais do seu oponente e logo abaixo suas informações gerais, são elas: nessa ordem, Life Points; carta magica do tipo campo; quantidade de cartas no cemitério<b>(quando precionado mostra as cartas no cemitério)</b>; quantidade de cartas no deck.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#3</b> Aqui ficam seus monstros invocados, quando um monstro é precionado uma janela com informações daquele monstro aparece, um botão<b>(usar carta)</b> tambem aparece e poderá ser usado para realizar ações com aquele monstro.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#4</b> Aqui ficam suas mágicas e armadilhas invocadas, quando uma carta é precionada uma janela com informações daquela carta aparece, um botão<b>(usar carta)</b> tambem aparece e poderá ser usado para realizar ações com aquela carta.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#5</b> Essas são as cartas que estão na sua mão, a cada começo de turno você puxa uma carta autoaticamente, precionando uma delas aparecerá uma janela com informações e você poderá invocar a carta.
                </td>
            </tr>
        </table>
        <hr />
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

<div class="row">
    <div class="col-md-9 col-md-push-3">
        <table border = "1" width = "100%">
            <tr>
                <td align="center">
                    <b style="color: red;">DUELANDO NO COMPUTADOR</b>
                </td>
            </tr>
        </table>
        <img style="border: 1px solid red;" src="imgs/tutorial_4.png" height="90%" width="100%" />
        <table border = "1" width = "100%">
            <tr>
                <td align="center">
                    <b style="color: green;">#1</b> Na primeira linha você vai encontrar 3 mostradores que são: nessa ordem, o tempo que falta para sua vez acabar; o botão para seguir pra a proxima fase, lembrando que no jogo Yu-Gi-Oh existem várias fases de ação, sendo as principais delas: <b>Main Phase 1, Battle Phase, e Main Phase 2</b>. Apertando esse botão você passará por cada fase, e quando passar da Main Phase 2<b>(MF2</b>) sua vez terminará; e por ultimo o mostrador de cartas do inimigo, primeiro: número de cartas na mão, e segundo: número de cartas no cemitério do oponente. Clicando no numero de cartas do oponente voçê visualiza as cartas que estão lá.
                </td>
            </tr>
            <tr>
                <td align="center"><b style="color: green;">#2</b> Aqui são mostradas informações da carta que voçê selecionou passando o mouse em cima ou clicando.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#3</b> Aqui ficam seus monstros invocados, quando um monstro é precionado uma janela com informações daquele monstro aparece, um botão<b>(usar carta)</b> tambem aparece e poderá ser usado para realizar ações com aquele monstro.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#4</b> Aqui ficam suas mágicas e armadilhas invocadas, quando uma carta é precionada uma janela com informações daquela carta aparece, um botão<b>(usar carta)</b> tambem aparece e poderá ser usado para realizar ações com aquela carta.
                </td>
            </tr>
            <tr>
                <td align="center">
                    <b style="color: green;">#5</b> Essas são as cartas que estão na sua mão, a cada começo de turno você puxa uma carta autoaticamente, precionando uma delas aparecerá uma janela com informações e você poderá invocar a carta.
                </td>
            </tr>
        </table>
        <hr />
    </div>
</div>

<div class="row">
    <div class="col-md-9 col-md-push-3">
        <table border = "1" width = "100%">
            <tr>
                <td align="center">
                    <b style="color: blue;">O MAPA DO JOGO</b>
                </td>
            </tr>
        </table>
        <img style="border: 1px solid red;" src="imgs/mapa.jpg" height="90%" width="100%" />
        <table border = "1" width = "100%">
            <tr>
                <td align="center">
                    O mapa do jogo se resume a um sistema RPG 2D onde seu personagens escolhido no cadastro irá "spawnar". Cada área do mapa corresponde a um reino, são quatro zonas e você spawna na correspondente ao seu reino. Note que as bordas do mapa mudam de acordo com a zona onde você está. se estiver em zona neutra você poderá duelar ou não contra outros duelistas, se estiver em sua própria zona o duelista de outra zona é obrigado a duelar caso você o desafie. Você só pode desafiar duelistas que estejam imediatamente ao seu lado, ou seja, a um passo de distância.
                </td>
            </tr>
            <tr>
                <td align="center">
                	Existem dois tipos de duelo, o comum e o de <b>honra</b>, o duelo de honra só ocorre quando você enfrenta um duelista no reino dele ou o enfrenta em seu próprio reino. Um duelo comum dá 2 pontos de reino para o ganhador e um para o perdedor. O duelo de honra dá 3 pontos de reino para o ganhador e um para o perdedor.
                </td>
            </tr>
            <tr>
                <td align="center">
                    Os pontos de reino são somados e mostrados na página inicial indicando qual o reino é mais "poderoso", o jogador com mais pontos de reino irá se tornar o lider do reino. Os pontos são resetados no começo de cada mês.
                </td>
            </tr>
        </table>
        <hr />
    </div>
</div>

<div class="row">
    <div class="col-md-9 col-md-push-3">
        <table border = "1" width = "100%">
            <tr>
                <td align="center">
                    <b style="color: purple;">SISTEMA DE CLÃS</b>
                </td>
            </tr>
        </table>
        <table border = "1" width = "100%">
            <tr>
                <td align="center">
                    No botão "CLÃ" você encontra as opções de criar/buscar clãs, entrando ou criando um clã você agora contribui para o clã cada vez que duela, se vencer ganha dois pontos para o clã se perder apenas um. A soma de todos os pontos dos duelistas de cada clã é o que determina a classificação desse clã no ranking.
                </td>
            </tr>
        </table>
        <hr />
        <table border = "1" width = "100%">
            <tr>
                <td align="center">Terminou? então... <b style="color: red;">É HORA DO DUELO!</b></td>
            </tr>
        </table>
        <hr />
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