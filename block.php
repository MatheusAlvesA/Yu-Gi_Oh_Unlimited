<?php
include("libs/tools_lib.php");
require_once 'libs/Mobile_Detect.php';

$tools = new Tools();
$tools->verificar();

session_start(); // logando
session_destroy(); // só pra poder deslogar

header("location: index.php?erro=15");
?>