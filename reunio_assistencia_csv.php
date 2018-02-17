<?php
require_once "internal/sess.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="reunions_assistencia' . date('d-m-Y') . '.csv"');

$sth = $con->query("SELECT rp.*, r.Nom AS NomReunio, p.nomusuari AS NomPersona, CASE rp.tipus WHEN 1 then 'Convocat' WHEN 2 then 'Assistirá' WHEN 3 then 'Ha assistit' END AS TipusString FROM reunions_persones AS rp INNER JOIN reunions AS r ON r.id=rp.rid INNER JOIN persones AS p ON p.id=rp.uid");
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
		//if($k == "Ordre" || $k == "Lloc" || $k == "Acta"){
		//	$row .= "\"" . str_replace("\r", "", str_replace("\n", " ", $v)) . "\",";
		//}else{
			$row .= "\"" . $v . "\",";
		//}
	}
	
	$row = rtrim($row, ',');
	echo $row . "\r\n";
}
?>