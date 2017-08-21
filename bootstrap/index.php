<?php
$momento = date("y")."/".date("m")."/".date("w")."-".date("h").":".date("i");
$log_txt = fopen("log.txt", 'a');
fwrite($log_txt, "tentativa de acesso em: ".$momento."\n");
fclose($log_txt);
header("location: ../404.php");
?>