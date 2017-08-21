<?php
//algoritmo responsavel por deslogar o usuario
//terminado em 12/11/2014
session_start();
session_destroy();
header("location: index.php");
?>