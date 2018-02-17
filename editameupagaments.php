<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/parcuserinc.php";
require_once "internal/php-iban/php-iban.php";

$disableform = false;
$confirmat = false;

if(isset($_POST["cancelar"])){
	$sth=$con->prepare("DELETE FROM quotes WHERE uid=:uid");
	$sth->bindParam(":uid", intval($_SESSION["uid"]), PDO::PARAM_INT);
	$sth->execute();
			
	die('<h1 class="clear">Gestió dels meus pagaments</h1>
	<p>Pagament cancelat. Pots actualitzar la teva informació.<br>
	<a href="">Gestionar nou pagament</a><br>
	<a href="/">Tornar endarrera: página principal</a></p>');
	
}

$sth=$con->prepare("SELECT * FROM quotes WHERE uid=:uid");
$sth->bindParam(":uid", intval($_SESSION["uid"]), PDO::PARAM_INT);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

$pagamentactiu = false;
$simulacio = false;
$invalidiban = false;

if(isset($result[0])){
	$disableform = true;
	$pagamentactiu = true;
	$quota = $result[0]["quota"];
}else{
	if(isset($_POST["quota"]) && !empty($_POST["quota"]) && is_numeric($_POST["quota"]) && is_numeric($_POST["periodicitat_quota"])){
		$quota = $_POST["quota"];
		
		if($_POST["periodicitat_quota"] == 12 || $_POST["periodicitat_quota"] == 3 || $_POST["periodicitat_quota"] == 4 || $_POST["periodicitat_quota"] == 6){
			$iban = iban_to_machine_format($_POST["iban"]);
			
			if(verify_iban($iban,$machine_format_only=true)){
				$disableform = true;
				
				if(isset($_POST["confirmar"])){
					$sth=$con->prepare("UPDATE persones SET Nom_i_Cognoms=:Nom_i_Cognoms, iban=:iban, BIC=:BIC, Periodicitat_Quota=:periodicitat_quota WHERE id=:id");
					
					$sth->bindParam(":id", intval($_SESSION["uid"]), PDO::PARAM_INT);
					$sth->bindParam(":iban", $iban, PDO::PARAM_STR, 100);
					$sth->bindParam(":BIC", $_POST["BIC"], PDO::PARAM_STR, 100);
					$sth->bindParam(":Nom_i_Cognoms", $_POST["Nom_i_Cognoms"], PDO::PARAM_STR, 200);
					$sth->bindParam(":periodicitat_quota", $_POST["periodicitat_quota"], PDO::PARAM_INT);
					$sth->execute();
					
					$sth=$con->prepare("INSERT INTO quotes (uid, quota, startdate) VALUES (?, ?, CURDATE())");
					$sth->execute(array(intval($_SESSION["uid"]), $quota));
					$confirmat = true;
				}else{
					$simulacio = true;
				}
			}else{
				$invalidiban = true;
			}
		}
	}
}

$periodicitat_quota = "";

$sth=$con->prepare("SELECT iban, Periodicitat_Quota, Nom_i_Cognoms, BIC FROM persones WHERE id=?");
$sth->bindParam(1, intval($_SESSION["uid"]), PDO::PARAM_INT);
$sth->execute();
$row = $sth->fetch(PDO::FETCH_ASSOC);

if(isset($_POST["periodicitat_quota"]) && ($_POST["periodicitat_quota"] == 12 || $_POST["periodicitat_quota"] == 3 || $_POST["periodicitat_quota"] == 4 || $_POST["periodicitat_quota"] == 6)){
	$periodicitat_quota = $_POST["periodicitat_quota"];
}else{
	$periodicitat_quota = $row["Periodicitat_Quota"];
}

if($confirmat){
	?>
	
	<h1 class="clear">Gestió dels meus pagaments</h1>
	<p>El teu pagament ja está actiu. Torna a aquesta pàgina si/quan vulguis cancelar-lo.<br>
	<a href="">Veure pagament</a></p>
	
	<?php
}else{
?>

<h1 class="clear">Gestió dels meus pagaments</h1>
<?php
if($pagamentactiu) echo "<p>Ja tens un pagament periòdic actiu. Pots veure les dades i pots elegir cancelar-ho.</p>";
else echo "<p>Si vols establir un pagament periòdic utilitza aquesta pàgina. També el podrás cancelar des d'aquesta pàgina.</p>";
?>

<div>
    <form method="post">
		<p class="clear">
		<?php if($invalidiban){ echo '<div class="warning">⚠️ Has introduit un IBAN no vàlid! Per continuar es necesari introduir un IBAN vàlid.</div>'; } ?>
		<label>Els teus noms i cognoms:
		<input type="text" required name="Nom_i_Cognoms" <?=$disableform ? " readonly" : ""?> value="<?php if(isset($_POST["Nom_i_Cognoms"])) echo safe_escape($_POST["Nom_i_Cognoms"]); else echo safe_escape($row["Nom_i_Cognoms"]); ?>"></label><br>
		<label>Quota:
		<input type="text" required name="quota" <?=$disableform ? " readonly" : ""?> value="<?php if(isset($quota)) echo safe_escape($quota); ?>"></label> € per període<br>
		<label>IBAN:
		<input type="text" required name="iban" <?=$disableform ? " readonly" : ""?> value="<?php if(isset($_POST["iban"])) echo safe_escape($_POST["iban"]); else echo safe_escape($row["iban"]); ?>"></label><br>
		<label>BIC (Codi banc):
		<input type="text" required name="BIC" <?=$disableform ? " readonly" : ""?> value="<?php if(isset($_POST["BIC"])) echo safe_escape($_POST["BIC"]); else echo safe_escape($row["BIC"]); ?>"></label><br>
		<label>Periodicitat quota:
		<select name="periodicitat_quota<?=$simulacio ? "_fake" : ""?>" <?=$disableform ? " disabled" : ""?>>
			<option value="12"<?=$periodicitat_quota == "12" ? " selected" : ""?>>Anual</option>
			<option value="3"<?=$periodicitat_quota == "3" ? " selected" : ""?>>Trimestral</option>
			<option value="4"<?=$periodicitat_quota == "4" ? " selected" : ""?>>Quatrimestral</option>
			<option value="6"<?=$periodicitat_quota == "6" ? " selected" : ""?>>Semestral</option>
		</select></label><br>
		
		<?php
		if($simulacio) echo("<input type='hidden' name='periodicitat_quota' value='" . safe_escape($periodicitat_quota) . "'>");
		?>
		
		<input type="submit" value="Enviar"<?=$disableform ? " disabled" : ""?>>
		<?php if($pagamentactiu) echo '<br><input type="submit" name="cancelar" value="Cancelar">'; ?>
</div>

<?php
	if($simulacio){
?>

	<h2>Simulació</h2>

	<p>Aixó es una simulació dels pagaments. Cada any pagarás:<br><br>
	<?php
	switch($_POST["periodicitat_quota"]){
		case 12:
			echo "<strong>" . safe_escape($_POST["quota"]) . "€</strong> per any.";
			break;
		case 3:
			echo "<strong>" . safe_escape($_POST["quota"]) . "€</strong> el primer trimestre.<br>";
			echo "<strong>" . safe_escape($_POST["quota"]) . "€</strong> el segon trimestre.<br>";
			echo "<strong>" . safe_escape($_POST["quota"]) . "€</strong> el tercer trimestre.<br>";
			echo "<strong>" . safe_escape($_POST["quota"]) . "€</strong> el quart trimestre.<br>";
			break;
		case 4:
			echo "<strong>" . safe_escape($_POST["quota"]) . "€</strong> el primer quatrimestre.<br>";
			echo "<strong>" . safe_escape($_POST["quota"]) . "€</strong> el segon quatrimestre.<br>";
			echo "<strong>" . safe_escape($_POST["quota"]) . "€</strong> el tercer quatrimestre.<br>";
			break;
		case 6:
			echo "<strong>" . safe_escape($_POST["quota"]) . "€</strong> el primer semestre.<br>";
			echo "<strong>" . safe_escape($_POST["quota"]) . "€</strong> el segon semestre.<br>";
			break;
	}
	?>
	</p>
	
	<h2>Confirmació</h2>
	
	<p>Revisa que la informació sigui correcta. Si ho has fet, pots afegir el pagament.<br>
	Vols afegir aquest pagament periòdic?<br>
	<input type="submit" name="confirmar" value="Confirmar"><br>
	<a href="">Endarrera</a>
	</p>

<?php
	}
}
?>

</form>
<h2>Altres opcions</h2>
<a href="/">Endarrera: pàgina principal</a>

<?php
require_once "internal/foot.php";
?>