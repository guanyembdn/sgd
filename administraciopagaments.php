<?php
require_once "internal/head.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";

function selectmonth(){
	if(isset($_POST["mes"])) return intval($_POST["mes"]);
	else return date("m");
}

$disableformgeneral = isset($_POST["generar"]) || isset($_POST["desasepa"]);
?>

<h1 class="clear">Administració de pagaments</h1>

<p>Si fas clic al botó <strong>Generar informe</strong>, es generará una llista de persones que tenen que pagar aquest mes, amb la quantitat i el codi IBAN de cada persona.</p>

<form method="post">
Selecciona el mes:<br>
<select name="mes"<?=$disableformgeneral ? " disabled" : ""?>>
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
<?php if($disableformgeneral && isset($_POST["mes"])) echo '<input type="hidden" name="mes" value="' . intval($_POST["mes"]) . '">'; ?>
<input type="submit" name="generar" value="Generar informe"<?=$disableformgeneral ? " disabled" : ""?>>
<br>
<?php if($disableformgeneral) echo '<a href="">Cancelar</a>'; ?>

<?php
if(isset($_POST["generar"]) || isset($_POST["desasepa"])){
?>

<h2>Informe pel mes <?=$_POST["mes"]?></h2>

<table class="sortable">
<tr>
<th>Nom de compte</th>
<th>Nom i cognoms</th>
<th>IBAN</th>
<th>BIC</th>
<th>Quantitat (€)</th>
</tr>

<?php
$disableformsepa = false;

if(isset($_POST["desasepa"])){
	$disableformsepa = true;
	$sth=$con->prepare("INSERT INTO infoentitat (id, Nom, IBAN, BIC, PrvtId) VALUES(1, :Nom, :IBAN, :BIC, :PrvtId) ON DUPLICATE KEY UPDATE Nom=:Nom, IBAN=:IBAN, BIC=:BIC, PrvtId=:PrvtId");
	$sth->bindParam(":Nom", $_POST["NomEntitat"], PDO::PARAM_STR, 200);
	$sth->bindParam(":IBAN", $_POST["IBANEntitat"], PDO::PARAM_STR, 100);
	$sth->bindParam(":BIC", $_POST["BICEntitat"], PDO::PARAM_STR, 100);
	$sth->bindParam(":PrvtId", $_POST["PrvtIdEntitat"], PDO::PARAM_STR, 100);
	$sth->execute();
}

$sth = $con->query("SELECT * FROM infoentitat WHERE id=1");
$rowentitat = $sth->fetchAll(PDO::FETCH_ASSOC);

$sth = $con->prepare("SELECT p.nomusuari, p.Nom_i_Cognoms, p.IBAN, p.BIC, q.quota AS quotafinal FROM quotes AS q INNER JOIN persones AS p ON q.uid = p.id WHERE ((MONTH(q.startdate)-(:mes)) % p.Periodicitat_Quota = 0)");
$sth->bindParam(":mes", intval($_POST["mes"]), PDO::PARAM_INT);
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
?>
</table>

<p><strong>Total pel mes <?=intval($_POST["mes"])?>:</strong> <?=$sumatotal?>€<br>
Els nom i cognoms mostrats poden ser canviats pel usuari.</p>


<p><a href="previsiocsv?mes1=<?=intval($_POST["mes"])?>&mes2=<?=intval($_POST["mes"])?>"><button type="button">Descarregar fitxer CSV</button></a></p>

<h2>Generar fitxer SEPA</h2>
Dades de la entitat que rebrà el pagament:<br>
Nom: <input type="text" name="NomEntitat" <?=$disableformsepa ? " readonly" : ""?> required value="<?php if(isset($rowentitat[0])) echo safe_escape($rowentitat[0]["Nom"]); ?>"><br>
Identificador privat: <input type="text" name="PrvtIdEntitat" <?=$disableformsepa ? " readonly" : ""?> required value="<?php if(isset($rowentitat[0])) echo safe_escape($rowentitat[0]["PrvtId"]); ?>"><br>
IBAN: <input type="text" name="IBANEntitat" <?=$disableformsepa ? " readonly" : ""?> required  value="<?php if(isset($rowentitat[0])) echo safe_escape($rowentitat[0]["IBAN"]); ?>"><br>
BIC: <input type="text" name="BICEntitat" <?=$disableformsepa ? " readonly" : ""?> required  value="<?php if(isset($rowentitat[0])) echo safe_escape($rowentitat[0]["BIC"]); ?>"><br>
<input type="submit" name="desasepa" value="Desa">
</form>

<?php
if(isset($_POST["desasepa"])){
	?>
	
	
	<a href="genxmlsepa?mes=<?=intval($_POST["mes"])?>"><button>Descarregar fitxer SEPA</button></a>
	
	<?php
}else{
?>
<button disabled>Descarregar fitxer SEPA</button>
<?php
}
?>

<p>Comprova que la informació sigui correcta. <strong>Desa</strong> la informació per poder descarregar el fitxer SEPA.</p>

<?php
}else{
	echo '</form>';
}
?>

<h2>Altres funcions</h2>

<p>
<ul>
<li><a href="previsionspagaments">Previsions pagaments</a></li>
<li><a href="/">Endarrera: pàgina principal</a></li>
</ul>
</p>

<?php
require_once "internal/foot.php";
?>