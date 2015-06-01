<?php
$DBConnect = mysql_connect("tablo-al.com.mysql", "tablo_al_com", "X6bEwLVP");
$DBSelect = mysql_select_db("tablo_al_com", $DBConnect);
$resultset = mysql_query("SELECT * FROM tabela3");
$size = mysql_num_rows($resultset);
for($i=0; $i<$size; $i++){
	$row = mysql_fetch_row($resultset);
	echo "".$row[1]."\n";
}
//mysql_close($DBConnect);
?>