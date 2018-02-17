<?php
require_once "internal/head.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

function selectmonth(){
	if(isset($_POST["mes1"])) return intval($_POST["mes1"]);
	else return date("m");
}

function selectmonth2(){
	if(isset($_POST["mes2"])) return intval($_POST["mes2"]);
	else return date("m");
}
?>

<h1 class="clear">Previsió pagaments</h1>

<p>Si fas clic al botó <strong>Generar previsió</strong>, es generará una llista de persones que tenen que pagar entre els mesos que seleccionis, amb la quantitat i el codi IBAN de cada persona, i es fara una previsió del ingres total per quotes entre els mesos segons les dades actuals.</p>

<form method="post">
Del mes:<br>
<select name="mes1">
<option value="1"<?=selectmonth() == 1 ? " selected" : ""?>>gener</option>
<option value="2"<?=selectmonth() == 2 ? " selected" : ""?>>febrer</option>
<option value="3"<?=selectmonth() == 3 ? " selected" : ""?>>març</option>
<option value="4"<?=selectmonth() == 4 ? " selected" : ""?>>abril</option>
<option value="5"<?=selectmonth() == 5 ? " selected" : ""?>>maig</option>
<option value="6"<?=selectmonth() == 6 ? " selected" : ""?>>juny</option>
<option value="7"<?=selectmonth() == 7 ? " selected" : ""?>>juliol</option>
<option value="8"<?=selectmonth() == 8 ? " selected" : ""?>>agost</option>
<option value="9"<?=selectmonth() == 9 ? " selected" : ""?>>setembre</option>
<option value="10"<?=selectmonth() == 10 ? " selected" : ""?>>octubre</option>
<option value="11"<?=selectmonth() == 11 ? " selected" : ""?>>novembre</option>
<option value="12"<?=selectmonth() == 12 ? " selected" : ""?>>desembre</option>
</select><br>
Fins al mes:<br>
<select name="mes2">
<option value="1"<?=selectmonth2() == 1 ? " selected" : ""?>>gener</option>
<option value="2"<?=selectmonth2() == 2 ? " selected" : ""?>>febrer</option>
<option value="3"<?=selectmonth2() == 3 ? " selected" : ""?>>març</option>
<option value="4"<?=selectmonth2() == 4 ? " selected" : ""?>>abril</option>
<option value="5"<?=selectmonth2() == 5 ? " selected" : ""?>>maig</option>
<option value="6"<?=selectmonth2() == 6 ? " selected" : ""?>>juny</option>
<option value="7"<?=selectmonth2() == 7 ? " selected" : ""?>>juliol</option>
<option value="8"<?=selectmonth2() == 8 ? " selected" : ""?>>agost</option>
<option value="9"<?=selectmonth2() == 9 ? " selected" : ""?>>setembre</option>
<option value="10"<?=selectmonth2() == 10 ? " selected" : ""?>>octubre</option>
<option value="11"<?=selectmonth2() == 11 ? " selected" : ""?>>novembre</option>
<option value="12"<?=selectmonth2() == 12 ? " selected" : ""?>>desembre</option>
</select><br>
<input type="submit" name="generar" value="Generar previsió">
</form>
<br>

<?php
if(isset($_POST["generar"])){
	?>
	
	<table>
<tr>
<th>Nom de compte</th>
<th>Nom i cognoms</th>
<th>IBAN</th>
<th>BIC</th>
<th>Quantitat (€)</th>
</tr>

<?php
$mesmin = min($_POST["mes1"], $_POST["mes2"]);
$mesmax = max($_POST["mes1"], $_POST["mes2"]);

$totalperiode = 0;

for($mes = $mesmin; $mes <= $mesmax; $mes++){
	?>
	
	<tr><td><strong>Mes <?=$mes?>:</strong></td></tr>
	
	<?php
	$sth = $con->prepare("SELECT p.nomusuari, p.Nom_i_Cognoms, p.IBAN, p.BIC, q.quota AS quotafinal FROM quotes AS q INNER JOIN persones AS p ON q.uid = p.id WHERE ((MONTH(q.startdate)-(:mes)) % p.Periodicitat_Quota = 0)");
	$sth->bindParam(":mes", $mes, PDO::PARAM_INT);
	$sth->execute();
	$rquotes = $sth->fetchAll(PDO::FETCH_ASSOC);

	$sumatotal = 0;

	foreach($rquotes as $row){
		$sumatotal += $row["quotafinal"];
		?>

			<tr>
			<td><?=safe_escape($row["nomusuari"])?></td>
			<td><?=safe_escape($row["Nom_i_Cognoms"])?></td>
			<td><?=safe_escape($row["IBAN"])?></td>
			<td><?=safe_escape($row["BIC"])?></td>
			<td><?=safe_escape($row["quotafinal"])?></td>
			</tr>

		<?php
	}
	
	$totalperiode += $sumatotal;
	?>

	<tr><td><strong>Total mes <?=$mes?>:</strong> <?=$sumatotal?>€</td></tr>
	<tr><td><strong>Total mesos <?=$mesmin?> a <?=$mes?>:</strong> <?=$totalperiode?>€</td></tr>

	<?php
}

?>

</table>
<p><a href="previsiocsv?mes1=<?=$mesmin?>&amp;mes2=<?=$mesmax?>"><button>Descarregar com a CSV</button></a><br>
<strong>Total per tots els mesos</strong>: <?=$totalperiode?>€<br>
Els nom i cognoms mostrats poden ser canviats pel usuari.</p>

<?php
}
?>

<h2>Altres funcions</h2>

<p>
<ul>
<li><a href="administraciopagaments">Administració pagaments</a></li>
<li><a href="/">Endarrera: pàgina principal</a></li>
</ul>
</p>

<?php
require_once "internal/foot.php";
?>