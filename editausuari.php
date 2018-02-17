<?php
require_once "internal/head.php";
require_once "internal/admininc.php";
require_once "internal/common.php";
require_once "internal/userinc.php";
require_once "internal/php-iban/php-iban.php";

if(!isset($_GET["id"]) || empty($_GET["id"])) die();

$invalidiban = false;

if(isset($_POST["nomusuari"]) && !empty($_POST["nomusuari"]) && validate_username($_POST["nomusuari"]) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
	if(!isset($_POST["alies_telegram"]) || empty($_POST["alies_telegram"]) || preg_match("/^[A-Za-z0-9_-]+$/", $_POST["alies_telegram"])){
		$sth=$con->prepare("UPDATE persones SET nomusuari=:nomusuari, tipus=:tipus, email=:email, Alies_Telegram=:alies_telegram,
		Genere=:genere, Nom_i_Cognoms =:Nom_i_Cognoms, NIF=:NIF, Districte=:Districte, Barri=:Barri, Telefon_Casa=:Telefon_Casa, Telefon_Mobil=:Telefon_Mobil,
		Tipus_Membre_Partit=:Tipus_Membre_Partit, Data_Naixement=:Data_Naixement, data_alta_simpatitzant=:data_alta_simpatitzant, data_baixa_simpatitzant=:data_baixa_simpatitzant,
		data_alta_membre_ple_dret=:data_alta_membre_ple_dret, data_baixa_membre_ple_dret=:data_baixa_membre_ple_dret, BIC=:bic,
		sense_ingressos=:sense_ingressos, paga_transferencia=:paga_transferencia, comentaris=:comentaris,
		Adresa=:Adresa, Codi_Postal=:Codi_Postal, Ciutat=:Ciutat, Provincia=:Provincia, Xarxes=:Xarxes, Periodicitat_Quota=:periodicitat_quota, No_vol_emails=:no_vol_emails WHERE id=:id");
		
		$sense_ingressos_bool = isset($_POST["sense_ingressos"]) ? 1 : 0;
		$paga_transferencia_bool = isset($_POST["paga_transferencia"]) ? 1 : 0;
		$no_vol_emails_bool = isset($_POST["no_vol_emails"]) ? 1 : 0;
	
		$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
		$sth->bindParam(":nomusuari", $_POST["nomusuari"], PDO::PARAM_STR, 50);
		$sth->bindParam(":tipus", $_POST["tipus"], PDO::PARAM_INT);
		$sth->bindParam(":email", $_POST["email"], PDO::PARAM_STR, 200);
		$sth->bindParam(":alies_telegram", $_POST["alies_telegram"], PDO::PARAM_STR, 100);
		$sth->bindParam(":genere", $_POST["genere"], PDO::PARAM_INT, 4);
		$sth->bindParam(":Nom_i_Cognoms", $_POST["nom_i_cognoms"], PDO::PARAM_STR, 200);
		$sth->bindParam(":NIF", $_POST["nif"], PDO::PARAM_STR, 20);
		$sth->bindParam(":Districte", $_POST["districte"], PDO::PARAM_INT, 4);
		$sth->bindParam(":Barri", $_POST["barri"], PDO::PARAM_STR, 50);
		$sth->bindParam(":Telefon_Casa", $_POST["telefon_casa"], PDO::PARAM_STR, 30);
		$sth->bindParam(":Telefon_Mobil", $_POST["telefon_mobil"], PDO::PARAM_STR, 30);
		$sth->bindParam(":Tipus_Membre_Partit", $_POST["tipus_membre_partit"], PDO::PARAM_INT);
		$sth->bindParam(":Data_Naixement", $_POST["data_naixement"], PDO::PARAM_STR);
		$sth->bindParam(":data_alta_simpatitzant", $_POST["data_alta_simpatitzant"], PDO::PARAM_STR);
		$sth->bindParam(":data_baixa_simpatitzant", $_POST["data_baixa_simpatitzant"], PDO::PARAM_STR);
		$sth->bindParam(":data_alta_membre_ple_dret", $_POST["data_alta_membre_ple_dret"], PDO::PARAM_STR);
		$sth->bindParam(":data_baixa_membre_ple_dret", $_POST["data_baixa_membre_ple_dret"], PDO::PARAM_STR);
		$sth->bindParam(":bic", $_POST["bic"], PDO::PARAM_STR, 100);
		$sth->bindParam(":sense_ingressos", $sense_ingressos_bool, PDO::PARAM_INT, 1);
		$sth->bindParam(":paga_transferencia", $paga_transferencia_bool, PDO::PARAM_INT, 1);
		$sth->bindParam(":comentaris", $_POST["comentaris"], PDO::PARAM_STR);
		$sth->bindParam(":Adresa", $_POST["adresa"], PDO::PARAM_STR, 200);
		$sth->bindParam(":Codi_Postal", $_POST["codi_postal"], PDO::PARAM_STR, 30);
		$sth->bindParam(":Ciutat", $_POST["ciutat"], PDO::PARAM_STR, 50);
		$sth->bindParam(":Provincia", $_POST["provincia"], PDO::PARAM_STR, 50);
		$sth->bindParam(":Xarxes", $_POST["xarxes"], PDO::PARAM_STR, 50);
		$sth->bindParam(":periodicitat_quota", $_POST["periodicitat_quota"], PDO::PARAM_INT);
		$sth->bindParam(":no_vol_emails", $no_vol_emails_bool, PDO::PARAM_INT, 1);
		
		$sth->execute();
	}
	
	if(isset($_POST["iban"]) && !empty($_POST["iban"])){
		$iban = iban_to_machine_format($_POST["iban"]);
			
		if(verify_iban($iban,$machine_format_only=true)){
			$sth=$con->prepare("UPDATE persones SET IBAN=:iban WHERE id=:id");
			$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
			$sth->bindParam(":iban", $iban, PDO::PARAM_STR, 100);
			$sth->execute();
		}else{
			$invalidiban = true;
		}
	}else{
		$iban = "";
		$sth=$con->prepare("UPDATE persones SET IBAN=:iban WHERE id=:id");
		$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
		$sth->bindParam(":iban", $iban, PDO::PARAM_STR, 100);
		$sth->execute();
	}
	
	if(isset($_POST["contrasenya"]) && !empty($_POST["contrasenya"]) && strlen($_POST["contrasenya"])>=6){
		$sth=$con->prepare("UPDATE persones SET contrasenya=:hashedpwd WHERE id=:id");
		$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
		$hashedpwd = phash($_POST["contrasenya"]);
		$sth->bindParam(":hashedpwd", $hashedpwd, PDO::PARAM_STR, 200);
		$sth->execute();
	}
	
	if(isset($_POST["cancelpay"])){
		$sth=$con->prepare("DELETE FROM quotes WHERE uid=:id");
		$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
		$sth->execute();
	}
	
	if(isset($_POST["createpay"]) && isset($_POST["quota"]) && $_POST["quota"]>0 && isset($_POST["iban"]) && !empty($_POST["iban"])){
		$sth=$con->prepare("INSERT INTO quotes (uid, quota, startdate) VALUES (:id, :quota, CURDATE())");
		$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
		$sth->bindParam(":quota", $_POST["quota"], PDO::PARAM_INT);
		$sth->execute();
	}
}

$sth=$con->prepare("SELECT * FROM persones WHERE id=?");
$sth->bindParam(1, intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$row = $sth->fetch(PDO::FETCH_ASSOC);
?>

<h1 class="clear">Edició de compte</h1>
<div>
    <form method="post">
		<p class="clear">
		<?php if($invalidiban){ echo '<div class="warning">⚠️ Has introduit un IBAN no vàlid! No ha sigut canviat.</div>'; } ?>
		<label>Nom de compte:
		<input type="text" name="nomusuari" value="<?=safe_escape($row["nomusuari"])?>"></label><br>
		<label>Tipus de compte:
		<select name="tipus">
			<option value="0"<?=$row["tipus"] == "0" ? " selected" : ""?>>Compte no verificada</option>
			<option value="1"<?=$row["tipus"] == "1" ? " selected" : ""?>>Administrador/a</option>
			<option value="2"<?=$row["tipus"] == "2" ? " selected" : ""?>>Compte verificada</option>
			<option value="3"<?=$row["tipus"] == "3" ? " selected" : ""?>>Compte verificada completament</option>
			<option value="4"<?=$row["tipus"] == "4" ? " selected" : ""?>>Desactivada</option>
		</select></label><br>
		<label>Contrasenya:
		<input type="password" name="contrasenya" placeholder="[encriptada]" minlength="8" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="Com a minim una minuscula, una majuscula i numeros o caracters especials"></label><br>
		<label>Email:
		<input type="email" name="email" value="<?=safe_escape($row["email"])?>"></label><br>
		<label>No rebre emails masius:
		<input type="checkbox" name="no_vol_emails"<?=$row["No_vol_emails"] == "0" ? "" : " checked"?>></label><br>
		<label>Alies Telegram:
		<input type="text" name="alies_telegram" value="<?=safe_escape($row["Alies_Telegram"])?>" pattern="^[A-Za-z0-9_-]+$" title="Només pot contenir lletres ('A-Z'), nombres ('0-9'), '-' i '_'"></label><br>
		<label>Genere:
		<select name="genere">
			<option value="0"<?=$row["Genere"] == "0" ? " selected" : ""?>>Desconegut/NC</option>
			<option value="1"<?=$row["Genere"] == "1" ? " selected" : ""?>>Dona</option>
			<option value="2"<?=$row["Genere"] == "2" ? " selected" : ""?>>Home</option>
			<option value="3"<?=$row["Genere"] == "3" ? " selected" : ""?>>Altre/Cap</option>
		</select></label><br>
		<label>Nom i cognoms:
		<input type="text" name="nom_i_cognoms" value="<?=safe_escape($row["Nom_i_Cognoms"])?>"></label><br>
		<label>NIF:
		<input type="text" name="nif" value="<?=safe_escape($row["NIF"])?>"></label><br>
		<label>Districte:
		<input type="text" name="districte" value="<?=safe_escape($row["Districte"])?>"></label><br>
		<label>Barri:
		<input type="text" name="barri" value="<?=safe_escape($row["Barri"])?>" list="barris"></label><br>
		<label>Adreça:
		<input type="text" name="adresa" value="<?=safe_escape($row["Adresa"])?>"></label><br>
		<label>Codi Postal:
		<input type="text" name="codi_postal" value="<?=safe_escape($row["Codi_Postal"])?>"></label><br>
		<label>Ciutat:
		<input type="text" name="ciutat" value="<?=safe_escape($row["Ciutat"])?>"></label><br>
		<label>Provincia:
		<input type="text" name="provincia" value="<?=safe_escape($row["Provincia"])?>"></label><br>
		
		<datalist id="barris">
		<?php
			foreach($con->query("SELECT Barri FROM persones GROUP BY Barri") as $rowbarri){
				echo "<option value=\"" . safe_escape($rowbarri["Barri"]) . "\">";
			}
		?>
		</datalist>
		
		<label>Telèfon Casa:
		<input type="text" name="telefon_casa" value="<?=safe_escape($row["Telefon_Casa"])?>"></label><br>
		<label>Telèfon Mòbil:
		<input type="text" name="telefon_mobil" value="<?=safe_escape($row["Telefon_Mobil"])?>"></label><br>
		<label>Tipus membre partit:
		<select name="tipus_membre_partit">
			<option value="0"<?=$row["Tipus_Membre_Partit"] == "0" ? " selected" : ""?>>No és</option>
			<option value="1"<?=$row["Tipus_Membre_Partit"] == "1" ? " selected" : ""?>>Simpatitzant</option>
			<option value="2"<?=$row["Tipus_Membre_Partit"] == "2" ? " selected" : ""?>>Membre Ple Dret</option>
		</select>
		</label><br>
		<label>Xarxes:
		<input type="text" name="xarxes" value="<?=safe_escape($row["Xarxes"])?>"></label>
	
		<br>
	
		<label>Data Naixement:
		<input type="date" name="data_naixement" value="<?=$row["Data_Naixement"]?>"></label><br>
		<label>Data Alta Simpatitzant:
		<input type="date" name="data_alta_simpatitzant" value="<?=$row["Data_Alta_Simpatitzant"]?>"></label><br>
		<label>Data Baixa Simpatitzant:
		<input type="date" name="data_baixa_simpatitzant" value="<?=$row["Data_Baixa_Simpatitzant"]?>"></label><br>
		<label>Data Alta Membre Ple Dret:
		<input type="date" name="data_alta_membre_ple_dret" value="<?=$row["Data_Alta_Membre_Ple_Dret"]?>"></label><br>
		<label>Data Baixa Membre Ple Dret:
		<input type="date" name="data_baixa_membre_ple_dret" value="<?=$row["Data_Baixa_Membre_Ple_Dret"]?>"></label>
		
		<br>
		
		<label>Sense Ingressos:
		<input type="checkbox" name="sense_ingressos" <?=$row["Sense_Ingressos"] == "0" ? "" : " checked"?>></label><br>
		<label>Paga Transferencia:
		<input type="checkbox" name="paga_transferencia" <?=$row["Paga_Transferencia"] == "0" ? "" : " checked"?>></label><br>
		<label>Comentaris:<br>
		<textarea name="comentaris" rows="5" cols="38"><?=safe_escape($row["Comentaris"])?></textarea>
		</label><br>
		<input type="submit" value="Desa tot">
</div>

<h2>Pagament</h2>
<p>Pots canviar les dades de pagament del usuari.</p>
<label>IBAN:
<input type="text" name="iban" value="<?=safe_escape($row["IBAN"])?>"></label><br>
<label>BIC:
<input type="text" name="bic" value="<?=safe_escape($row["BIC"])?>"></label><br>
<input type="submit" value="Desa tot">

<?php
$sth = $con->prepare("SELECT * FROM quotes WHERE uid=:id");
$sth->bindParam(":id", intval($_GET["id"]), PDO::PARAM_INT);
$sth->execute();
$row_pagaments = $sth->fetchAll(PDO::FETCH_ASSOC);
$te_pagament_actiu = false;

if(isset($row_pagaments[0])){
	$te_pagament_actiu = true;
	
	?>
	
	<p>Aquest usuari te un pagament actiu. Si vols modificar el pagament, ho tens que anular.<br>
	Quota: <input type="text" value="<?=$row_pagaments[0]["quota"]?>" readonly>€/període</br>
	<label>Periodicitat quota:
		<select name="periodicitat_quota_fake" disabled>
			<option value="12"<?=$row["Periodicitat_Quota"] == "12" ? " selected" : ""?>>Anual</option>
			<option value="3"<?=$row["Periodicitat_Quota"] == "3" ? " selected" : ""?>>Trimestral</option>
			<option value="4"<?=$row["Periodicitat_Quota"] == "4" ? " selected" : ""?>>Quatrimestral</option>
			<option value="6"<?=$row["Periodicitat_Quota"] == "6" ? " selected" : ""?>>Semestral</option>
		</select></label><br>
		<input type="hidden" name="periodicitat_quota" value="<?=$row["Periodicitat_Quota"]?>">
	<input type="submit" name="cancelpay" value="Desa tot i anula el pagament"></p>
	
	<?php
}else{
	?>
	
	<p>Aquest usuari no té cap pagament actiu. Pots crear un pagament.<br>
	Quota: <input type="text" name="quota">€/període<br>
	<label>Periodicitat quota:
		<select name="periodicitat_quota">
			<option value="12"<?=$row["Periodicitat_Quota"] == "12" ? " selected" : ""?>>Anual</option>
			<option value="3"<?=$row["Periodicitat_Quota"] == "3" ? " selected" : ""?>>Trimestral</option>
			<option value="4"<?=$row["Periodicitat_Quota"] == "4" ? " selected" : ""?>>Quatrimestral</option>
			<option value="6"<?=$row["Periodicitat_Quota"] == "6" ? " selected" : ""?>>Semestral</option>
		</select></label><br>
	<input type="submit" name="createpay" value="Desa tot i crea un nou pagament"></p>
	<p>Assegurat que tot es correcte abans de premer el botó!</p>
	
	<?php
}
?>

</form>

<h2>Altres opcions</h2>
<p>
	<ul>
		<li><a href="eliminarusuari?id=<?=$row["id"]?>">Eliminar aquest compte</a></li>
		<li><a href="gestorusuaris">Tornar endarrera: gestor d'usuaris</li>
		<li><a href="/">Pàgina principal</li>
	</ul>
</p>

<?php
require_once "internal/foot.php";
?>