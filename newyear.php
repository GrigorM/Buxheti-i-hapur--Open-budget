<?php
$v1 = $_GET["v1"];
$v2 = $_GET["v2"];
$v3 = $_GET["v3"];
$v4 = $_GET["v4"];
$v5 = $_GET["v5"];
$v6 = $_GET["v6"];
$v7 = $_GET["v7"];
$v8 = $_GET["v8"];
$v9 = $_GET["v9"];
$v10 = $_GET["v10"];
$v11 = $_GET["v11"];
$DBConnect = mysql_connect("tablo-al.com.mysql", "tablo_al_com", "X6bEwLVP");
$DBSelect = mysql_select_db("tablo_al_com", $DBConnect);
mysql_query("UPDATE tabela3 SET vlera=".v1." WHERE id=1");
mysql_query("UPDATE tabela3 SET vlera=".v2." WHERE id=2");
mysql_query("UPDATE tabela3 SET vlera=".v3." WHERE id=3");
mysql_query("UPDATE tabela3 SET vlera=".v4." WHERE id=4");
mysql_query("UPDATE tabela3 SET vlera=".v5." WHERE id=5");
mysql_query("UPDATE tabela3 SET vlera=".v6." WHERE id=6");
mysql_query("UPDATE tabela3 SET vlera=".v7." WHERE id=7");
mysql_query("UPDATE tabela3 SET vlera=".v8." WHERE id=8");
mysql_query("UPDATE tabela3 SET vlera=".v9." WHERE id=9");
mysql_query("UPDATE tabela3 SET vlera=".v10." WHERE id=10");
mysql_query("UPDATE tabela3 SET vlera=".v11." WHERE id=11");
?>