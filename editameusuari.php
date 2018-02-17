<?php
require_once "internal/head.php";
require_once "internal/common.php";
require_once "internal/parcuserinc.php";

if(isset($_POST["nomusuari"]) && !empty($_POST["nomusuari"]) && validate_username($_POST["nomusuari"]) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
	if(!isset($_POST["alies_telegram"]) || empty($_POST["alies_telegram"]) || preg_match("/^[A-Za-z0-9_-]+$/", $_POST["alies_telegram"])){
		$sth=$con->prepare("UPDATE persones SET email=:email,
		Genere=:genere, Nom_i_Cognoms =:Nom_i_Cognoms, NIF=:NIF, Districte=:Districte, Barri=:Barri, Telefon_Casa=:Telefon_Casa, Telefon_Mobil=:Telefon_Mobil,
		Data_Naixement=:Data_Naixement, Alies_Telegram=:alies_telegram, canviatself=:canviatself, css=:css,
		Adresa=:Adresa, Codi_Postal=:Codi_Postal, Ciutat=:Ciutat, Provincia=:Provincia, No_vol_emails=:no_vol_emails WHERE id=:id");
		
		$canviatself = 1; // el/la usuari/a ha editat la seva compte al menys una vegada
		$no_vol_emails_bool = isset($_POST["no_vol_emails"]) ? 1 : 0;
		
		$sth->bindParam(":id", intval($_SESSION["uid"]), PDO::PARAM_INT);
		$sth->bindParam(":email", $_POST["email"], PDO::PARAM_STR, 200);
		$sth->bindParam(":alies_telegram", $_POST["alies_telegram"], PDO::PARAM_STR, 100);
		$sth->bindParam(":genere", $_POST["genere"], PDO::PARAM_INT, 4);
		$sth->bindParam(":Nom_i_Cognoms", $_POST["nom_i_cognoms"], PDO::PARAM_STR, 200);
		$sth->bindParam(":NIF", $_POST["nif"], PDO::PARAM_STR, 20);
		$sth->bindParam(":Districte", $_POST["districte"], PDO::PARAM_INT, 4);
		$sth->bindParam(":Barri", $_POST["barri"], PDO::PARAM_STR, 50);
		$sth->bindParam(":Telefon_Casa", $_POST["telefon_casa"], PDO::PARAM_STR, 30);
		$sth->bindParam(":Telefon_Mobil", $_POST["telefon_mobil"], PDO::PARAM_STR, 30);
		$sth->bindParam(":Data_Naixement", $_POST["data_naixement"], PDO::PARAM_STR);
		$sth->bindParam(":canviatself", $canviatself, PDO::PARAM_INT);
		$sth->bindParam(":css", intval($_POST["css"]), PDO::PARAM_INT, 4);
		$sth->bindParam(":Adresa", $_POST["adresa"], PDO::PARAM_STR, 200);
		$sth->bindParam(":Codi_Postal", $_POST["codi_postal"], PDO::PARAM_STR, 30);
		$sth->bindParam(":Ciutat", $_POST["ciutat"], PDO::PARAM_STR, 50);
		$sth->bindParam(":Provincia", $_POST["provincia"], PDO::PARAM_STR, 50);
		$sth->bindParam(":no_vol_emails", $no_vol_emails_bool, PDO::PARAM_INT, 1);
		
		$sth->execute();
	}
	
	if(isset($_POST["contrasenya"]) && !empty($_POST["contrasenya"]) && strlen($_POST["contrasenya"])>=6){
		$sth=$con->prepare("UPDATE persones SET contrasenya=:hashedpwd WHERE id=:id");
		$sth->bindParam(":id", intval($_SESSION["uid"]), PDO::PARAM_INT);
		$hashedpwd = phash($_POST["contrasenya"]);
		$sth->bindParam(":hashedpwd", $hashedpwd, PDO::PARAM_STR, 200);
		$sth->execute();
	}
	
	$_SESSION["canviatself"] = $canviatself;
}

$sth=$con->prepare("SELECT * FROM persones WHERE id=?");
$sth->bindParam(1, intval($_SESSION["uid"]), PDO::PARAM_INT);
$sth->execute();
$row = $sth->fetch(PDO::FETCH_ASSOC);
?>

<h1 class="clear">Edició de el meu compte</h1>
<div>
    <form method="post">
		<p class="clear">
		<label>Nom de compte:
		<input readonly type="text" name="nomusuari" value="<?=safe_escape($row["nomusuari"])?>"></label><br>
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
		<select disabled name="tipus_membre_partit">
			<option value="0"<?=$row["Tipus_Membre_Partit"] == "0" ? " selected" : ""?>>No és</option>
			<option value="1"<?=$row["Tipus_Membre_Partit"] == "1" ? " selected" : ""?>>Simpatitzant</option>
			<option value="2"<?=$row["Tipus_Membre_Partit"] == "2" ? " selected" : ""?>>Membre Ple Dret</option>
		</select>
		</label>
	
		<br>
	
		<label>Data Naixement:
		<input type="date" name="data_naixement" value="<?=safe_escape($row["Data_Naixement"])?>"></label><br>
		<label>Data Alta Simpatitzant:
		<input disabled type="date" name="data_alta_simpatitzant" value="<?=safe_escape($row["Data_Alta_Simpatitzant"])?>"></label><br>
		<label>Data Baixa Simpatitzant:
		<input disabled type="date" name="data_baixa_simpatitzant" value="<?=safe_escape($row["Data_Baixa_Simpatitzant"])?>"></label><br>
		<label>Data Alta Membre Ple Dret:
		<input disabled type="date" name="data_alta_membre_ple_dret" value="<?=safe_escape($row["Data_Alta_Membre_Ple_Dret"])?>"></label><br>
		<label>Data Baixa Membre Ple Dret:
		<input disabled type="date" name="data_baixa_membre_ple_dret" value="<?=safe_escape($row["Data_Baixa_Membre_Ple_Dret"])?>"></label>
		</label><br>
		<input type="submit" value="Enviar tot">
		
		<h2>Preferencies d'usuari/a</h2>
		<label>Tipus interfície web:
		<select name="css">
			<option value="0"<?=$row["css"] == "0" ? " selected" : ""?>>Simple</option>
			<option value="1"<?=$row["css"] == "1" ? " selected" : ""?>>Moderne</option>
		</select>
		</label><br>
		<input type="submit" value="Enviar tot">
    </form>
</div>

<h2>Altres opcions</h2>
<ul>
<li><a href="/">Endarrera: pàgina principal</a></li>
</ul>

<?php
require_once "internal/foot.php";
?>