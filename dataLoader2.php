<?php
$DBConnect = mysql_connect("tablo-al.com.mysql", "tablo_al_com", "X6bEwLVP");
$DBSelect = mysql_select_db("tablo_al_com", $DBConnect);
$resultset = mysql_query("SELECT * FROM tabelaere");
$size = mysql_num_rows($resultset);
$count=0;
for($i=0; $i<$size; $i++){
  $row = mysql_fetch_row($resultset);
  if((($i%12)==10) || (($i%12)==11)){
    echo $row[11]."\n";
	$count++;
  }
  else{
    for($j=2; $j<12; $j++){
      echo $row[$j]."\n";
	  $count++;
    }
  }
}
mysql_close($DBConnect);
?>