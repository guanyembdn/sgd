<?php
require_once "internal/sess.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

if(!isset($_GET["mes1"]) || !is_numeric($_GET["mes1"])) die();
if(!isset($_GET["mes2"]) || !is_numeric($_GET["mes2"])) die();

$mesmin = min($_GET["mes1"], $_GET["mes2"]);
$mesmax = max($_GET["mes1"], $_GET["mes2"]);

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="previsiopagaments Desat ' . date('d-m-Y') . ' Mes ' . $_GET["mes1"] . ' a ' . $_GET["mes2"] . '.csv"');


$totalperiode = 0;

echo "\"Nom de compte\",\"Nom i cognoms\",\"IBAN\",\"BIC\",\"QuantitatEuros\"\r\n";

for($mes = $mesmin; $mes <= $mesmax; $mes++){
	echo "\"Mes " . $mes . "\"\r\n";
	
	$sth = $con->prepare("SELECT p.nomusuari, p.Nom_i_Cognoms, p.IBAN, p.BIC, q.quota AS quotafinal FROM quotes AS q INNER JOIN persones AS p ON q.uid = p.id WHERE ((MONTH(q.startdate)-(:mes)) % p.Periodicitat_Quota = 0)");
	$sth->bindParam(":mes", $mes, PDO::PARAM_INT);
	$sth->execute();
	$rquotes = $sth->fetchAll(PDO::FETCH_ASSOC);

	$sumatotal = 0;

	foreach($rquotes as $row){
		$sumatotal += $row["quotafinal"];
		echo "\"" . $row["nomusuari"] . "\",\"" . $row["Nom_i_Cognoms"] . "\",\"" . $row["IBAN"] . "\",\"" . $row["BIC"] . "\",\"" . $row["quotafinal"] . "\"\r\n";
	}
	
	$totalperiode += $sumatotal;
	
	echo "\"Total mes " . $mes . "\",\"" . $sumatotal . "\"\r\n";
	echo "\"Total mesos " . $mesmin . " a " . $mes . "\",\"" . $totalperiode . "\"\r\n";
}

echo "\"Total per tots els mesos\",\"" . $totalperiode . "\"";
?>