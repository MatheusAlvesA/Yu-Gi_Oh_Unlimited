<?php
include("libs/tools_lib.php");
include("libs/db_lib.php");
include("libs/msg_lib.php");

$tools = new Tools();
$tools->verificar();
$tools->verificarlog();
// processando mensagem sendo enviada
if($_POST["texto"] != '' && $_POST["nome"] != '') {
	if(converter_nome_id($_POST["nome"]) == 0 || converter_nome_id($_POST["nome"]) == $_SESSION["id"]) {header("location: enviarmsg.php?erro=2");exit();}
	if(strlen($_POST["texto"]) > 500) {header("location: enviarmsg.php?erro=1");exit();}

	$msg = new MSG();
	$msg->enviar(htmlentities($_POST["texto"]), $_SESSION["id"], converter_nome_id($_POST["nome"]));

	$arq = fopen("msgs/_".converter_nome_id($_POST["nome"]).".txt", 'w');
	fwrite($arq, 1);
	fclose($arq);
	header("location: enviarmsg.php?msg=1");
}
 function converter_nome_id($nome) {
  $bd = new DB();
  return $bd->nome_id($nome);
 }
// fim do processamen
$erro_msg = '';
if($_GET["erro"] == 1) {$erro_msg = "<tr><td><p style=\"color: red\">A mensagem deve ter no máximo 500 caracteres</p></td></tr>";}
if($_GET["erro"] == 2) {$erro_msg = "<tr><td><p style=\"color: red\">Este usuário não existe</p></td></tr>";}
if($_GET["msg"] == 1) {$erro_msg = "<tr><td><p style=\"color: blue\">Mensagem enviada com sucesso</p></td></tr>";}

$nome = '';
if($tools->verificarstr($_GET["nome"])) {$nome = $_GET["nome"];}
?>
<html>
<head>
<?php include 'head.txt';?>
<title>Enviar mensagem</title>
</head>
<body <?php echo $tools->fundo;?> >
<img src="<?php echo $tools->capa;?>" height="<?php echo $tools->rcapa;?>" width="100%" />
<form action="enviarmsg.php" method="post">
<table border = "1" width = "100%">
<?php echo $erro_msg;?>
<tr><td><b>Nome: </b><input type="text" name="nome" value="<?php echo $nome;?>" /></td></tr>
<tr><td><textarea name="texto" cols="35" rows="10"></textarea></td></tr>
<tr><td><input type="submit" value="Enviar"/></td></tr>
</form>
</table>
<?php include 'rodape.txt';?>
</body>
</html>