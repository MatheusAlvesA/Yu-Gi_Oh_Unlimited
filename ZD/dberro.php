<?php
include("../libs/tools_lib.php");
$tools = new Tools();
$tools->verificar();
header("location: index.php?sair=1&dberro=1");
?>
