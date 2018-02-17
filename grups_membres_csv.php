<?php
require_once "internal/sess.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="grups_membres' . date('d-m-Y') . '.csv"');

$sth = $con->query("SELECT g.id AS GrupID, g.Nom AS GrupNom, gp.idpersona AS IDPersona, p.nomusuari AS NomCompte, gp.rol, CASE gp.rol WHEN 0 then 'Pot veure' WHEN 1 then 'Usuari/a' WHEN 2 then 'Administrador/a' END AS RolString FROM grups AS g INNER JOIN grups_persones AS gp ON g.id=gp.idgrup INNER JOIN persones AS p ON p.id=gp.idpersona");
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
		//if($k == "Lloc_Reunions"){
		//	$row .= "\"" . str_replace("\r", "", str_replace("\n", " ", $v)) . "\",";
		//}else{
			$row .= "\"" . $v . "\",";
		//}
	}
	
	$row = rtrim($row, ',');
	echo $row . "\r\n";
}
?>