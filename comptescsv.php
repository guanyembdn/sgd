<?php
require_once "internal/sess.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="comptes' . date('d-m-Y') . '.csv"');

$sth = $con->query("SELECT p.id, p.nomusuari, p.tipus AS TipusSGD, p.Nom_i_Cognoms, p.NIF, p.Districte, p.Barri, p.email, p.Telefon_Casa, p.Telefon_Mobil, p.Genere, p.Data_Naixement, p.Tipus_Membre_Partit, p.Data_Alta_Simpatitzant, p.Data_Baixa_Simpatitzant, p.Data_Alta_Membre_Ple_Dret, p.Data_Baixa_Membre_Ple_Dret, p.IBAN, p.Sense_Ingressos, p.Paga_Transferencia, p.Periodicitat_Quota, p.BIC, p.Alies_Telegram, p.Adresa, p.Codi_Postal, p.Ciutat, p.Provincia, p.Comentaris, IF(quota IS NULL,0,quota) AS quota FROM persones AS p LEFT JOIN quotes AS q ON p.id = q.uid");
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
		if($k == "Comentaris"){
			$row .= "\"" . str_replace("\r", "", str_replace("\n", " ", $v)) . "\",";
		}else{
			$row .= "\"" . $v . "\",";
		}
	}
	
	$row = rtrim($row, ',');
	echo $row . "\r\n";
}
?>