<?php
$sql_host = "localhost";
$sql_banco = "base_de_dados";
$sql_user = "root";
$sql_senha = "";

$bd = mysql_connect($sql_host, $sql_user, $sql_senha) or die(sqlerro(1));
mysql_select_db($sql_banco) or die(sqlerro(2)); 

mysql_query('CREATE TABLE clans (
nome VARCHAR(100),
lider INT,
PRIMARY KEY(nome)
)ENGINE=InnoDB') or die(sqlerro(3));

mysql_query('CREATE TABLE usuarios (
id INT AUTO_INCREMENT,
nome TEXT,
sexo CHAR,
idade INT,
email TEXT,
confirmado INT,
senha TEXT,
xp INT,
dinheiro INT,
status INT,
vitorias INT,
derrotas INT,
registro DATE,
acessada DATE,
reino INT,
pts_reino INT,
personagem INT,
clan VARCHAR(100),
pts_clan INT,
status_clan INT DEFAULT 0,
VIP BIGINT DEFAULT 0,
PRIMARY KEY(id),
CONSTRAINT fk_clan FOREIGN KEY(clan) REFERENCES clans (nome)
)ENGINE=InnoDB') or die(sqlerro(3));

mysql_query('CREATE TABLE decks (
id INT,
c1 INT,
c2 INT,
c3 INT,
c4 INT,
c5 INT,
c6 INT,
c7 INT,
c8 INT,
c9 INT,
c10 INT,
c11 INT,
c12 INT,
c13 INT,
c14 INT,
c15 INT,
c16 INT,
c17 INT,
c18 INT,
c19 INT,
c20 INT,
c21 INT,
c22 INT,
c23 INT,
c24 INT,
c25 INT,
c26 INT,
c27 INT,
c28 INT,
c29 INT,
c30 INT,
c31 INT,
c32 INT,
c33 INT,
c34 INT,
c35 INT,
c36 INT,
c37 INT,
c38 INT,
c39 INT,
c40 INT,
c41 INT,
c42 INT,
c43 INT,
c44 INT,
c45 INT,
c46 INT,
c47 INT,
c48 INT,
c49 INT,
c50 INT,
PRIMARY KEY(id)
)') or die(sqlerro(4));

mysql_query('CREATE TABLE inventarios (
id INT,
c1 INT,
c2 INT,
c3 INT,
c4 INT,
c5 INT,
c6 INT,
c7 INT,
c8 INT,
c9 INT,
c10 INT,
c11 INT,
c12 INT,
c13 INT,
c14 INT,
c15 INT,
c16 INT,
c17 INT,
c18 INT,
c19 INT,
c20 INT,
c21 INT,
c22 INT,
c23 INT,
c24 INT,
c25 INT,
c26 INT,
c27 INT,
c28 INT,
c29 INT,
c30 INT,
c31 INT,
c32 INT,
c33 INT,
c34 INT,
c35 INT,
c36 INT,
c37 INT,
c38 INT,
c39 INT,
c40 INT,
c41 INT,
c42 INT,
c43 INT,
c44 INT,
c45 INT,
c46 INT,
c47 INT,
c48 INT,
c49 INT,
c50 INT,
c51 INT,
c52 INT,
c53 INT,
c54 INT,
c55 INT,
c56 INT,
c57 INT,
c58 INT,
c59 INT,
c60 INT,
c61 INT,
c62 INT,
c63 INT,
c64 INT,
c65 INT,
c66 INT,
c67 INT,
c68 INT,
c69 INT,
c70 INT,
c71 INT,
c72 INT,
c73 INT,
c74 INT,
c75 INT,
c76 INT,
c77 INT,
c78 INT,
c79 INT,
c80 INT,
c81 INT,
c82 INT,
c83 INT,
c84 INT,
c85 INT,
c86 INT,
c87 INT,
c88 INT,
c89 INT,
c90 INT,
c91 INT,
c92 INT,
c93 INT,
c94 INT,
c95 INT,
c96 INT,
c97 INT,
c98 INT,
c99 INT,
c100 INT,
PRIMARY KEY(id)
)') or die(sqlerro(5));

mysql_query('CREATE TABLE amigos (
id INT,
a1 INT,
a2 INT,
a3 INT,
a4 INT,
a5 INT,
a6 INT,
a7 INT,
a8 INT,
a9 INT,
a10 INT,
a11 INT,
a12 INT,
a13 INT,
a14 INT,
a15 INT,
a16 INT,
a17 INT,
a18 INT,
a19 INT,
a20 INT,
a21 INT,
a22 INT,
a23 INT,
a24 INT,
a25 INT,
a26 INT,
a27 INT,
a28 INT,
a29 INT,
a30 INT,
a31 INT,
a32 INT,
a33 INT,
a34 INT,
a35 INT,
a36 INT,
a37 INT,
a38 INT,
a39 INT,
a40 INT,
a41 INT,
a42 INT,
a43 INT,
a44 INT,
a45 INT,
a46 INT,
a47 INT,
a48 INT,
a49 INT,
a50 INT,
a51 INT,
a52 INT,
a53 INT,
a54 INT,
a55 INT,
a56 INT,
a57 INT,
a58 INT,
a59 INT,
a60 INT,
a61 INT,
a62 INT,
a63 INT,
a64 INT,
a65 INT,
a66 INT,
a67 INT,
a68 INT,
a69 INT,
a70 INT,
a71 INT,
a72 INT,
a73 INT,
a74 INT,
a75 INT,
a76 INT,
a77 INT,
a78 INT,
a79 INT,
a80 INT,
a81 INT,
a82 INT,
a83 INT,
a84 INT,
a85 INT,
a86 INT,
a87 INT,
a88 INT,
a89 INT,
a90 INT,
a91 INT,
a92 INT,
a93 INT,
a94 INT,
a95 INT,
a96 INT,
a97 INT,
a98 INT,
a99 INT,
a100 INT,
PRIMARY KEY(id)
)') or die(sqlerro(6));

//mysql_query("INSERT INTO usuarios (nome, idade) VALUES ('matheus', '19')") or die (sqlerro());

//$rs = mysql_query('SELECT * FROM usuarios WHERE id = '."'".$_GET['x']."'");

//while($array = mysql_fetch_array($rs)) {
//echo $array['nome'].'.<br />';
//}
//echo $array['nome'].'.<br />';

//mysql_query("DELETE FROM usuarios WHERE id = 1") or die(sqlerro());

//mysql_query("UPDATE usuarios SET nome='Camila' WHERE id = ".$_GET['x']) or die(sqlerro());

echo ': '.$bd;
mysql_close();
function sqlerro($x) {
if($x == 1) {echo 'erro na conexao 1.';}
if($x == 2) {echo 'erro na conexao 2.';}
if($x == 3) {echo 'erro para criar tabela usuarios.';}
if($x == 4) {echo 'erro para criar tabela deck.';}
if($x == 5) {echo 'erro para criar tabela inventario.';}
if($x == 6) {echo 'erro para criar tabela amigos.';}
}
?>