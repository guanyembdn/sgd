<?php
require_once "internal/sess.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="votacions_resultats' . date('d-m-Y') . '.csv"');

$sth = $con->query("SELECT vr.*, v.pregunta AS pregunta FROM votacions_respostes AS vr INNER JOIN votacions AS v ON vr.vid = v.id ");
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
		if($k == "pregunta"){
			$row .= "\"" . str_replace("\r", "", str_replace("\n", " ", $v)) . "\",";
		}else{
			$row .= "\"" . $v . "\",";
		}
	}
	
	$row = rtrim($row, ',');
	echo $row . "\r\n";
}
?>