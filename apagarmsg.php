<?php
include("libs/tools_lib.php");
include("libs/gravacao_lib.php");
include("libs/erro_lib.php");
include("libs/msg_lib.php");

$tools = new Tools();
$tools->verificar();
$tools->verificarlog();

$msg = new MSG();
$msgs = $msg->ler($_SESSION["id"]);

if(!$tools->verificaridade($_GET["id"]) || $_GET["id"] <= 0) {header("location: mensagens.php");exit();}
if($msgs[0][0] <= 0 || $_GET["id"] > $msgs[0][0]) {header("location: mensagens.php");exit();}

$msg->apagar($_GET["id"], $_SESSION["id"]);
header("location: mensagens.php");
?>