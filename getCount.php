<html>
<head>
<title>
</title>
</head>
<body>
<?php
$database=mysql_connect("tablo-al.com.mysql","tablo_al_com","X6bEwLVP");
$select=mysql_select_db("tablo_al_com",$database);
$id=$_GET["id"];
$query=mysql_query("SELECT * FROM logimi ");
$boolean=false;
$count=0;
while($row=mysql_fetch_row($query))
{
    if($id==$row[1])
	{
	$count=$row[2];
	$boolean=true;
	}
}	
	




?>
</body>
</html>