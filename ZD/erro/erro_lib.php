<?php
class Erro {
 function set($codigo, $arg) {
   $momento = date("y")."/".date("m")."/".date("w")."-".date("h").":".date("i");
   $a;
   $status = fopen("SiteStatus.txt", 'w');
   $arquivo = fopen("erro/log.txt", 'a'); // abrindo log
     if($codigo == '') {$a = 0;fwrite($arquivo, "nao informado erro $momento \n");exit();} // erro nao informado
  switch ($codigo) {
   case 1:
    fwrite($arquivo, "tentativa de mudar array quando e matriz ou o contrario em gravacao_lib $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 2:
    fwrite($arquivo, "parametro incorreto passado em gravacao_lib $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 3:
    fwrite($arquivo, "array ou matriz esta vasio em gravacao_lib $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 4:
    fwrite($arquivo, "caminho do arquivo nao informado em gravacao_lib $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 5:
    fwrite($arquivo, "nao foi possiveu abrir o arquivo em gravacao_lib, caminho: $arg  $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 6:
    fwrite($arquivo, "nao foi possiveu abrir o cod.txt em db_lib $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 7:
    fwrite($arquivo, "nao foi possiveu ler cod.txt em db_lib $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 8:
    fwrite($arquivo, "nao foi preenchido tudo em db_lib $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 9:
    fwrite($arquivo, "nao foi possiveu abrir users.txt db_lib $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 10:
    fwrite($arquivo, "nao fo possiveu abrir pastas.txt db_lib $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 11:
    fwrite($arquivo, "nao foi possiveu abrir rg.txt db_lib $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 12:
    fwrite($arquivo, "nao foi possiveu abrir aviso.txt em home.php $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 13:
    fwrite($arquivo, "id nao passado para ler em msg_lib.php $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 14:
    fwrite($arquivo, "argumento nao passado para enviar em msg_lib.php $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
    case 15:
    fwrite($arquivo, "argumento nao passado para apagar em msg_lib.php $momento \n");
    fwrite($status, "1");
    header("location: erro/erro1.php");
    exit();
    break;
   default : //erro ao procurar a causa do erro
    if($a){fwrite($arquivo, "informado erro nao registrado: $codigo \n");}
    fwrite($status, "1");
    exit();
   break;
  }
  fclose($arquivo); // fechando log
  fclose($status);
 }
}
?>