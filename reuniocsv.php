<?php
require_once "internal/sess.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="reunions' . date('d-m-Y') . '.csv"');

$sth = $con->query("SELECT r.* FROM reunions AS r");
$res = $sth->fetchAll(PDO::FETCH_ASSOC);

$col = "";
foreach($res[0] as $k => $v){
    $col .= "\"" . $k . "\",";
}
$col = rtrim($col, ',');
echo $col . "\r\n";

foreach($res as $rowa){
	$row = "";
	
	foreach($rowa as $k => $v){
		if($k == "Ordre" || $k == "Lloc" || $k == "Acta"){
			$row .= "\"" . str_replace("\r", "", str_replace("\n", " ", $v)) . "\",";
		}else{
			$row .= "\"" . $v . "\",";
		}
	}
	
	$row = rtrim($row, ',');
	echo $row . "\r\n";
}
?>