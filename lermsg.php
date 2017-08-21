<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/msg_lib.php");
$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$msg_temp = new MSG();
$msgs = $msg_temp->ler($_SESSION["id"]);
unset($msg_temp);

if(!$tools->verificaridade($_GET["id"]) || $_GET["id"] <= 0) {header("location: mensagens.php");exit();}
if($msgs[0][0] <= 0 || $_GET["id"] > $msgs[0][0]) {header("location: mensagens.php");exit();}

?>
<html>
<head>
<?php include 'head.txt';?>
<title>Mensagens Yu-Gi-Oh Unlimited</title>
</head>
<body <?php echo $tools->fundo;?> >
<img src="<?php echo $tools->capa;?>" height="<?php echo $tools->rcapa;?>" width="100%" />
<table border = "1" width = "100%">
<tr><td align="center"><?php echo usuario($msgs[$_GET["id"]][1]);?></td></tr>
<tr><td><?php echo mensagen($_GET["id"]);?></td></tr>
<tr><td align="center"><a href="enviarmsg.php?nome=<?php echo susuario($msgs[$_GET["id"]][1]);?>"><b>Responder</b></a></td></tr>
<tr><td align="center"><a href="javascript:apagar('<?php echo $_GET["id"];?>')"><b>Excluir</b></a></td></tr>
</table>
<?php include 'rodape.txt';?>
<script type="text/javascript" src="ZD/libs/php.js"></script>
<script type="text/javascript">
  function apagar(id) {
    file_get_contents('apagarmsg.php?id='+id);
    window.opener.location.href='mensagens.php?apagada=1';
    window.close();
  }
</script>
<?php echo file_get_contents('EOP.txt');?>
</body>
</html>
<?php
 function mensagen($id) { // retorna a lista de mensagens
  $msg = new MSG();
  $msgs = $msg->ler($_SESSION["id"]);
  unset($msg);

  $x = 1;
  while($x <= $msgs[$id][0]) {
   $retorno = $retorno.$msgs[$id][$x + 1]." ";
   $x++;
  }
  $retorno = substr($retorno, 0, -1);
   
  return $retorno;
 }
 
 function usuario($id) {
  $bd = new DB();
  $bd->ler($id);
  return "<b>".$bd->nome."</b>";
 }

 function susuario($id) {
  $bd = new DB();
  $bd->ler($id);
  return $bd->nome;
 }
?>