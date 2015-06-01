<?php
$c = $_GET["c"];
$v0 = $_GET["v0"];
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
$votes = array($v0, $v1, $v2, $v3, $v4, $v5, $v6, $v7, $v8, $v9, $v10);
$offsets = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
for($i=0; $i<11; $i++){
	$offsets[$i]+=(20*$votes[$i]);
	for($j=0; $j<11; $j++){
		if($i!=$j){
			$offsets[$i]-=(2*$votes[$j]);
		}
	}
}
$DBConnect = mysql_connect("tablo-al.com.mysql", "tablo_al_com", "X6bEwLVP");
$DBSelect = mysql_select_db("tablo_al_com", $DBConnect);
$resultset = mysql_query("SELECT * FROM tabela3");
for($i=1; $i<12; $i++){
	$row = mysql_fetch_row($resultset);
	$row[1]+=$offsets[$i-1];
	mysql_query("UPDATE tabela3 SET vlera=".$row[1]." WHERE id=".$i);
}
mysql_close($DBConnect);
?>