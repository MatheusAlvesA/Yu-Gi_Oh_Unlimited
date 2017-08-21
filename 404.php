<?php
require_once 'libs/Mobile_Detect.php';
?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Yu-Gi-Oh Unlimited é um jogo totalmente online sem necessidade de download onde você pode formar seu deck do jeito que quiser totalmente de graça para em seguida duelar contra outros duelistas online no Brasil todo em um sistema de duelo automático. O jogo atualmente está em fase Alpha(Em teste).">
    <meta name="robots" content="index">
	<meta name="keywords" content="yugi,yugioh,jogar yugioh,yugioh grátis,grátis,yu-gi-oh,yugioh online,yugioh unlimited,yu-gi-oh game,yugioh jogo,yugioh jogo online,forbidden memories,yu-gi-oh online,yugioh rpg,duelo,duelar online"/>
    <link rel="icon" href="imgs/favicon.png">

    <title>Yu-Gi-Oh Unlimited</title>

    <link href="https://yugiohult.com.br/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.yugiohult.com.br/fonte.css" type="text/css" media="screen"/>
    <?php 
      $detectar = new Mobile_Detect;
      if(!$detectar->isMobile() || $detectar->isTablet()) echo '<link href="https://yugiohult.com.br/style_PC.css" rel="stylesheet"> <!--Estilos exclusivos quando aberto no computador-->';
      else echo '<link href="https://yugiohult.com.br/style.css" rel="stylesheet"> <!--Estilos personalizados-->';
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
            <li><a href="https://yugiohult.com.br/index.php">Principal</a></li>
            <li><a href="https://yugiohult.com.br/tutorial.php">Aprenda a jogar</a></li>
            <li><a href="desenvolvimento.php">Desenvolvimento</a></li>
            <li><a href="<https://yugiohult.com.br/sobre.php">Sobre</a></li>
          </ul>
        </div>
      </div>
    </nav>

<div class="container">
	<h1 style="text-align: center;">Página não encontrada</h1><hr>
	<p style="text-align: center; font-size: 12pt;">Não foi possível encontrar a página que você está procurando.<br> Verifique que a url foi digitada corretamente</p>
<?php // esse código insere propaganda na página
    global $G_PROPAGANDA;
    if($G_PROPAGANDA) echo '
<hr>
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

    <script src="https://yugiohult.com.br/bootstrap/jquery-3.1.1.min.js"></script>
    <script src="https://yugiohult.com.br/bootstrap/js/bootstrap.min.js"></script>
<?php echo file_get_contents('EOP.txt');?>
  </body>
</html>